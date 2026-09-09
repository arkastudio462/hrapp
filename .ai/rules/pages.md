---
paths:
  - 'resources/js/Pages/**'
---

# Pages

## Use AppLayout with variant for all tenant pages
All Inertia pages under resources/js/Pages (except Welcome.vue, Auth/*, SuperAdmin/Login.vue, SetupWizard) must wrap their content in the shared sidebar layout `@/layouts/AppLayout.vue` via `<AppLayout title="..." variant="...">`. variant is one of 'default' (HR admin nav), 'employee' (employee portal nav), 'superadmin' (superadmin nav). Pages must NOT define their own top header/nav anymore.
