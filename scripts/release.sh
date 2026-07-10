#!/usr/bin/env sh
set -eu

ROOT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
APP_NAME="${APP_NAME:-tenrusl-payment-webhook-sim}"
VERSION="${1:-$(git -C "$ROOT_DIR" rev-parse --short HEAD 2>/dev/null || date +%Y%m%d%H%M%S)}"
DIST_DIR="$ROOT_DIR/dist"
ZIP_PATH="$DIST_DIR/${APP_NAME}-${VERSION}.zip"
TMP_DIR="$(mktemp -d)"

cleanup() {
  rm -rf "$TMP_DIR"
}
trap cleanup EXIT INT TERM

mkdir -p "$DIST_DIR"
rm -f "$ZIP_PATH"

cd "$ROOT_DIR"

git archive --format=tar --worktree-attributes HEAD | tar -x -C "$TMP_DIR"

cd "$TMP_DIR"
zip -qr "$ZIP_PATH" .

validate_absent() {
  pattern="$1"
  if unzip -Z1 "$ZIP_PATH" | grep -E "$pattern" >/dev/null; then
    echo "Release archive contains forbidden path pattern: $pattern" >&2
    exit 1
  fi
}

validate_absent '(^|/)\.env($|/)'
validate_absent '^storage/logs/'
validate_absent '^storage/framework/cache/'
validate_absent '^storage/framework/views/'
validate_absent '^storage/framework/sessions/'
validate_absent '^storage/api-docs/'
validate_absent '^bootstrap/cache/'
validate_absent '^database/.*\.sqlite$'
validate_absent '^public/build/'
validate_absent '^vendor/'
validate_absent '^node_modules/'
validate_absent '^coverage/'

echo "Release archive created: $ZIP_PATH"
