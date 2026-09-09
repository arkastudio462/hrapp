<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    Calculator,
    FileText,
    Users,
    Hourglass,
    Wallet,
    ShieldCheck,
    ReceiptText,
    MapPin,
    CalendarDays,
    CalendarRange,
    ArrowRight,
    TrendingUp,
    Check,
    Plus,
    ShieldPlus,
    IdCard,
    CheckCircle2,
} from 'lucide-vue-next';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalEmployees: 0,
            presentToday: 0,
            lateToday: 0,
            onLeave: 0,
            pendingApprovals: 0,
            permanentCount: 0,
            contractCount: 0,
            attendanceRate: 0,
        }),
    },
    pendingLeaves: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const tenant = computed(() => page.props.tenant);

const isAdmin = computed(() => user.value?.role === 'admin');
const isHr = computed(() => user.value?.role === 'hr');
const canApproveLeaves = computed(() => isAdmin.value || isHr.value);
const canManageSubscription = computed(() => isAdmin.value);
const canManageSettings = computed(() => isAdmin.value);

const todayLabel = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

const leaveTypeLabels = {
    leave: 'Cuti Tahunan',
    sick: 'Izin Sakit',
    permission: 'Izin / Dinas',
};

const leaveTypeBadges = {
    leave: 'bg-secondary-fixed text-on-secondary-fixed',
    sick: 'bg-error-container text-error',
    permission: 'bg-surface-container text-on-primary-fixed',
};

const initials = (name) => (name || '?').split(' ').map((n) => n[0]).slice(0, 2).join('');

const chartBars = [
    { x: 75, y: 85, h: 95, active: false, label: 'Des 24', value: '95.8%' },
    { x: 175, y: 60, h: 120, active: false, label: 'Jan 25', value: '97.0%' },
    { x: 275, y: 75, h: 105, active: false, label: 'Feb 25', value: '96.4%' },
    { x: 375, y: 45, h: 135, active: false, label: 'Mar 25', value: '98.1%' },
    { x: 475, y: 55, h: 125, active: false, label: 'Apr 25', value: '97.3%' },
    { x: 565, y: 38, h: 142, active: true, label: 'Mei 25', value: '98.5%' },
];

const departments = [
    { name: 'Engineering', rate: 98, detail: '42/43 Hadir • 1 Sakit', good: true },
    { name: 'Sales & Mkt', rate: 94, detail: '33/35 Hadir • 2 Dinas Luar', good: false },
    { name: 'Operasional', rate: 96, detail: '48/50 Hadir • 2 Cuti', good: false },
    { name: 'Finance & GA', rate: 100, detail: '14/14 Lengkap', good: true },
];
</script>

