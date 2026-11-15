#!/usr/bin/env bash
#
# Build an installable plugin ZIP from the files that .distignore keeps.
# The same file list ships to WordPress.org via 10up/action-wordpress-plugin-deploy.

set -euo pipefail

cd "$(dirname "$0")/.."

SLUG="smntcs-admin-bar-toggle"
VERSION="$(sed -n 's/^[ *]*Version:[[:space:]]*//p' "$SLUG.php" | tr -d '[:space:]')"
ZIP="$SLUG-$VERSION.zip"

if [ -z "$VERSION" ]; then
	echo "Could not read the version from $SLUG.php" >&2
	exit 1
fi
BUILD_DIR="$(mktemp -d)"
trap 'rm -rf "$BUILD_DIR"' EXIT

# .distignore entries are anchored with a leading slash; rsync wants them relative.
rsync -a \
	--exclude-from=<(sed -e 's#^/##' -e '/^$/d' .distignore) \
	--exclude='.DS_Store' \
	--exclude='*.cache' \
	--exclude='/assets' \
	--exclude='/*.zip' \
	./ "$BUILD_DIR/$SLUG/"

rm -f "$ZIP"
(cd "$BUILD_DIR" && zip -qr - "$SLUG") > "$ZIP"

echo "Created $ZIP:"
unzip -Z1 "$ZIP"
