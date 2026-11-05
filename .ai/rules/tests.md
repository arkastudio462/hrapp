---
paths:
  - 'tests/**'
---

# Tests

## Tests must initialize tenancy and create tenant_id columns
TenantTestCase::initializeTenancy() must call tenancy()->initialize($tenant) after creating the tenant: models using BelongsToTenant only fill tenant_id (and apply TenantScope) while tenancy is initialized, so factory rows would otherwise have NULL tenant_id and be filtered out during requests. createTenantTables() must also add tenant_id to all 11 tenant tables (mirrors migration add_tenant_id_to_tenant_tables) or HandleInertiaRequests/PayrollController queries fail with 'no such column'.
