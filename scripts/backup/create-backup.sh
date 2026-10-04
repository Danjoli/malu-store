#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

app_root="${APP_ROOT:-$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)}"
backup_dir="${BACKUP_DIR:-${HOME:?HOME ausente}/backups/malu-store}"
mysql_config="${BACKUP_MYSQL_CONFIG:-${HOME}/.malu-store-backup.cnf}"
database_file="${BACKUP_DATABASE_FILE:-${HOME}/.malu-store-backup-database}"
key_file="${BACKUP_ENCRYPTION_KEY_FILE:-${HOME}/.malu-store-backup.key}"
retention="${BACKUP_RETENTION_COUNT:-14}"

[[ "$retention" =~ ^[1-9][0-9]*$ ]] || { printf 'Retenção inválida.\n' >&2; exit 1; }
test -r "$mysql_config"
test -r "$database_file"
test -r "$key_file"
test -d "$app_root/storage/app/public"
database="$(tr -d '\r\n' < "$database_file")"
[[ "$database" =~ ^[A-Za-z0-9_]+$ ]] || { printf 'Nome de banco inválido.\n' >&2; exit 1; }

mkdir -p "$backup_dir"
stage="$(mktemp -d "$backup_dir/.staging-XXXXXXXX")"
cleanup() { rm -rf -- "$stage"; }
trap cleanup EXIT

timestamp="$(date -u +'%Y%m%dT%H%M%SZ')"
name="malu-store-$timestamp"
plain="$stage/$name.tar.gz"
encrypted="$stage/$name.tar.gz.enc"

mysqldump \
    --defaults-extra-file="$mysql_config" \
    --single-transaction \
    --quick \
    --skip-lock-tables \
    --no-tablespaces \
    --default-character-set=utf8mb4 \
    "$database" \
    > "$stage/database.sql"

release="unknown"
if [[ -f "$app_root/RELEASE_COMMIT" ]]; then
    release="$(tr -d '\r\n' < "$app_root/RELEASE_COMMIT")"
fi
printf 'created_at=%s\nrelease=%s\n' "$timestamp" "$release" > "$stage/manifest.txt"

tar -czf "$plain" \
    -C "$stage" database.sql manifest.txt \
    -C "$app_root/storage/app" public

openssl enc -aes-256-cbc -salt -pbkdf2 -iter 200000 \
    -in "$plain" \
    -out "$encrypted" \
    -pass "file:$key_file"

(cd "$stage" && sha256sum "$name.tar.gz.enc" > "$name.sha256")
mv -- "$encrypted" "$backup_dir/"
mv -- "$stage/$name.sha256" "$backup_dir/"

find "$backup_dir" -maxdepth 1 -type f -name 'malu-store-*.tar.gz.enc' -printf '%T@ %p\n' \
    | sort -rn \
    | cut -d' ' -f2- \
    > "$stage/archives"
mapfile -t archives < "$stage/archives"
if (( ${#archives[@]} > retention )); then
    for old_archive in "${archives[@]:retention}"; do
        [[ "$old_archive" == "$backup_dir"/malu-store-*.tar.gz.enc ]] || exit 1
        rm -f -- "$old_archive" "${old_archive%.tar.gz.enc}.sha256"
    done
fi

printf 'Backup criado: %s/%s.tar.gz.enc\n' "$backup_dir" "$name"
