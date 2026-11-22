#!/usr/bin/env bash
# Lab 3 Modul 3: Puan Siti cuba membayar melalui portal (Sanctum SPA).
# Log masuk dengan cookie + CSRF, kemudian POST /api/v1/payments.
#   bash lab/spa-pay.sh            (pelayan: php artisan serve)
#   BASE=http://127.0.0.1:8000 INVOICE=7 bash lab/spa-pay.sh
set -euo pipefail

BASE=${BASE:-http://127.0.0.1:8000}
INVOICE=${INVOICE:-7}
JAR=$(mktemp)
trap 'rm -f "$JAR"' EXIT

xsrf() { grep XSRF-TOKEN "$JAR" | awk '{print $7}' | sed 's/%3D/=/g'; }

curl -s -c "$JAR" -b "$JAR" -H "Referer: $BASE" "$BASE/sanctum/csrf-cookie" > /dev/null

curl -s -c "$JAR" -b "$JAR" -H "Accept: application/json" -H "Referer: $BASE" \
     -H "X-XSRF-TOKEN: $(xsrf)" \
     -d email=siti@utm.my -d password=rahsia123 "$BASE/login"
echo

curl -s -o /dev/stderr -w "%{http_code}\n" -c "$JAR" -b "$JAR" \
     -H "Accept: application/json" -H "Referer: $BASE" -H "X-XSRF-TOKEN: $(xsrf)" \
     -d invoice_id="$INVOICE" -d method=fpx "$BASE/api/v1/payments"
