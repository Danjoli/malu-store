#!/usr/bin/env bash
set -Eeuo pipefail

archive_name="${1:?arquivo da release ausente}"
checksum_name="${2:?checksum ausente}"
commit="${3:?commit ausente}"
if [[ ! "$commit" =~ ^[0-9a-f]{40}$ ]]; then
    printf 'Commit inválido para implantação.\n' >&2
    exit 1
fi

home_dir="${HOME:?HOME ausente}"
app_root="$home_dir/domains/malu-store.com"
current="$app_root/public_html"
releases="$app_root/releases"
release="$releases/$commit"
previous="$app_root/previous-$commit"
php_bin="/opt/alt/php85/usr/bin/php"
health_url="https://malu-store.com/up"

cd "$home_dir"
sha256sum --check "$checksum_name"
mkdir -p "$releases"

# A implantação atual já preservará a versão ativa em $previous. Cópias de
# rollbacks mais antigos e releases incompletas apenas consomem cota/inodes.
find "$app_root" -mindepth 1 -maxdepth 1 -type d -name 'previous-*' -exec rm -rf -- {} +
find "$releases" -mindepth 1 -maxdepth 1 -type d -exec rm -rf -- {} +

rm -rf "$release"
mkdir -p "$release"
tar -xzf "$archive_name" -C "$release"
cp "$current/.env" "$release/.env"
sed -i 's|^APP_URL=.*$|APP_URL=https://malu-store.com|' "$release/.env"
rm -rf "$release/storage"
cp -a "$current/storage" "$release/storage"
rm -f "$release/public/storage"
ln -s ../storage/app/public "$release/public/storage"

cd "$release"
"$php_bin" artisan optimize:clear
"$php_bin" artisan migrate --force

rm -rf "$previous"
mkdir -p "$previous"

move_contents() {
    local source="$1"
    local destination="$2"
    local entries=()

    shopt -s dotglob nullglob
    entries=("$source"/*)
    if (( ${#entries[@]} > 0 )); then
        mv -- "${entries[@]}" "$destination/"
    fi
    shopt -u dotglob nullglob
}

# Hostinger associates the document root with the public_html directory itself.
# Keep that directory (and its inode/ACLs) in place and only replace its contents.
move_contents "$current" "$previous"
move_contents "$release" "$current"

rollback() {
    local failed="$releases/$commit-failed"

    rm -rf "$failed"
    mkdir -p "$failed"
    move_contents "$current" "$failed"
    move_contents "$previous" "$current"
}

# Laravel caches contain absolute paths. Build them only after the release is in
# its final location, otherwise PHP-FPM writes to the now-empty staging folder.
if ! (
    cd "$current" &&
    "$php_bin" artisan config:cache &&
    "$php_bin" artisan route:cache &&
    "$php_bin" artisan view:cache &&
    "$php_bin" artisan sitemap:generate
); then
    rollback
    exit 1
fi

healthy=false
for attempt in {1..10}; do
    if curl --fail --silent --max-time 10 "$health_url" >/dev/null; then
        healthy=true
        break
    fi

    printf 'Health check ainda indisponível (tentativa %s/10).\n' "$attempt"
    sleep 3
done

if [[ "$healthy" != true ]]; then
    rollback
    exit 1
fi

deployed_commit="$(tr -d '\r\n' < "$current/RELEASE_COMMIT")"
if [[ "$deployed_commit" != "$commit" ]]; then
    rollback
    exit 1
fi

cd "$current"
"$php_bin" artisan queue:restart
rm -f "$home_dir/$archive_name" "$home_dir/$checksum_name"
printf 'Release %s implantada com sucesso. Rollback disponível em %s\n' "$commit" "$previous"

