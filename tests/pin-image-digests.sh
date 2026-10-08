#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
COMPOSE="$ROOT/tests/docker-compose.yml"
pull_and_digest() {
  local image="$1"
  docker pull "$image"
  docker image inspect "$image" --format='{{index .RepoDigests 0}}'
}
MYSQL_DIGEST=$(pull_and_digest mysql:8.0)
PHP82_DIGEST=$(pull_and_digest php:8.2-cli)
PHP84_DIGEST=$(pull_and_digest php:8.4-cli)
echo "MYSQL=$MYSQL_DIGEST" > "$ROOT/tests/image-digests.env"
echo "PHP82=$PHP82_DIGEST" >> "$ROOT/tests/image-digests.env"
echo "PHP84=$PHP84_DIGEST" >> "$ROOT/tests/image-digests.env"
sed -i "s|image: mysql:8.0@sha256:.*|image: ${MYSQL_DIGEST}|" "$COMPOSE"
sed -i "s|FROM php:8.2-cli|FROM ${PHP82_DIGEST}|" "$ROOT/tests/Dockerfile.php82"
sed -i "s|FROM php:8.4-cli|FROM ${PHP84_DIGEST}|" "$ROOT/tests/Dockerfile.php84"
cat "$ROOT/tests/image-digests.env"
