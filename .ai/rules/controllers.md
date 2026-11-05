---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Link users to employees via users.employee_id, not employees.user_id
The `employees` table has NO `user_id` column; the link is `users.employee_id`. Look up with `Employee::where('id', $request->user()->employee_id)->first()` (see EmployeePortalController). On SQLite DQS, `where('user_id', ...)` on a missing column silently returns null instead of erroring — this caused broken pages (face attendance, leave create) with "Data karyawan tidak ditemukan".
