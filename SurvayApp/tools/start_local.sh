#!/usr/bin/env bash
set -euo pipefail
ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="$ROOT_DIR/.env.local"
[ -f "$ENV_FILE" ] && set -a && source "$ENV_FILE" && set +a || true
APP_PORT="${APP_PORT:-8000}"
DOCROOT="$ROOT_DIR/till.mezoo.co.il_bm1756763301dm"
# stop existing
if [ -f /tmp/survayapp_php_server.pid ]; then
  kill "$(cat /tmp/survayapp_php_server.pid)" 2>/dev/null || true
  rm -f /tmp/survayapp_php_server.pid
fi
php -S "localhost:${APP_PORT}" -t "$DOCROOT" >/dev/null 2>&1 & echo $! > /tmp/survayapp_php_server.pid
sleep 1
URL="http://localhost:${APP_PORT}/welcome/login"
echo "Server is up: ${URL}"
