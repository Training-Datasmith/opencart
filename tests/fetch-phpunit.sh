#!/usr/bin/env bash
set -euo pipefail
VERSION=9.6.22
PHAR=tools/phpunit-${VERSION}.phar
URL="https://phar.phpunit.de/phpunit-${VERSION}.phar"
# SHA-256 from https://phar.phpunit.de/phive.xml release phpunit-9.6.22.phar
EXPECTED=9618d52015c9b06b4979a8e481ca9567be6be20e711e98926c61378a400e1f2e
mkdir -p tools
curl -fsSL "$URL" -o "$PHAR"
ACTUAL=$(sha256sum "$PHAR" | awk '{print $1}')
if [ "$EXPECTED" != "$ACTUAL" ]; then
  echo "PHPUnit PHAR checksum mismatch" >&2
  echo "expected=$EXPECTED actual=$ACTUAL" >&2
  exit 1
fi
echo "$ACTUAL" > tools/phpunit-${VERSION}.phar.sha256
echo "Verified PHPUnit ${VERSION} SHA-256: ${ACTUAL}"
