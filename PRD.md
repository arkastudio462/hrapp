# Product Requirements Document (PRD)
## HR Management System - SaaS Platform

---

## 1. Overview

### 1.1 Product Name
**HRHub** - Sistem Manajemen Sumber Daya Manusia (SaaS)

### 1.2 Purpose
Platform SaaS untuk mengelola proses HR perusahaan meliputi absensi, penggajian, dan manajemen karyawan. Digunakan oleh banyak perusahaan (tenant) dalam satu instance aplikasi dengan data isolation yang kuat.

### 1.3 Business Model
- **Multi-tenant architecture** - Satu aplikasi melayani banyak perusahaan
- **Subscription-based** - Perusahaan berlangganan paket tertentu
- **Tenant isolation** - Data perusahaan terpisah satu sama lain

### 1.4 Technology Stack
- **Backend**: Laravel 10+
- **Frontend**: Vue 3 + Vite
- **Database**: MySQL 8.0+ (shared database approach)
- **Cache**: Redis
- **Queue**: Laravel Horizon (Redis-based)
- **File Storage**: S3-compatible (AWS S3 / DigitalOcean Spaces)
- **Authentication**: Laravel Sanctum
- **Face Detection**: face-api.js / MediaPipe
- **QR Code**: Laravel QR Code package + HTML5 QR Scanner
- **Payment Gateway**: Midtrans / Xendit (Indonesia)

---

## 2. Multi-Tenancy Architecture

### 2.1 Tenancy Approach
**Shared Database, Shared Schema with tenant_id**

Semua data tenant disimpan dalam database yang sama dengan `tenant_id` sebagai foreign key di setiap tabel. Laravel package yang digunakan: **stancl/tenancy** atau custom implementation.

### 2.2 Tenant Identification
- **Subdomain**: `company-name.hrhub.id`
- **Header**: `X-Tenant-ID` untuk API requests
- **Session**: Tenant context disimpan dalam session

### 2.3 Tenant Isolation
```
Request Flow:
1. User akses company-name.hrhub.id
2. Middleware resolve tenant dari subdomain
3. Set tenant context (set database prefix/session)
4. Semua query otomatis filter by tenant_id
5. Response dikembalikan dengan data tenant tersebut saja
```

---

## 3. User Roles

### 3.1 Super Admin (Platform Owner)
- Mengelola semua tenant
- Manage subscription & billing
- Monitor system health
- Platform settings

### 3.2 Tenant Admin (Company Admin)
- Akses penuh ke fitur HR perusahaan mereka
- Manage users dalam tenant
- Company settings

### 3.3 HR Staff
- Mengelola karyawan, absensi, penggajian
- Approval workflows

### 3.4 Employee
- Melihat data pribadi, absensi, payslip
- Submit izin/cuti

---

## 4. Fitur Utama

### 4.1 Super Admin Panel

#### Tenant Management
- **List tenants** dengan status (active/suspended/trial)
- **Create tenant**: 
  - Company name
  - Subdomain (auto-generate, customizable)
  - Admin email
  - Package selection
- **Tenant detail**:
  - Usage statistics (karyawan, storage)
  - Subscription status
  - Activity logs
- **Actions**: Suspend, terminate, change package

#### Subscription & Billing
- **Subscription Plans** (see section 4.10)
- **Invoice management**
- **Payment history**
- **Usage metering**

#### System Monitor
- Total tenants, users, karyawan
- System resource usage
- Error logs
- Queue monitor

---

### 4.2 Authentication & Authorization

#### Login
- Email + password
- Remember me
- Forgot password (email reset)
- Login audit trail

#### Role-Based Access Control (RBAC)
| Role | Scope | Permissions |
|------|-------|-------------|
| Super Admin | Platform-wide | All tenants |
| Tenant Admin | Own tenant | All modules |
| HR Staff | Own tenant | Employees, Attendance, Payroll |
| Employee | Own tenant | Self data only |

#### Tenant Middleware
- Auto-filter all queries by `tenant_id`
- Prevent cross-tenant data access
- Rate limiting per tenant

---

### 4.3 Super Admin Dashboard

- **Key Metrics**:
  - Total active tenants
  - Total users platform-wide
  - Monthly recurring revenue (MRR)
  - Churn rate
- **Tenant Activity**: Real-time activity feed
- **Revenue Chart**: Monthly revenue trend
- **System Health**: Server status, queue, cache

