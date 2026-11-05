---
paths:
  - app/Models/Tenant.php
---

# Models

## Never array-cast Tenant settings/limits
settings and limits are VirtualColumn attributes serialized into tenants.data. Declaring them as 'array' casts makes every save JSON-encode one extra layer; the value nested 21 layers deep (25MB) and json_encode exhausted 256MB on settings save. Read/write them as plain arrays via the data column's own cast. Data corrupted by this was repaired by migration 2026_11_05_135732_repair_double_encoded_tenant_settings.
