#!/usr/bin/env bash
set -euo pipefail
if [ -f /tmp/survayapp_php_server.pid ]; then
  kill "$(cat /tmp/survayapp_php_server.pid)" 2>/dev/null || true
  rm -f /tmp/survayapp_php_server.pid
  echo "Stopped local PHP server"
else
  echo "No local PHP server PID file found"
fi