---

### 4.4 Tenant Onboarding

#### Registration Flow
```
1. Company visit landing page
2. Click "Daftar Sekarang"
3. Fill form:
   - Company name
   - Subdomain (auto-check availability)
   - Admin name & email
   - Phone number
   - Package selection
4. Email verification
5. Auto-create tenant + admin user
6. Redirect to setup wizard
```

#### Setup Wizard (Tenant Admin)
```
Step 1: Company Profile
- Logo
- Address
- Phone
- NPWP (opsional)

Step 2: Work Settings
- Jam kerja (jam masuk/pulang)
- Toleransi keterlambatan
- Geofence location

Step 3: Departments & Positions
- Import dari Excel atau buat manual

Step 4: Add Employees
- Import bulk dari Excel
- Or add manual

Step 5: Setup Payroll
- Komponen gaji default
- Potongan default
```

---

### 4.5 Tenant Dashboard

#### Admin Dashboard
- Total karyawan aktif
- Ringkasan absensi hari ini (hadir, izin, sakit, alpa)
- Grafik turnover rate
- Penggajian bulan ini
- Notifications/alerts penting
- Sisa kuota karyawan (berdasarkan paket)

#### HR Dashboard
- Karyawan yang perlu approval (izin, cuti)
- Rekap absensi mingguan/bulanan
- Upcoming gajian

#### Employee Dashboard
- Status absensi hari ini
- Ringkasan cuti tersisa
- Payslip terakhir
- Notifications

---

### 4.6 Manajemen Karyawan

#### Data Karyawan
- **Fields**:
  - NIK (Nomor Induk Karyawan) - auto generate per tenant
  - Foto profil
  - Nama lengkap
  - Email kantor & personal
  - No. telepon
  - Tempat, tanggal lahir
  - Jenis kelamin
  - Alamat
  - Posisi/Jabatan
  - Departemen
  - Tanggal masuk
  - Status (Kontrak/Permanent/Percobaan)
  - Status aktif/tidak

#### Fitur
- CRUD (Create, Read, Update, Delete)
- Search & filter
- Export ke Excel/PDF
- Import dari Excel (bulk upload)
- Upload foto profil
- **Bulk actions**: deactivate, delete, export selected

#### Organizational Structure
- Tree view struktur organisasi
- Mapping reporting line (atasan)

---

### 4.7 Manajemen Departemen & Jabatan

#### Departemen
- CRUD Departemen
- Head of Department
- Parent department (multi-level)
- Budget allocation (optional)

#### Jabatan
- CRUD Jabatan
- Level/jenjang
- Salary grade

---

### 4.8 Sistem Absensi

#### Opsi 1: Face Detection + Lokasi

**Face Detection Setup**
- Employee register wajah (multi-angle photos, minimal 3-5 photos)
- Face recognition menggunakan face-api.js
- Accuracy threshold: 90%+
- **Storage**: Face data disimpan per tenant (isolated)

**Location Verification**
- Mandatory: Izinkan akses lokasi browser
- Geofence radius: 100-200 meter dari kantor (configurable per tenant)
- Validasi: Wajah cocok + lokasi dalam radius = Absensi berhasil

**Flow Absensi**
```
1. Buka halaman absensi
2. Aktifkan kamera (selfie mode)
3. Sistem deteksi wajah secara real-time
4. Validasi wajah terhadap database tenant
5. Ambil koordinat GPS
6. Cek apakah dalam geofence tenant
7. Jika valid → Tampilkan "Absensi Berhasil"
8. Simpan: timestamp, foto selfie, koordinat GPS, IP address
```

**Absensi Masuk**
- Waktu: 07:00 - 09:00 (configurable per tenant)
- Status: Hadir / Terlambat
- Toleransi keterlambatan: 15 menit (configurable)

**Absensi Pulang**
- Waktu: 17:00 - 21:00 (configurable)
- Status: Pulang Normal / Lembur

#### Opsi 2: QR Code Scan

**QR Code Generation**
- QR Code unik per hari per tenant (auto-refresh setiap 24 jam)
- QR ditampilkan di layar TV/monitor kantor
- Bisa dicetak untuk dipasang di lokasi
- **Tenant-specific**: QR hanya valid untuk tenant tersebut

