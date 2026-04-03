# Production Readiness Checklist

Use this checklist before promoting the module to staging/production.

## 1) Security and API hygiene
- [ ] API endpoints do **not** return raw exception messages to clients.
- [ ] All exception paths log errors with context and return generic error responses.
- [ ] Request parameters are trimmed/cast before strict-typed service calls.

## 2) Schema ownership and upgrades
- [ ] `domus_delivery_schedule` ownership is declarative (`etc/db_schema.xml`), not duplicated in patch creation logic.
- [ ] `etc/db_schema_whitelist.json` is synced to current declarative schema.
- [ ] `bin/magento setup:upgrade` completes without schema conflicts in a clean DB.

## 3) Build & runtime checks
- [ ] Run `scripts/verify-production-readiness.sh`.
- [ ] Run `php bin/magento setup:di:compile` in target environment.
- [ ] Run `php bin/magento cache:flush` after deployment.

## 4) Functional smoke tests
- [ ] REST: `/domus/rest/check`, `/domus/rest/express`, `/domus/rest/autocomplete`, `/domus/rest/timeslots`.
- [ ] Frontend pages: schedule/express/timeslots render and return expected payloads.
- [ ] Admin pincode form save/edit works for country/store/group-specific rules.

## 5) Observability
- [ ] Error logs contain actionable context for REST/controller failures.
- [ ] No repetitive/flooding logs under normal traffic.
