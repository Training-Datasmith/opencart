#!/usr/bin/env bash
set -uo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
PHAR=tools/phpunit-9.6.22.phar
if [ ! -f "$PHAR" ]; then
  bash tests/fetch-phpunit.sh
fi
SHA=$(cat tools/phpunit-9.6.22.phar.sha256)
export PHPUNIT_SHA256="$SHA"
PHP_BIN="${PHP_BIN:-php}"
CMD=("$PHP_BIN" -d error_reporting=-1 "$PHAR" -c phpunit.xml.dist)
run_once() {
  local label="$1"
  shift
  echo "===== $label ====="
  "${CMD[@]}" "$@"
}
run_once "default-1" --order-by=default
run_once "default-2" --order-by=default
run_once "random-1" --order-by=random --random-order-seed=20261008
run_once "random-2" --order-by=random --random-order-seed=20261008