**Scan QR**
- Employee buka aplikasi → Pilih mode QR
- Scan QR Code yang ditampilkan
- Validasi: QR belum expired + belum dipakai + benar tenant
- Catat waktu + lokasi (optional)

**Anti-Fraud Measures**
- QR expire dalam 5 menit setelah ditampilkan
- Setiap QR hanya bisa dipakai 1x per orang per hari
- Log IP address dan device info
- Maximum scan radius (jika menggunakan lokasi)
- **Tenant validation**: QR harus dari tenant yang sama

#### Rekap Absensi
- Kalender view
- Detail: waktu masuk, waktu pulang, durasi kerja
- Status warna: Hijau (Hadir), Kuning (Terlambat), Merah (Alpa), Biru (Izin/Sakit)
- Export rekap absensi

---

### 4.9 Izin & Cuti

#### Pengajuan Izin/Cuti
- **Fields**:
  - Tipe: Izin / Cuti / Sakit
  - Tanggal mulai & selesai
  - Alasan/keterangan
  - Lampiran (opsional): foto surat dokumen, dll

#### Approval Flow
```
Employee Submit → HR Review → Approve/Reject
```
- Configurable approval chain per tenant
- Multi-level approval (optional)

#### Cuti Management
- **Configurable per tenant**:
  - Cuti tahunan: X hari/tahun
  - Cuti sakit: sesuai ketentuan
  - Cuti melahirkan: sesuai ketentuan
- Sisa cuti otomatis berkurang
- Cuti tidak terpakai (carry over - configurable)

#### Notifikasi
- Email notification saat pengajuan
- Email notification saat approve/reject
- In-app notification
- WhatsApp notification (optional - via API)

---

### 4.10 Penggajian (Payroll)

#### Setup Payroll Components (per tenant)

**Earnings (Pemasukan)**
| Code | Name | Type |
|------|------|------|
| BASIC | Gaji Pokok | Fixed |
| THR | Tunjangan Hari Raya | Fixed |
| TRANSPORT | Tunjangan Transportasi | Fixed |
| MEAL | Tunjangan Makan | Fixed |
| OVERTIME | Uang Lembur | Variable |
| BONUS | Bonus | Variable |

**Deductions (Potongan)**
| Code | Name | Type |
|------|------|------|
| BPJS_K | BPJS Kesehatan | Percentage (1%) |
| BPJS_J | BPJS Ketenagakerjaan | Percentage (0.5%) |
| PPH21 | Pajak Penghasilan | Progressive |
| ABSENT | Potongan Absensi | Variable |
| LATE | Potongan Keterlambatan | Variable |

#### Payroll Process Flow
```
1. HR buka periode gajian (monthly)
2. System kalkulasi otomatis:
   - Gaji pokok
   - Tunjangan tetap
   - Lembur (dari data absensi)
   - Potongan hadir (alfa, izin tanpa approval)
   - BPJS (karyawan)
   - PPh21 (jika applicable)
3. HR review hasil kalkulasi
4. HR approve/reject item tertentu
5. Generate payslip
6. Proses pembayaran (manual/by system)
```

#### Payslip
- **Tampilan per bulan**
- **Detail**:
  - Info karyawan & perusahaan
  - Periode gajian
  - Earnings breakdown
  - Deductions breakdown
  - Net salary
- **Download PDF** dengan branding perusahaan
- **Riwayat payslip** (semua bulan)

#### Payroll Report
- Salary report per departemen
- BPJS report
- PPh21 report
- Export ke Excel/PDF

---

### 4.11 Subscription & Billing (SaaS)

#### Subscription Plans

| Plan | Price | Karyawan | Storage | Features |
|------|-------|----------|---------|----------|
| **Free Trial** | Gratis (14 hari) | 10 | 1 GB | Basic features |
| **Starter** | Rp 199K/bulan | 25 | 5 GB | Attendance, Payroll |
| **Professional** | Rp 499K/bulan | 100 | 20 GB | + Face Detection, Reports |
| **Enterprise** | Rp 999K/bulan | Unlimited | 100 GB | + API Access, Custom |

#### Billing Features
- **Auto-renewal** (monthly/yearly)
- **Invoice generation** (auto PDF)
- **Payment methods**: Bank transfer, Credit Card, E-wallet
- **Midtrans/Xendit integration**
- **Usage-based billing** (optional add-ons)

#### Subscription Management
- Tenant admin can:
  - View current plan
  - Upgrade/downgrade
  - View billing history
  - Update payment method
  - Cancel subscription