<template>
    <AppLayout title="Dashboard">
    <!-- Hero Banner -->
    <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <!-- Welcome & Quick Actions -->
        <div class="relative flex flex-col justify-between overflow-hidden rounded-xl bg-surface-card p-6 shadow-sm xl:col-span-2">
            <div class="pointer-events-none absolute -right-12 -bottom-12 h-64 w-64 rounded-full bg-surface-container-low opacity-60"></div>
            <div class="relative z-10 flex flex-col gap-2">
                <div class="flex items-center gap-1.5">
                    <span class="rounded-full bg-surface-container px-2 py-0.5 text-xs font-semibold text-primary">Portal Administrasi HR</span>
                    <span class="text-xs text-on-surface-variant">•</span>
                    <span class="text-xs text-on-surface-variant">{{ todayLabel }}</span>
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-on-surface">Selamat pagi, {{ user?.name || 'Admin' }} 👋</h1>
                <p class="max-w-2xl text-sm leading-relaxed text-on-surface-variant">
                    Berikut adalah ringkasan absensi, izin tim, dan persiapan payroll untuk
                    <span class="font-medium text-on-surface">{{ tenant?.name || 'Perusahaan Anda' }}</span> hari ini. Semua sistem presensi biometrik &amp; geofence berfungsi normal.
                </p>
            </div>
            <div v-if="canApproveLeaves" class="relative z-10 mt-6 flex flex-wrap items-center gap-3 pt-4">
                <Link
                    href="/payroll"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-on-primary shadow-sm transition-all hover:bg-primary-container"
                >
                    <Calculator class="h-4 w-4" />
                    <span>Kalkulasi Payroll Cepat</span>
                </Link>
                <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-surface-subtle px-4 py-2 text-sm font-medium text-on-surface transition-colors hover:bg-surface-container">
                    <FileText class="h-4 w-4 text-on-surface-variant" />
                    <span>Download Ringkasan Harian (PDF)</span>
                </button>
                <div class="ml-auto hidden items-center gap-1.5 text-xs font-semibold text-status-success md:flex">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-status-success"></span>
                    <span>Sinkronisasi Cloud Real-Time</span>
                </div>
            </div>
        </div>
        <!-- Subscription Quota -->
        <div v-if="canManageSubscription" class="flex flex-col justify-between rounded-xl bg-surface-card p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold tracking-wider text-on-surface-variant uppercase">Alokasi Lisensi</span>
                    <h2 class="mt-1 text-base font-semibold text-on-surface">Paket Enterprise Pro</h2>
                </div>
                <span class="rounded bg-secondary-fixed px-2 py-0.5 text-xs font-semibold text-on-secondary-fixed">Aktif s/d Nov 2025</span>
            </div>
            <div class="my-4 space-y-1">
                <div class="flex items-baseline justify-between">
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold tracking-tight text-on-surface">{{ stats.totalEmployees }}</span>
                        <span class="text-sm text-on-surface-variant">/ 200 kursi terpakai</span>
                    </div>
                    <span class="text-xs font-semibold text-status-info">Sisa {{ 200 - stats.totalEmployees }} Kursi</span>
                </div>
                <div class="h-2.5 w-full overflow-hidden rounded-full bg-surface-subtle p-px">
                    <div class="h-full rounded-full bg-status-info transition-all duration-500" :style="{ width: Math.min((stats.totalEmployees / 200) * 100, 100) + '%' }"></div>
                </div>
                <p class="text-xs text-on-surface-variant">
                    Pemanfaatan kapasitas saat ini <span class="text-xs font-semibold text-on-surface">{{ Math.round((stats.totalEmployees / 200) * 100) }}%</span>. Tambahkan kursi sebelum kuota tercapai.
                </p>
            </div>
            <Link
                v-if="canManageSubscription"
                href="/subscription"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-surface-subtle px-4 py-2 text-sm font-medium text-primary transition-colors hover:bg-surface-container-high"
            >
                <ShieldPlus class="h-4 w-4" />
                <span>Kelola Paket &amp; Tambah Kuota</span>
            </Link>
        </div>
    </section>
    <!-- KPI Cards -->
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="flex flex-col justify-between rounded-xl bg-surface-card p-4 shadow-sm transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-on-surface-variant">Total Karyawan Aktif</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-container-low text-primary">
                    <Users class="h-5 w-5" />
                </div>
            </div>
            <div class="my-2">
                <div class="text-3xl font-bold tracking-tight text-on-surface">{{ stats.totalEmployees }}</div>
                <div class="mt-1 flex items-center gap-1">
                    <span class="inline-flex items-center text-xs font-semibold text-status-success">
                        <TrendingUp class="h-4 w-4" />
                        +4 bulan ini
                    </span>
                    <span class="text-xs text-on-surface-variant">vs April</span>
                </div>
            </div>
            <div class="-mx-4 -mb-4 rounded-b-xl bg-surface-subtle px-4 py-1.5">
                <span class="text-xs text-on-surface-variant">{{ stats.permanentCount }} Karyawan Tetap • {{ stats.contractCount }} PKWT</span>
            </div>
        </div>
        <div class="flex flex-col justify-between rounded-xl bg-surface-card p-4 shadow-sm transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-on-surface-variant">Tingkat Kehadiran Hari Ini</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-container-low text-status-success">
                    <CheckCircle2 class="h-5 w-5" />
                </div>
            </div>
            <div class="my-2">
                <div class="flex items-baseline gap-2">
                    <div class="text-3xl font-bold tracking-tight text-on-surface">{{ stats.attendanceRate }}%</div>
                    <span class="rounded bg-surface-container px-1.5 py-0.5 text-xs font-semibold text-on-primary-fixed">{{ stats.presentToday }} Hadir</span>
                </div>
                <div class="mt-1 flex items-center gap-1">
                    <span class="text-xs font-semibold text-status-warning">{{ stats.lateToday }} Terlambat</span>
                    <span class="text-xs text-on-surface-variant">•</span>
                    <span class="text-xs text-on-surface-variant">{{ stats.onLeave }} Izin / Sakit</span>
                </div>
            </div>
            <div class="-mx-4 -mb-4 flex items-center justify-between rounded-b-xl bg-surface-subtle px-4 py-1.5">
                <span class="text-xs text-on-surface-variant">Batas presensi: 09.00 WIB</span>
                <span class="text-xs font-semibold text-status-success">Tertib</span>
            </div>
        </div>
        <div class="flex flex-col justify-between rounded-xl bg-surface-card p-4 shadow-sm transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-on-surface-variant">Izin &amp; Cuti Menunggu</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-error-container text-error">
                    <Hourglass class="h-5 w-5" />
                </div>
            </div>
            <div class="my-2">
                <div class="flex items-baseline gap-2">
                    <div class="text-3xl font-bold tracking-tight text-on-surface">{{ stats.pendingApprovals }}</div>
                    <span class="rounded-full bg-status-warning px-2 py-0.5 text-xs font-semibold text-on-primary">Perlu Review</span>
                </div>
                <div class="mt-1 text-xs text-on-surface-variant">2 Pengajuan Cuti • 1 Sakit</div>
            </div>
            <div class="-mx-4 -mb-4 flex items-center justify-between rounded-b-xl bg-surface-subtle px-4 py-1.5">
                <span class="text-xs text-on-surface-variant">SLA Response: 24 Jam</span>
                <span class="text-xs font-semibold text-status-warning">Penting</span>
            </div>
        </div>
        <div class="flex flex-col justify-between rounded-xl bg-surface-card p-4 shadow-sm transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-on-surface-variant">Estimasi Penggajian Mei</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-container-low text-primary">
                    <Wallet class="h-5 w-5" />
                </div>
            </div>
            <div class="my-2">
                <div class="truncate text-3xl font-bold tracking-tight text-on-surface">Rp 342,8 Jt</div>
                <div class="mt-1 flex items-center gap-1">
                    <span class="rounded bg-surface-container px-1.5 py-0.5 text-xs font-semibold text-on-primary-fixed-variant">Cut-off: 25 Mei</span>
                    <span class="text-xs text-on-surface-variant">Siap Kalkulasi</span>
                </div>
            </div>
            <div class="-mx-4 -mb-4 rounded-b-xl bg-surface-subtle px-4 py-1.5">
                <span class="text-xs text-on-surface-variant">Termasuk PPh 21 TER &amp; BPJS TK/Kes</span>
            </div>
        </div>
    </section>
    <!-- Middle Section -->
    <section class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
        <!-- Left Column -->
        <div class="space-y-6 lg:col-span-8">
            <!-- Attendance Chart -->
            <div class="rounded-xl bg-surface-card p-6 shadow-sm">
                <div class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-base font-semibold text-on-surface">Tren Kehadiran &amp; Kedisiplinan Kerja</h2>
                        <p class="text-xs text-on-surface-variant">Analisis komparasi semester I (Desember 2024 - Mei 2025)</p>
                    </div>
                    <div class="inline-flex rounded-lg bg-surface-subtle p-1 text-xs">
                        <button type="button" class="rounded-md bg-surface-card px-2 py-1 text-xs font-semibold text-on-surface shadow-sm">Kehadiran %</button>
                        <button type="button" class="rounded-md px-2 py-1 text-xs text-on-surface-variant transition-colors hover:text-on-surface">Lembur (Jam)</button>
                        <button type="button" class="rounded-md px-2 py-1 text-xs text-on-surface-variant transition-colors hover:text-on-surface">Turnover %</button>
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-3 gap-1 rounded-lg bg-surface-subtle px-4 py-1.5">
                    <div>
                        <span class="block text-xs text-on-surface-variant">Rata-rata Kehadiran</span>
                        <span class="text-base font-semibold text-on-surface">97.2%</span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant">Tingkat Ketepatan Jam</span>
                        <span class="text-base font-semibold text-status-success">98.4%</span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant">Total Jam Lembur</span>
                        <span class="text-base font-semibold text-on-surface">318 Jam</span>
                    </div>
                </div>
                <div class="w-full overflow-x-auto">
                    <div class="min-w-[540px]">
                        <svg aria-label="Grafik Kehadiran 6 Bulan" class="h-56 w-full overflow-visible" viewBox="0 0 640 220">
                            <defs>
                                <linearGradient id="areaGradient" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#0284C7" stop-opacity="0.25"></stop>
                                    <stop offset="100%" stop-color="#0284C7" stop-opacity="0.0"></stop>
                                </linearGradient>
                            </defs>
                            <line stroke="#F1F5F9" stroke-width="1.5" x1="40" x2="620" y1="20" y2="20"></line>
                            <text fill="#94a3b8" font-size="11" text-anchor="end" x="32" y="24">100%</text>
                            <line stroke="#F1F5F9" stroke-width="1.5" x1="40" x2="620" y1="70" y2="70"></line>
                            <text fill="#94a3b8" font-size="11" text-anchor="end" x="32" y="74">98%</text>
                            <line stroke="#F1F5F9" stroke-width="1.5" x1="40" x2="620" y1="120" y2="120"></line>
                            <text fill="#94a3b8" font-size="11" text-anchor="end" x="32" y="124">96%</text>
                            <line stroke="#F1F5F9" stroke-width="1.5" x1="40" x2="620" y1="170" y2="170"></line>
                            <text fill="#94a3b8" font-size="11" text-anchor="end" x="32" y="174">94%</text>
                            <rect v-for="b in chartBars" :key="b.x" :class="b.active ? 'fill-primary-fixed hover:fill-primary-fixed-dim' : 'fill-surface-subtle hover:fill-surface-container'" class="transition-colors" :height="b.h" rx="4" width="30" :x="b.x" :y="b.y"></rect>
                            <path d="M 90,85 C 130,70 150,60 190,60 C 230,60 250,75 290,75 C 330,75 350,45 390,45 C 430,45 450,55 490,55 C 530,55 550,38 580,38 L 580,180 L 90,180 Z" fill="url(#areaGradient)"></path>
                            <path d="M 90,85 C 130,70 150,60 190,60 C 230,60 250,75 290,75 C 330,75 350,45 390,45 C 430,45 450,55 490,55 C 530,55 550,38 580,38" fill="none" stroke="#006194" stroke-linecap="round" stroke-width="3"></path>
                            <g v-for="(b, i) in chartBars" :key="'p' + i" class="group cursor-pointer">
                                <circle :class="b.active ? 'fill-primary stroke-surface-card' : 'fill-surface-card stroke-primary'" stroke-width="3" :cx="b.x + 15" :cy="b.y" :r="b.active ? 6 : 4"></circle>
                                <text :class="b.active ? 'fill-primary' : 'fill-on-surface'" :font-size="b.active ? 11 : 10" :font-weight="b.active ? 700 : 600" text-anchor="middle" :x="b.x + 15" :y="b.y - 12">{{ b.value }}</text>
                            </g>
                            <text v-for="b in chartBars" :key="'l' + b.x" :class="b.active ? 'fill-primary' : 'fill-slate-500'" :font-size="12" :font-weight="b.active ? 700 : 400" text-anchor="middle" :x="b.x + 15" y="202">{{ b.label }}</text>
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Department Distribution -->
            <div class="rounded-xl bg-surface-card p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-on-surface">Distribusi Kehadiran Departemen Hari Ini</h3>
                        <p class="text-xs text-on-surface-variant">Perbandingan rasio kehadiran waktu kerja riil per divisi</p>
                    </div>
                    <Link href="/departments" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-container">
                        <span>Detail Tim</span>
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="dept in departments" :key="dept.name" class="flex flex-col justify-between rounded-lg bg-surface-subtle p-2.5">
                        <div class="mb-1 flex items-center justify-between">
                            <span class="truncate text-sm font-medium text-on-surface">{{ dept.name }}</span>
                            <span class="text-xs font-semibold" :class="dept.good ? 'text-status-success' : 'text-on-surface'">{{ dept.rate }}%</span>
                        </div>
                        <div class="mb-1 h-1.5 w-full overflow-hidden rounded-full bg-border-subtle">
                            <div class="h-full rounded-full" :class="dept.good ? 'bg-status-success' : 'bg-primary'" :style="{ width: dept.rate + '%' }"></div>
                        </div>
                        <span class="text-xs text-on-surface-variant">{{ dept.detail }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right Column: Pending Approvals -->
        <div class="flex flex-col rounded-xl bg-surface-card p-6 shadow-sm lg:col-span-4">
            <div class="flex items-start justify-between pb-3">
                <div>
                    <h2 class="text-base font-semibold text-on-surface">Persetujuan Tertunda</h2>
                    <p class="text-xs text-on-surface-variant">Pengajuan izin &amp; cuti butuh respon</p>
                </div>
                <span class="rounded-full bg-status-warning px-2 py-0.5 text-xs font-semibold text-on-primary">{{ stats.pendingApprovals }} Menunggu</span>
            </div>
            <div class="flex flex-1 flex-col divide-y divide-border-subtle">
                <div v-for="leave in pendingLeaves" :key="leave.id" class="group flex flex-col gap-1.5 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-fixed text-sm font-semibold text-on-primary-fixed">
                                {{ initials(leave.employee_name) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-on-surface">{{ leave.employee_name }}</span>
                                <span class="text-xs text-on-surface-variant">{{ leave.position }} • {{ leave.department }}</span>
                            </div>
                        </div>
                        <span class="rounded px-2 py-1 text-xs font-semibold" :class="leaveTypeBadges[leave.type]">
                            {{ leaveTypeLabels[leave.type] }}
                        </span>
                    </div>
                    <div class="rounded-lg bg-surface-subtle p-2 text-xs text-on-surface-variant">
                        <div class="mb-1 flex items-center gap-1 font-semibold text-on-surface">
                            <CalendarRange class="h-4 w-4 text-tertiary" />
                            <span>{{ leave.start_date }} - {{ leave.end_date }} ({{ leave.days }} Hari)</span>
                        </div>
                        <p class="italic">"{{ leave.reason }}"</p>
                    </div>
                    <div v-if="canApproveLeaves" class="mt-1 flex items-center gap-2">
                        <Link
                            :href="`/leaves/${leave.id}/approve`"
                            method="post"
                            as="button"
                            class="inline-flex flex-1 items-center justify-center gap-1 rounded-lg bg-primary py-1.5 px-2 text-xs font-semibold text-on-primary transition-colors hover:bg-primary-container"
                        >
                            <Check class="h-4 w-4" />
                            <span>Setujui</span>
                        </Link>
                        <Link
                            :href="`/leaves/${leave.id}/reject`"
                            method="post"
                            as="button"
                            class="rounded-lg bg-surface-subtle px-4 py-1.5 text-xs font-semibold text-on-surface-variant transition-colors hover:bg-error-container hover:text-error"
                        >
                            Tolak
                        </Link>
                    </div>
                </div>
                <div v-if="!pendingLeaves.length" class="py-6 text-center text-sm text-on-surface-variant">
                    Tidak ada pengajuan yang menunggu.
                </div>
            </div>
            <div class="mt-auto pt-4 text-center">
                <Link href="/leaves" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-container">
                    <span>Lihat Semua Riwayat Pengajuan Izin</span>
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
        </div>
    </section>
    <!-- Bottom Grid -->
    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Compliance -->
        <div class="flex flex-col justify-between rounded-xl bg-surface-card p-6 shadow-sm">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <ShieldCheck class="h-5 w-5 text-primary" />
                        <h3 class="text-base font-semibold text-on-surface">Kepatuhan Regulasi &amp; Status Operasional</h3>
                    </div>
                    <span class="rounded bg-surface-container-low px-2 py-0.5 text-xs font-semibold text-primary">Status Normal</span>
                </div>
                <p class="mb-4 text-xs text-on-surface-variant">
                    Sistem otomatis memantau regulasi ketenagakerjaan RI, masa kontrak, dan integritas presensi lokasi kantor.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 rounded-lg bg-surface-subtle p-2.5">
                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-surface-container text-primary">
                            <ReceiptText class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-medium text-on-surface">Perhitungan Pajak PPh 21 TER (PP 58/2023)</span>
                                <span class="h-2 w-2 rounded-full bg-status-success"></span>
                            </div>
                            <span class="text-xs text-on-surface-variant">Skema Tarif Efektif Rata-rata (Kategori A, B, C) aktif dan telah tervalidasi dengan slip gaji bulan Mei 2025.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-lg bg-surface-subtle p-2.5">
                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-error-container text-status-warning">
                            <IdCard class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-on-surface">2 Karyawan Kontrak PKWT Berakhir Segera</span>
                                <span class="text-xs font-semibold text-status-warning">&lt; 30 Hari</span>
                            </div>
                            <span class="text-xs text-on-surface-variant">Andi Pratama (IT Support) &amp; Rina Kusuma (Operations) memerlukan evaluasi perpanjangan kontrak kerja.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-lg bg-surface-subtle p-2.5">
                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-surface-container text-tertiary">
                            <MapPin class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-medium text-on-surface">Status Geofence Multi-Cabang</span>
                                <span class="rounded bg-surface-container px-1.5 py-0.5 text-xs font-semibold text-on-primary-fixed">3 Titik Online</span>
                            </div>
                            <span class="text-xs text-on-surface-variant">Kantor Pusat Jakarta, Hub Surabaya, dan Bali Satellite Office aktif dengan radius toleransi 50 meter.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between pt-4">
                <span class="text-xs text-on-surface-variant">Terakhir diaudit: Hari ini 06:00 WIB</span>
                <button v-if="canManageSettings" type="button" class="text-xs font-semibold text-primary hover:text-primary-container">Perbarui Audit Kepegawaian</button>
            </div>
        </div>
        <!-- Agenda -->
        <div class="flex flex-col justify-between rounded-xl bg-surface-card p-6 shadow-sm">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <CalendarDays class="h-5 w-5 text-primary" />
                        <h3 class="text-base font-semibold text-on-surface">Agenda &amp; Jadwal Penting HR</h3>
                    </div>
                    <button v-if="canApproveLeaves" type="button" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-container">
                        <Plus class="h-4 w-4" />
                        <span>Tambah Agenda</span>
                    </button>
                </div>
                <p class="mb-4 text-xs text-on-surface-variant">
                    Pengingat tenggat waktu penggajian, evaluasi kinerja tim, dan program engagement kuartalan.
                </p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-surface-subtle p-2.5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-surface-card text-center shadow-sm">
                                <span class="text-[11px] font-semibold text-primary uppercase">MEI</span>
                                <span class="text-base leading-none font-semibold text-on-surface">15</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-on-surface">Review KPI Q2 - Divisi Tech &amp; Product</span>
                                <span class="text-xs text-on-surface-variant">Besok, 10:00 - 12:00 WIB • Ruang Rapat Kartini / Zoom</span>
                            </div>
                        </div>
                        <span class="rounded bg-secondary-fixed px-2 py-0.5 text-xs font-semibold text-on-secondary-fixed">Internal</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-surface-subtle p-2.5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-surface-card text-center shadow-sm">
                                <span class="text-[11px] font-semibold text-status-warning uppercase">MEI</span>
                                <span class="text-base leading-none font-semibold text-status-warning">25</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-on-surface">Cut-off Penggajian Periode Mei 2025</span>
                                <span class="text-xs text-on-surface-variant">Batas akhir approval lembur, insentif &amp; potong gaji</span>
                            </div>
                        </div>
                        <span class="rounded bg-error-container px-2 py-0.5 text-xs font-semibold text-status-warning">Kritikal</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-surface-subtle p-2.5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-surface-card text-center shadow-sm">
                                <span class="text-[11px] font-semibold text-tertiary uppercase">MEI</span>
                                <span class="text-base leading-none font-semibold text-on-surface">30</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-on-surface">Townhall Akbar Kuartal II &amp; Employee Award</span>
                                <span class="text-xs text-on-surface-variant">15:30 - 17:30 WIB • Seluruh 142 Karyawan Hybrid</span>
                            </div>
                        </div>
                        <span class="rounded bg-surface-container px-2 py-0.5 text-xs font-semibold text-on-primary-fixed">Perusahaan</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between pt-4">
                <span class="text-xs text-on-surface-variant">Sinkron dengan Google Calendar</span>
                <a href="#" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-container">
                    <span>Buka Kalender Kerja Lengkap</span>
                    <CalendarDays class="h-4 w-4" />
                </a>
            </div>
        </div>
    </section>
    </AppLayout>
</template>
