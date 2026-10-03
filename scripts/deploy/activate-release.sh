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
shared="$app_root/shared"
release="$releases/$commit"
previous="$app_root/previous-$commit"
php_bin="/opt/alt/php85/usr/bin/php"
health_url="https://loja.malu-store.com/up"

cd "$home_dir"
sha256sum --check "$checksum_name"
mkdir -p "$releases" "$shared"

if [[ ! -f "$shared/.env" ]]; then
    cp "$current/.env" "$shared/.env"
fi

if [[ ! -d "$shared/storage" ]]; then
    cp -a "$current/storage" "$shared/storage"
fi

rm -rf "$release"
mkdir -p "$release"
tar -xzf "$archive_name" -C "$release"
rm -rf "$release/storage"
ln -s "$shared/storage" "$release/storage"
ln -s "$shared/.env" "$release/.env"
rm -f "$release/public/storage"
ln -s ../storage/app/public "$release/public/storage"

cd "$release"
"$php_bin" artisan optimize:clear
"$php_bin" artisan migrate --force
"$php_bin" artisan config:cache
"$php_bin" artisan route:cache
"$php_bin" artisan view:cache

rm -rf "$previous"
mv "$current" "$previous"
mv "$release" "$current"

rollback() {
    rm -rf "$release"
    mv "$current" "$release"
    mv "$previous" "$current"
}

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
printf 'Release %s implantada com sucesso. Rollback disponível em %s\n' "$commit" "$previous"