#### Overage Handling
- Ketika limit terlampaui:
  - Warning notification
  - Grace period 7 hari
  - Suspend fitur tertentu jika tidak upgrade

---

### 4.12 Reports & Analytics

#### Attendance Report
- Daily/Weekly/Monthly summary
- Export Excel/PDF
- Filter: departemen, tanggal, status

#### Employee Report
- List karyawan aktif
- Karyawan baru/bulan
- Karyawan keluar/bulan
- Demographics report

#### Payroll Report
- Total pengeluaran gaji/bulan
- Breakdown per komponen
- Tax report

#### Tenant Analytics (Super Admin)
- Usage trends
- Revenue per tenant
- Feature adoption rate

---

### 4.13 Settings

#### Super Admin Settings
- Platform branding
- Default subscription plans
- System-wide configurations
- Email templates

#### Tenant Settings

**Company Settings**
- Nama perusahaan
- Alamat
- Logo
- Jam kerja (jam masuk, jam pulang)
- Geofence location & radius

**System Settings (per tenant)**
- Email notification settings
- Attendance method (face/qr/both)
- Leave policies
- Payroll components

---

## 5. Database Schema

### 5.1 Core Tenant Tables

```
tenants
├── id
├── name (company name)
├── slug (subdomain)
├── domain (custom domain - optional)
├── logo
├── package_id (FK → subscription_packages)
├── subscription_status (active/trial/suspended)
├── trial_ends_at
├── settings (JSON)
├── limits (JSON - max employees, storage)
└── created_at

subscription_packages
├── id
├── name (Starter/Professional/Enterprise)
├── price_monthly
├── price_yearly
├── max_employees
├── max_storage_gb
├── features (JSON)
├── is_active
└── created_at

invoices
├── id
├── tenant_id (FK)
├── invoice_number (unique)
├── amount
├── status (pending/paid/overdue)
├── due_date
├── paid_at
├── payment_method
└── created_at
```

### 5.2 Tenant Data Tables (with tenant_id)

```
users
├── id
├── tenant_id (FK → tenants)
├── email
├── password
├── role (super_admin/admin/hr/employee)
├── employee_id (FK, nullable)
├── is_active
└── last_login_at

employees
├── id
├── tenant_id (FK)
├── nik (unique per tenant)
├── name
├── email_personal
├── phone
├── birth_date
├── gender
├── address
├── photo
├── face_data (JSON - face embedding)
├── department_id (FK)
├── position_id (FK)
├── join_date
├── status (contract/permanent/probation)
└── is_active

departments
├── id
├── tenant_id (FK)
├── name
├── head_id (FK → employees)
├── parent_id (self-referencing)
└── budget

positions
├── id
├── tenant_id (FK)
├── name
├── level
└── salary_grade

attendances
├── id
├── tenant_id (FK)
├── employee_id (FK)
├── date
├── check_in_time
├── check_out_time
├── check_in_photo
├── check_in_location (JSON: lat, lng)
├── check_in_method (face/qr)
├── qr_code_id (FK, nullable)
├── status (present/late/absent)
├── notes
└── created_at

qr_codes
├── id
├── tenant_id (FK)
├── code (unique)
├── date
├── expires_at
├── is_active
├── created_by (FK → users)
└── created_at

leave_requests
├── id
├── tenant_id (FK)
├── employee_id (FK)
├── type (leave/sick/permission)
├── start_date
├── end_date
├── reason
├── attachment
├── status (pending/approved/rejected)
├── approved_by (FK → users)
├── approved_at
└── created_at

leave_balances
├── id
├── tenant_id (FK)
├── employee_id (FK)
├── year
├── type (annual/sick/maternity)
├── total
├── used
└── remaining

payroll_periods
├── id
├── tenant_id (FK)
├── month
├── year
├── status (draft/processing/completed)
├── processed_by (FK → users)
├── processed_at
└── created_at

payrolls
├── id
├── tenant_id (FK)
├── employee_id (FK)
├── payroll_period_id (FK)
├── basic_salary
├── total_earnings
├── total_deductions
├── net_salary
├── status (draft/paid)
└── created_at

payroll_items
├── id
├── tenant_id (FK)
├── payroll_id (FK)
├── component_code (FK → payroll_components)
├── amount
├── type (earning/deduction)
├── description
└── created_at

payroll_components
├── id
├── tenant_id (FK)
├── code
├── name
├── type (earning/deduction)
├── calculation_type (fixed/percentage/formula)
├── default_value
├── is_active
└── created_at
```

