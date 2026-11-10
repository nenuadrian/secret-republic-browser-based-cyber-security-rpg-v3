#!/usr/bin/env bash
# Package a standalone release from the current checkout.
#
#   composer install --no-dev --optimize-autoloader
#   scripts/build-release.sh v2026.11
#
# Produces dist/secretrepublic-v3-<version>.{tar.gz,zip} and dist/SHA256SUMS.txt.
set -euo pipefail

VERSION="${1:?usage: scripts/build-release.sh <version>}"
NAME="secretrepublic-v3-$VERSION"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/dist"
STAGE="$(mktemp -d)"
trap 'rm -rf "$STAGE"' EXIT

cd "$ROOT"
[ -f includes/vendor/autoload.php ] || { echo "includes/vendor missing - run composer install --no-dev first" >&2; exit 1; }
[ ! -d includes/vendor/phpunit ] || { echo "dev dependencies present - run composer install --no-dev first" >&2; exit 1; }

mkdir -p "$STAGE/$NAME"
tar cf - \
	--exclude=./.git --exclude=./.github --exclude=./dist --exclude=./docs --exclude=./screens \
	--exclude=./tests --exclude=./scripts --exclude=./test.sh --exclude=./node_modules \
	--exclude=./.env --exclude=./.env.example --exclude=./.phpunit.cache \
	--exclude=./includes/database_info.php --exclude=./includes/local_settings.php \
	--exclude='./includes/*.sqlite' --exclude='./includes/cache/*' --exclude='./includes/templates_c/*' \
	--exclude='.DS_Store' --exclude='*~' --exclude='*.un~' \
	. | tar xf - -C "$STAGE/$NAME"

mkdir -p "$STAGE/$NAME/includes/cache" "$STAGE/$NAME/includes/templates_c"

mkdir -p "$OUT"
rm -f "$OUT/$NAME.tar.gz" "$OUT/$NAME.zip"
(cd "$STAGE" && tar czf "$OUT/$NAME.tar.gz" "$NAME" && zip -qr "$OUT/$NAME.zip" "$NAME")
(cd "$OUT" && { command -v sha256sum >/dev/null && sha256sum "$NAME.tar.gz" "$NAME.zip" || shasum -a 256 "$NAME.tar.gz" "$NAME.zip"; } > SHA256SUMS.txt)

ls -lh "$OUT"
