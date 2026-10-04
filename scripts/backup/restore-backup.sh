#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

archive="${1:?informe o arquivo .tar.gz.enc}"
target_database="${2:?informe o banco de destino}"
mysql_config="${BACKUP_MYSQL_CONFIG:-${HOME:?HOME ausente}/.malu-store-backup.cnf}"
key_file="${BACKUP_ENCRYPTION_KEY_FILE:-${HOME}/.malu-store-backup.key}"
uploads_destination="${RESTORE_UPLOADS_DESTINATION:-}"

[[ "$target_database" =~ ^[A-Za-z0-9_]+$ ]] || { printf 'Nome de banco inválido.\n' >&2; exit 1; }
test -f "$archive"
test -f "${archive%.tar.gz.enc}.sha256"
test -r "$mysql_config"
test -r "$key_file"

(cd "$(dirname "$archive")" && sha256sum --check "$(basename "${archive%.tar.gz.enc}.sha256")")

stage="$(mktemp -d "${TMPDIR:-/tmp}/malu-store-restore-XXXXXXXX")"
cleanup() { rm -rf -- "$stage"; }
trap cleanup EXIT

openssl enc -d -aes-256-cbc -pbkdf2 -iter 200000 \
    -in "$archive" \
    -out "$stage/backup.tar.gz" \
    -pass "file:$key_file"
tar -xzf "$stage/backup.tar.gz" -C "$stage"
test -s "$stage/database.sql"
test -f "$stage/manifest.txt"
test -d "$stage/public"

mysql --defaults-extra-file="$mysql_config" "$target_database" < "$stage/database.sql"

if [[ -n "$uploads_destination" ]]; then
    mkdir -p "$uploads_destination"
    cp -a "$stage/public/." "$uploads_destination/"
fi

printf 'Restauração validada no banco %s.\n' "$target_database"