---

## 6. API Endpoints

### 6.1 Super Admin APIs
```
GET    /api/super-admin/tenants
POST   /api/super-admin/tenants
GET    /api/super-admin/tenants/{id}
PUT    /api/super-admin/tenants/{id}
DELETE /api/super-admin/tenants/{id}

GET    /api/super-admin/dashboard/stats
GET    /api/super-admin/revenue
GET    /api/super-admin/system/health
```

### 6.2 Tenant Auth APIs
```
POST   /api/register                (tenant registration)
POST   /api/login
POST   /api/logout
POST   /api/forgot-password
POST   /api/reset-password
GET    /api/me                      (current user)
```

### 6.3 Tenant Admin APIs
```
GET    /api/tenant/settings
PUT    /api/tenant/settings
GET    /api/tenant/usage
GET    /api/tenant/billing
POST   /api/tenant/subscription/upgrade
```

### 6.4 Employee APIs
```
GET    /api/employees
POST   /api/employees
GET    /api/employees/{id}
PUT    /api/employees/{id}
DELETE /api/employees/{id}
POST   /api/employees/import
GET    /api/employees/export
POST   /api/employees/{id}/face-registration
```

### 6.5 Attendance APIs
```
POST   /api/attendance/check-in          (face detection)
POST   /api/attendance/check-in-qr       (QR scan)
POST   /api/attendance/check-out
GET    /api/attendance/history
GET    /api/attendance/summary/{month}/{year}
GET    /api/attendance/qr-code            (HR generate QR)
```

### 6.6 Leave APIs
```
GET    /api/leave-requests
POST   /api/leave-requests
PUT    /api/leave-requests/{id}/approve
PUT    /api/leave-requests/{id}/reject
GET    /api/leave-balance/{employee_id}
```

### 6.7 Payroll APIs
```
GET    /api/payroll-periods
POST   /api/payroll-periods
POST   /api/payroll/calculate/{period_id}
GET    /api/payroll/{employee_id}/{period_id}
GET    /api/payroll/{employee_id}/payslip/{period_id}
POST   /api/payroll/{id}/approve
```

---

## 7. Halaman (Pages)

### 7.1 Super Admin
| Route | Page |
|-------|------|
| `/super-admin/login` | Super Admin Login |
| `/super-admin/dashboard` | Platform Dashboard |
| `/super-admin/tenants` | Tenant Management |
| `/super-admin/tenants/:id` | Tenant Detail |
| `/super-admin/packages` | Subscription Packages |
| `/super-admin/invoices` | Invoice Management |
| `/super-admin/settings` | Platform Settings |

### 7.2 Landing Page (Public)
| Route | Page |
|-------|------|
| `/` | Homepage |
| `/pricing` | Pricing Page |
| `/features` | Features Page |
| `/register` | Tenant Registration |
| `/login` | Tenant Login |
| `/docs` | Documentation |

### 7.3 Tenant - Admin & HR
| Route | Page |
|-------|------|
| `/dashboard` | Dashboard |
| `/employees` | Data Karyawan |
| `/employees/:id` | Detail Karyawan |
| `/departments` | Manajemen Departemen |
| `/positions` | Manajemen Jabatan |
| `/attendance` | Rekap Absensi |
| `/attendance/live` | Monitor Absensi Real-time |
| `/leave` | Pengajuan Izin/Cuti |
| `/leave/approval` | Approval Izin/Cuti |
| `/payroll` | Proses Penggajian |
| `/payroll/payslip/:id` | Print Payslip |
| `/reports` | Laporan |
| `/settings` | Pengaturan |
| `/billing` | Subscription & Billing |

### 7.4 Tenant - Employee
| Route | Page |
|-------|------|
| `/my-dashboard` | Dashboard Saya |
| `/my-attendance` | Riwayat Absensi Saya |
| `/absen` | Halaman Absensi (Face/QR) |
| `/my-leave` | Pengajuan Izin/Cuti Saya |
| `/my-payslip` | Payslip Saya |
| `/my-profile` | Profil Saya |

---

## 8. Non-Functional Requirements

