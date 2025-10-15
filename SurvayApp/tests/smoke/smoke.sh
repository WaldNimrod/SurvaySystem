#!/usr/bin/env bash
set -euo pipefail
: "${BASE_URL:?Usage: BASE_URL=https://host/path tests/smoke/smoke.sh}"

echo '== Smoke: GET /welcome/index with minimal params =='
TS=$(date +%s)
curl -sS -G "$BASE_URL/welcome/index" \
  --data-urlencode PD_IDNumber=$TS \
  --data-urlencode CompanyId=1 \
  --data-urlencode divisionId=1 \
  --data-urlencode SurveyId=1 \
  --data-urlencode ResponseId=SMOKE-$TS | head -c 400 || true; echo

echo '== Smoke: export CSV (headers only due to filters) =='
curl -sS -G -D - "$BASE_URL/welcome/export" \
  --data-urlencode daterange=01.01.2020%20-%2001.02.2020 | sed -n '1,20p' || true

echo '== Smoke: admin list page (after login bypass) =='
curl -sS -c /tmp/smoke_cookies.txt "$BASE_URL/welcome/login" -o /dev/null
curl -sS -b /tmp/smoke_cookies.txt -c /tmp/smoke_cookies.txt -X POST "$BASE_URL/welcome/login" -o /dev/null
curl -sS "$BASE_URL/welcome/admin?perPage=10&page=1" | sed -n '1,40p' || true

echo '== Smoke: pick first feedback and run recalc ==' 
ADMIN_HTML=$(curl -sS -b /tmp/smoke_cookies.txt -c /tmp/smoke_cookies.txt "$BASE_URL/welcome/admin?perPage=1&page=1") || true
FID=$(echo "$ADMIN_HTML" | grep -oE 'welcome/generate/[0-9]+' | head -n1 | sed 's@.*\/@@' || true)
if [[ -n "${FID:-}" ]]; then
  curl -sS "$BASE_URL/welcome/recalc/$FID" | head -c 400 || true; echo
else
  echo 'No feedback ID found'
fi

echo '== Smoke: generate report and verify ModernReport exists =='
if [[ -n "${FID:-}" ]]; then
  # Follow redirects (dev mode saves HTML and redirects to tmpp/reports/...)
  HTML=$(curl -sSL "$BASE_URL/welcome/generate/$FID?debug=1") || true
  if echo "$HTML" | grep -q 'id="ModernReport"'; then
    echo 'PASS: ModernReport container found'
  else
    echo 'FAIL: ModernReport container not found'
    echo "$HTML" | sed -n '1,80p' || true
  fi
else
  echo 'Skip generate: no FID'
fi
