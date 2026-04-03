#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "==> Production readiness verification (lightweight)"

run_check() {
  local label="$1"
  shift
  echo "-- $label"
  "$@"
}

run_check "Lint REST controllers" php -l Controller/Rest/Check.php
run_check "Lint REST controllers" php -l Controller/Rest/Express.php
run_check "Lint REST controllers" php -l Controller/Rest/Autocomplete.php
run_check "Lint REST controllers" php -l Controller/Rest/TimeSlots.php
run_check "Lint schedule patch" php -l Setup/Patch/Schema/AddDeliverySlots.php

run_check "Validate declarative schema XML" \
  python -c "import xml.etree.ElementTree as ET; ET.parse('etc/db_schema.xml'); print('db_schema.xml OK')"

if [[ -x "bin/magento" ]]; then
  echo "-- Optional Magento checks"
  php bin/magento setup:db:status || true
  php bin/magento setup:di:compile || true
else
  echo "-- Optional Magento checks skipped (bin/magento not executable in this environment)"
fi

echo "==> Verification script completed"
