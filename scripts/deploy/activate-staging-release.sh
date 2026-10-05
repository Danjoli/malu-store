#!/usr/bin/env bash
set -Eeuo pipefail

archive_name="${1:?arquivo da release ausente}"
checksum_name="${2:?checksum ausente}"
commit="${3:?commit ausente}"
app_root="${4:?diretório da homologação ausente}"
health_url="${5:?URL de saúde ausente}"

if [[ ! "$commit" =~ ^[0-9a-f]{40}$ ]]; then
    printf 'Commit inválido para implantação.\n' >&2
    exit 1
fi

home_dir="${HOME:?HOME ausente}"
case "$app_root" in
    "$home_dir"/domains/*) ;;
    *)
        printf 'O diretório da homologação deve estar dentro de %s/domains/.\n' "$home_dir" >&2
        exit 1
        ;;
esac

if [[ ! "$health_url" =~ ^https://staging\.malu-store\.com(/|$) ]]; then
    printf 'A URL de saúde precisa pertencer a staging.malu-store.com.\n' >&2
    exit 1
fi

current="$app_root/public_html"
releases="$app_root/releases"
release="$releases/$commit"
previous="$app_root/previous-$commit"
php_bin="/opt/alt/php85/usr/bin/php"

if [[ ! -f "$current/.env" ]]; then
    printf 'Crie %s/.env antes do primeiro deploy.\n' "$current" >&2
    exit 1
fi

env_value() {
    local key="$1"
    sed -n "s/^${key}=//p" "$current/.env" | tail -n 1 | sed -e 's/^"//' -e 's/"$//'
}

require_value() {
    local key="$1"
    local expected="$2"
    local actual
    actual="$(env_value "$key")"
    if [[ "$actual" != "$expected" ]]; then
        printf '%s deve ser %s na homologação.\n' "$key" "$expected" >&2
        exit 1
    fi
}

require_contains() {
    local key="$1"
    local fragment="$2"
    local actual
    actual="$(env_value "$key")"
    if [[ "$actual" != *"$fragment"* ]]; then
        printf '%s deve conter %s na homologação.\n' "$key" "$fragment" >&2
        exit 1
    fi
}

require_value APP_ENV staging
require_value APP_DEBUG false
require_value ASAAS_ENV sandbox
require_value MELHOR_ENVIO_ENV sandbox
require_value CACHE_STORE redis
require_value SESSION_DRIVER redis
require_value QUEUE_CONNECTION redis
require_value FILESYSTEM_DISK s3
require_value SENTRY_ENVIRONMENT staging
require_contains APP_URL staging.malu-store.com
require_contains DB_DATABASE staging
require_contains REDIS_PREFIX staging
require_contains AWS_BUCKET staging

mailer="$(env_value MAIL_MAILER)"
if [[ "$mailer" != log && "$mailer" != array ]]; then
    printf 'MAIL_MAILER deve ser log ou array na homologação.\n' >&2
    exit 1
fi

if [[ -z "$(env_value STAGING_BASIC_AUTH_USERNAME)" || -z "$(env_value STAGING_BASIC_AUTH_PASSWORD)" ]]; then
    printf 'Configure usuário e senha exclusivos da homologação.\n' >&2
    exit 1
fi

cd "$home_dir"
sha256sum --check "$checksum_name"
mkdir -p "$releases"

rm -rf "$release"
mkdir -p "$release"
tar -xzf "$archive_name" -C "$release"
cp "$current/.env" "$release/.env"

if [[ -d "$current/storage" ]]; then
    rm -rf "$release/storage"
    cp -a "$current/storage" "$release/storage"
fi

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

move_contents "$current" "$previous"
move_contents "$release" "$current"

rollback() {
    local failed="$releases/$commit-failed"

    rm -rf "$failed"
    mkdir -p "$failed"
    move_contents "$current" "$failed"
    move_contents "$previous" "$current"
}

if ! (
    cd "$current" &&
    "$php_bin" artisan config:cache &&
    "$php_bin" artisan route:cache &&
    "$php_bin" artisan view:cache
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

    printf 'Health check da homologação indisponível (tentativa %s/10).\n' "$attempt"
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
printf 'Homologação %s implantada com sucesso. Rollback disponível em %s\n' "$commit" "$previous"
