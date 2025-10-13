#!/usr/bin/env bash
set -euo pipefail
: "${BASE_URL:?Usage: BASE_URL=https://host/path tests/smoke/smoke.sh}"

echo '== Smoke: GET /welcome/index with minimal params =='
curl -sS -G "$BASE_URL/welcome/index" \
  --data-urlencode PD_IDNumber=123456789 \
  --data-urlencode CompanyId=1 \
  --data-urlencode divisionId=1 \
  --data-urlencode SurveyId=1 \
  --data-urlencode ResponseId=1 | head -c 400 && echo

echo '== Smoke: export CSV (headers only due to filters) =='
curl -sS -G -D - "$BASE_URL/welcome/export" \
  --data-urlencode daterange=01.01.2020%20-%2001.02.2020 | head -n 20