### 8.1 Performance
- Halaman load < 3 detik
- Face detection response < 2 detik
- Support 1000+ concurrent users across tenants
- Database query optimization dengan indexing
- Redis caching untuk frequently accessed data

### 8.2 Security
- **Tenant isolation**: Data tidak bisa diakses silang tenant
- HTTPS mandatory
- CSRF protection
- XSS prevention
- Rate limiting per tenant
- Input validation & sanitization
- Password hashing (bcrypt)
- JWT/Sanctum token expiry
- **Encryption**: Sensitive data (face_data, salary) di-encrypt

### 8.3 Scalability
- Horizontal scaling dengan load balancer
- Database read replicas
- Queue workers untuk background jobs
- File storage di S3 (scalable)
- CDN untuk static assets

### 8.4 Compatibility
- Browser: Chrome, Firefox, Safari, Edge (latest 2 versions)
- Mobile responsive
- Camera access required for face detection
- GPS access for geofence

### 8.5 Backup & Recovery
- **Automated daily backup** per tenant
- Point-in-time recovery
- Cross-region backup replication
- Manual backup & restore capability
- **Tenant-level backup** (optional add-on)

### 8.6 Multi-Tenancy SLA
- **Uptime**: 99.9% (excluding maintenance)
- **Data isolation**: Zero cross-tenant data leakage
- **Backup**: RPO < 24 hours, RTO < 4 hours
- **Support**: Email support within 24 hours

---

## 9. Deployment Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    LOAD BALANCER                        │
│                    (Nginx/HAProxy)                      │
└─────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
┌───────┴───────┐   ┌───────┴───────┐   ┌───────┴───────┐
│   App Server  │   │   App Server  │   │   App Server  │
│   (Laravel)   │   │   (Laravel)   │   │   (Laravel)   │
└───────────────┘   └───────────────┘   └───────────────┘
        │                   │                   │
        └───────────────────┼───────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
┌───────┴───────┐   ┌───────┴───────┐   ┌───────┴───────┐
│  MySQL (Main) │   │  Redis Cache  │   │  S3 Storage   │
│   Database    │   │    & Queue    │   │  (Files)      │
└───────────────┘   └───────────────┘   └───────────────┘
```

---

## 10. Milestones

### Phase 1: Foundation (6 minggu)
- [ ] Laravel project setup dengan tenancy package
- [ ] Database schema & migrations
- [ ] Tenant registration & onboarding flow
- [ ] Super admin panel (basic)
- [ ] Authentication system (all roles)

### Phase 2: Core HR Features (6 minggu)
- [ ] Employee CRUD
- [ ] Department & Position Management
- [ ] Basic Attendance (QR Code)
- [ ] Leave Management
- [ ] Tenant settings

### Phase 3: Advanced Features (6 minggu)
- [ ] Face Detection Attendance
- [ ] Geofence Validation
- [ ] Payroll System
- [ ] Payslip Generation
- [ ] Tax Calculation (PPH21)

### Phase 4: SaaS Features (4 minggu)
- [ ] Subscription & Billing system
- [ ] Payment gateway integration
- [ ] Usage metering & limits
- [ ] Super admin analytics

### Phase 5: Polish & Launch (4 minggu)
- [ ] Reports & Analytics
- [ ] Export Features
- [ ] Landing page & pricing
- [ ] Documentation
- [ ] Beta testing
- [ ] Performance optimization
- [ ] Security audit

---

## 11. Future Enhancements

- Mobile app (React Native / Flutter)
- White-label solution
- API access for enterprise
- Integration with accounting software (Jurnal, Accurate)
- Integration with BPJS online
- E-Signature for documents
- Training & e-learning module
- Recruitment & ATS module
- Performance appraisal system
- Employee self-service portal enhancement
- Chat/Messaging feature
- AI-powered analytics

---

## 12. Success Metrics

### Technical Metrics
- **Uptime**: 99.9%
- **Response time**: < 500ms (API), < 2s (pages)
- **Error rate**: < 0.1%
- **Data backup success**: 100%

### Business Metrics
- **MRR (Monthly Recurring Revenue)**: Target Rp 100M/bulan (12 bulan)
- **Active tenants**: 100+ (12 bulan)
- **Tenant churn rate**: < 5%/bulan
- **Customer satisfaction (NPS)**: > 50

---

**Document Version**: 2.0 (SaaS Edition)
**Created**: Agustus 2026
**Last Updated**: Agustus 2026
**Author**: Development Team
