<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { leaveTypeLabels, leaveTypeBadges } from '@/lib/constants';
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
            newThisMonth: 0,
            expiringContracts: 0,
            attendanceRate: 0,
            payrollEstimate: 0,
            cutOffDay: null,
        }),
    },
    subscription: {
        type: Object,
        default: () => ({
            packageName: '-',
            maxEmployees: 200,
            subscriptionStatus: 'trial',
        }),
    },
    settings: {
        type: Object,
        default: () => ({
            workStart: '08:00',
            geofenceRadius: 100,
        }),
    },
    pendingLeaves: {
        type: Array,
        default: () => [],
    },
    departments: {
        type: Array,
        default: () => [],
    },
    monthlyTrend: {
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

const todayLabel = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

const initials = (name) => (name || '?').split(' ').map((n) => n[0]).slice(0, 2).join('');

const today = new Date();
const indoMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
const todayMonth = indoMonths[today.getMonth()];
const todayDate = today.getDate();
const nextMonthDate = new Date(today.getFullYear(), today.getMonth() + 1, 1);
const nextMonthMonth = indoMonths[nextMonthDate.getMonth()];
const nextMonthDay = nextMonthDate.getDate();
const in30DaysDate = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000);
const in30DaysMonth = indoMonths[in30DaysDate.getMonth()];
const in30DaysDay = in30DaysDate.getDate();

const formatRupiah = (amount) => {
    if (amount >= 1_000_000_000) {
        return 'Rp ' + (amount / 1_000_000_000).toFixed(1).replace('.', ',') + ' M';
    }
    if (amount >= 1_000_000) {
        return 'Rp ' + (amount / 1_000_000).toFixed(1).replace('.', ',') + ' Jt';
    }
    return 'Rp ' + Number(amount).toLocaleString('id-ID');
};

const quotaPercentage = computed(() => {
    const max = props.subscription.maxEmployees;
    return max > 0 ? Math.min((props.stats.totalEmployees / max) * 100, 100) : 0;
});

const quotaRemaining = computed(() => {
    return Math.max(props.subscription.maxEmployees - props.stats.totalEmployees, 0);
});

const chartBars = computed(() => {
    const trend = props.monthlyTrend;
    if (!trend.length) return [];

    const barWidth = 30;
    const gap = 100;
    const startX = 75;
    const chartHeight = 142;
    const minRate = Math.min(...trend.map((d) => d.rate));
    const maxRate = Math.max(...trend.map((d) => d.rate));
    const range = maxRate - minRate || 1;

    return trend.map((d, i) => {
        const normalizedRate = (d.rate - minRate) / range;
        const barHeight = Math.max(20, normalizedRate * chartHeight);
        const y = 38 + (chartHeight - barHeight);

        return {
            x: startX + i * gap,
            y,
            h: barHeight,
            active: i === trend.length - 1,
            label: d.label,
            value: d.rate + '%',
        };
    });
});

const avgAttendance = computed(() => {
    const trend = props.monthlyTrend;
    if (!trend.length) return 0;
    return (trend.reduce((sum, d) => sum + d.rate, 0) / trend.length).toFixed(1);
});

const departments = computed(() => props.departments);

const linePath = computed(() => {
    const bars = chartBars.value;
    if (bars.length < 2) return '';

    const points = bars.map((b) => ({ x: b.x + 15, y: b.y }));
    let d = `M ${points[0].x},${points[0].y}`;

    for (let i = 1; i < points.length; i++) {
        const prev = points[i - 1];
        const curr = points[i];
        const cpx1 = prev.x + (curr.x - prev.x) * 0.4;
        const cpx2 = curr.x - (curr.x - prev.x) * 0.4;
        d += ` C ${cpx1},${prev.y} ${cpx2},${curr.y} ${curr.x},${curr.y}`;
    }

    return d;
});

const areaPath = computed(() => {
    const bars = chartBars.value;
    if (bars.length < 2) return '';

    const line = linePath.value;
    const lastX = bars[bars.length - 1].x + 15;
    const firstX = bars[0].x + 15;

    return `${line} L ${lastX},180 L ${firstX},180 Z`;
});
</script>

<template>
    <AppLayout title="Dashboard">
    <!-- Hero Banner -->
    <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="relative flex flex-col justify-between overflow-hidden rounded-2xl bg-white p-6 shadow-soft xl:col-span-2">
            <div class="pointer-events-none absolute -right-12 -bottom-12 h-64 w-64 rounded-full bg-blush/20"></div>
            <div class="relative z-10 flex flex-col gap-2">
                <div class="flex items-center gap-1.5">
                    <span class="rounded-full bg-coral/10 px-2.5 py-0.5 text-[11px] font-extrabold text-coral">Portal HR</span>
                    <span class="text-[11px] text-muted-app">&bull;</span>
                    <span class="text-[11px] text-muted-app">{{ todayLabel }}</span>
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Selamat pagi, {{ user?.name || 'Admin' }} &#x1F44B;</h1>
                <p class="max-w-2xl text-sm leading-relaxed text-muted-app">
                    Ringkasan absensi, izin tim, dan persiapan payroll untuk
                    <span class="font-bold text-ink-app">{{ tenant?.name || 'Perusahaan Anda' }}</span> hari ini.
                </p>
            </div>
            <div v-if="canApproveLeaves" class="relative z-10 mt-6 flex flex-wrap items-center gap-3 pt-4">
                <Link
                    href="/payroll"
                    class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2.5 text-sm font-bold text-white shadow-soft transition-all hover:bg-coral-deep"
                >
                    <Calculator class="size-4" />
                    <span>Kalkulasi Payroll Cepat</span>
                </Link>
                <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-outline-app/50 bg-white px-4 py-2.5 text-sm font-bold text-ink-app transition-colors hover:bg-cream">
                    <FileText class="size-4 text-muted-app" />
                    <span>Download Ringkasan</span>
                </button>
            </div>
        </div>

        <!-- Subscription Quota -->
        <div v-if="canManageSubscription" class="flex flex-col justify-between rounded-2xl bg-white p-6 shadow-soft">
            <div>
                <span class="text-[11px] font-extrabold tracking-wider text-muted-app uppercase">Alokasi Lisensi</span>
                <h2 class="mt-1 text-base font-extrabold text-ink-app">{{ subscription.packageName }}</h2>
            </div>
            <div class="my-4 space-y-1.5">
                <div class="flex items-baseline justify-between">
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-extrabold tracking-tight text-ink-app">{{ stats.totalEmployees }}</span>
                        <span class="text-sm text-muted-app">/ {{ subscription.maxEmployees }} kursi</span>
                    </div>
                    <span class="text-[11px] font-bold text-sage">Sisa {{ quotaRemaining }}</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-cream">
                    <div class="h-full rounded-full bg-coral transition-all duration-500" :style="{ width: quotaPercentage + '%' }"></div>
                </div>
            </div>
            <Link
                href="/subscription"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-outline-app/50 bg-cream px-4 py-2.5 text-sm font-bold text-ink-app transition-colors hover:bg-blush/20"
            >
                <ShieldPlus class="size-4" />
                Kelola Paket
            </Link>
        </div>
    </section>

    <!-- KPI Cards -->
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="flex flex-col justify-between rounded-2xl bg-white p-5 shadow-soft transition-shadow hover:shadow-card">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-muted-app">Total Karyawan Aktif</span>
                <div class="flex size-9 items-center justify-center rounded-xl bg-lavender text-purple-700">
                    <Users class="size-5" />
                </div>
            </div>
            <div class="my-2">
                <div class="text-3xl font-extrabold tracking-tight text-ink-app">{{ stats.totalEmployees }}</div>
                <div class="mt-1 flex items-center gap-1">
                    <span class="inline-flex items-center text-[11px] font-bold text-sage">
                        <TrendingUp class="size-3.5" />
                        +{{ stats.newThisMonth }} bulan ini
                    </span>
                </div>
            </div>
            <div class="rounded-xl bg-cream p-2.5">
                <span class="text-[11px] font-medium text-muted-app">{{ stats.permanentCount }} Tetap &bull; {{ stats.contractCount }} PKWT</span>
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-2xl bg-white p-5 shadow-soft transition-shadow hover:shadow-card">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-muted-app">Tingkat Kehadiran</span>
                <div class="flex size-9 items-center justify-center rounded-xl bg-sage/50 text-green-700">
                    <CheckCircle2 class="size-5" />
                </div>
            </div>
            <div class="my-2">
                <div class="flex items-baseline gap-2">
                    <div class="text-3xl font-extrabold tracking-tight text-ink-app">{{ stats.attendanceRate }}%</div>
                    <span class="rounded-full bg-sage/50 px-2 py-0.5 text-[11px] font-extrabold text-green-700">{{ stats.presentToday }} Hadir</span>
                </div>
                <div class="mt-1 flex items-center gap-1.5">
                    <span class="text-[11px] font-bold text-amber">{{ stats.lateToday }} Terlambat</span>
                    <span class="text-muted-app">&bull;</span>
                    <span class="text-[11px] text-muted-app">{{ stats.onLeave }} Izin</span>
                </div>
            </div>
            <div class="flex items-center justify-between rounded-xl bg-cream p-2.5">
                <span class="text-[11px] text-muted-app">Batas: {{ settings.workStart }} WIB</span>
                <span class="text-[11px] font-bold text-sage">Tertib</span>
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-2xl bg-white p-5 shadow-soft transition-shadow hover:shadow-card">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-muted-app">Izin & Cuti Pending</span>
                <div class="flex size-9 items-center justify-center rounded-xl bg-blush/40 text-coral-deep">
                    <Hourglass class="size-5" />
                </div>
            </div>
            <div class="my-2">
                <div class="flex items-baseline gap-2">
                    <div class="text-3xl font-extrabold tracking-tight text-ink-app">{{ stats.pendingApprovals }}</div>
                    <span class="rounded-full bg-amber/40 px-2 py-0.5 text-[11px] font-extrabold text-yellow-700">Perlu Review</span>
                </div>
            </div>
            <div class="flex items-center justify-between rounded-xl bg-cream p-2.5">
                <span class="text-[11px] text-muted-app">SLA: 24 Jam</span>
                <span class="text-[11px] font-bold text-amber">Penting</span>
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-2xl bg-white p-5 shadow-soft transition-shadow hover:shadow-card">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-muted-app">Estimasi Payroll</span>
                <div class="flex size-9 items-center justify-center rounded-xl bg-lavender text-purple-700">
                    <Wallet class="size-5" />
                </div>
            </div>
            <div class="my-2">
                <div class="truncate text-3xl font-extrabold tracking-tight text-ink-app">{{ formatRupiah(stats.payrollEstimate) }}</div>
                <div class="mt-1" v-if="stats.cutOffDay">
                    <span class="rounded bg-cream px-2 py-0.5 text-[11px] font-bold text-muted-app">Cut-off: {{ stats.cutOffDay }}</span>
                </div>
            </div>
            <div class="rounded-xl bg-cream p-2.5">
                <span class="text-[11px] text-muted-app">Termasuk PPh 21 & BPJS</span>
            </div>
        </div>
    </section>

    <!-- Middle Section -->
    <section class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
        <div class="space-y-6 lg:col-span-8">
            <!-- Attendance Chart -->
            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <div class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-base font-extrabold text-ink-app">Tren Kehadiran</h2>
                        <p class="text-xs text-muted-app">Analisis komparasi semester</p>
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-3 gap-1 rounded-xl bg-cream px-4 py-2">
                    <div>
                        <span class="block text-[11px] text-muted-app">Rata-rata</span>
                        <span class="text-sm font-extrabold text-ink-app">{{ avgAttendance }}%</span>
                    </div>
                    <div>
                        <span class="block text-[11px] text-muted-app">Terlambat</span>
                        <span class="text-sm font-extrabold text-amber">{{ stats.lateToday }}</span>
                    </div>
                    <div>
                        <span class="block text-[11px] text-muted-app">Hadir</span>
                        <span class="text-sm font-extrabold text-ink-app">{{ stats.presentToday }}</span>
                    </div>
                </div>
                <div class="w-full overflow-x-auto">
                    <div v-if="!chartBars.length" class="py-12 text-center text-sm text-muted-app">
                        Belum ada data tren kehadiran.
                    </div>
                    <div v-else class="min-w-[540px]">
                        <svg aria-label="Grafik Kehadiran" class="h-56 w-full overflow-visible" :viewBox="`0 0 ${Math.max(640, (chartBars.length * 100) + 140)} 220`">
                            <defs>
                                <linearGradient id="areaGradient" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#F0703C" stop-opacity="0.25"></stop>
                                    <stop offset="100%" stop-color="#F0703C" stop-opacity="0.0"></stop>
                                </linearGradient>
                            </defs>
                            <line stroke="#E8E5E0" stroke-width="1.5" x1="40" x2="620" y1="20" y2="20"></line>
                            <text fill="#9CA3AF" font-size="11" text-anchor="end" x="32" y="24">100%</text>
                            <line stroke="#E8E5E0" stroke-width="1.5" x1="40" x2="620" y1="70" y2="70"></line>
                            <text fill="#9CA3AF" font-size="11" text-anchor="end" x="32" y="74">98%</text>
                            <line stroke="#E8E5E0" stroke-width="1.5" x1="40" x2="620" y1="120" y2="120"></line>
                            <text fill="#9CA3AF" font-size="11" text-anchor="end" x="32" y="124">96%</text>
                            <line stroke="#E8E5E0" stroke-width="1.5" x1="40" x2="620" y1="170" y2="170"></line>
                            <text fill="#9CA3AF" font-size="11" text-anchor="end" x="32" y="174">94%</text>
                            <rect v-for="b in chartBars" :key="b.x" :class="b.active ? 'fill-coral' : 'fill-cream'" class="transition-colors" :height="b.h" rx="4" width="30" :x="b.x" :y="b.y"></rect>
                            <path v-if="chartBars.length > 1" :d="areaPath" fill="url(#areaGradient)"></path>
                            <path v-if="chartBars.length > 1" :d="linePath" fill="none" stroke="#F0703C" stroke-linecap="round" stroke-width="3"></path>
                            <g v-for="(b, i) in chartBars" :key="'p' + i" class="group cursor-pointer">
                                <circle :class="b.active ? 'fill-coral stroke-white' : 'fill-white stroke-coral'" stroke-width="3" :cx="b.x + 15" :cy="b.y" :r="b.active ? 6 : 4"></circle>
                                <text :class="b.active ? 'fill-coral' : 'fill-ink-app'" :font-size="b.active ? 11 : 10" :font-weight="b.active ? 700 : 600" text-anchor="middle" :x="b.x + 15" :y="b.y - 12">{{ b.value }}</text>
                            </g>
                            <text v-for="b in chartBars" :key="'l' + b.x" :class="b.active ? 'fill-coral' : 'fill-muted-app'" :font-size="12" :font-weight="b.active ? 700 : 400" text-anchor="middle" :x="b.x + 15" y="202">{{ b.label }}</text>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Department Distribution -->
            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-ink-app">Distribusi Kehadiran Departemen</h3>
                        <p class="text-xs text-muted-app">Rasio kehadiran per divisi</p>
                    </div>
                    <Link href="/departments" class="inline-flex items-center gap-1 text-xs font-bold text-coral hover:text-coral-deep">
                        <span>Detail</span>
                        <ArrowRight class="size-3.5" />
                    </Link>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-if="!departments.length" class="col-span-full py-6 text-center text-sm text-muted-app">
                        Belum ada data kehadiran departemen.
                    </div>
                    <div v-for="dept in departments.slice(0, 4)" :key="dept.name" class="flex flex-col justify-between rounded-xl bg-cream p-3">
                        <div class="mb-1 flex items-center justify-between">
                            <span class="truncate text-sm font-bold text-ink-app">{{ dept.name }}</span>
                            <span class="text-xs font-extrabold" :class="dept.good ? 'text-sage' : 'text-ink-app'">{{ dept.rate }}%</span>
                        </div>
                        <div class="mb-1 h-1.5 w-full overflow-hidden rounded-full bg-outline-app/30">
                            <div class="h-full rounded-full" :class="dept.good ? 'bg-sage' : 'bg-coral'" :style="{ width: dept.rate + '%' }"></div>
                        </div>
                        <span class="text-[11px] text-muted-app">{{ dept.detail }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Pending Approvals -->
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-soft lg:col-span-4">
            <div class="flex items-start justify-between pb-3">
                <div>
                    <h2 class="text-base font-extrabold text-ink-app">Persetujuan Tertunda</h2>
                    <p class="text-xs text-muted-app">Pengajuan izin & cuti</p>
                </div>
                <span class="rounded-full bg-amber/40 px-2.5 py-0.5 text-[11px] font-extrabold text-yellow-700">{{ stats.pendingApprovals }} Menunggu</span>
            </div>
            <div class="flex flex-1 flex-col divide-y divide-outline-app/30">
                <div v-for="leave in pendingLeaves" :key="leave.id" class="group flex flex-col gap-1.5 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-lavender text-xs font-extrabold text-purple-700">
                                {{ initials(leave.employee_name) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-ink-app">{{ leave.employee_name }}</span>
                                <span class="text-[11px] text-muted-app">{{ leave.position }} &bull; {{ leave.department }}</span>
                            </div>
                        </div>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-extrabold" :class="leaveTypeBadges[leave.type]">
                            {{ leaveTypeLabels[leave.type] }}
                        </span>
                    </div>
                    <div class="rounded-xl bg-cream p-2.5 text-xs text-muted-app">
                        <div class="mb-1 flex items-center gap-1 font-bold text-ink-app">
                            <CalendarRange class="size-3.5 text-muted-app" />
                            <span>{{ leave.start_date }} - {{ leave.end_date }} ({{ leave.days }} Hari)</span>
                        </div>
                        <p class="italic">"{{ leave.reason }}"</p>
                    </div>
                    <div v-if="canApproveLeaves" class="mt-1 flex items-center gap-2">
                        <Link
                            :href="`/leaves/${leave.id}/approve`"
                            method="post"
                            as="button"
                            class="inline-flex flex-1 items-center justify-center gap-1 rounded-xl bg-coral py-2 px-3 text-xs font-extrabold text-white transition-colors hover:bg-coral-deep"
                        >
                            <Check class="size-3.5" />
                            Setujui
                        </Link>
                        <Link
                            :href="`/leaves/${leave.id}/reject`"
                            method="post"
                            as="button"
                            class="rounded-xl border border-outline-app/50 bg-white px-4 py-2 text-xs font-bold text-muted-app transition-colors hover:border-coral hover:text-coral"
                        >
                            Tolak
                        </Link>
                    </div>
                </div>
                <div v-if="!pendingLeaves.length" class="py-6 text-center text-sm text-muted-app">
                    Tidak ada pengajuan tertunda.
                </div>
            </div>
            <div class="mt-auto pt-4 text-center">
                <Link href="/leaves" class="inline-flex items-center gap-1 text-xs font-bold text-coral hover:text-coral-deep">
                    <span>Lihat Semua Riwayat</span>
                    <ArrowRight class="size-3.5" />
                </Link>
            </div>
        </div>
    </section>

    <!-- Bottom Grid -->
    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Compliance -->
        <div class="flex flex-col justify-between rounded-2xl bg-white p-6 shadow-soft">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <ShieldCheck class="size-5 text-coral" />
                        <h3 class="text-base font-extrabold text-ink-app">Kepatuhan Regulasi</h3>
                    </div>
                    <span class="rounded-full bg-sage/50 px-2.5 py-0.5 text-[10px] font-extrabold text-green-700">Normal</span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 rounded-xl bg-cream p-3">
                        <div class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-blush/30 text-coral">
                            <ReceiptText class="size-4" />
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-bold text-ink-app">PPh 21 TER</span>
                                <span class="size-1.5 rounded-full bg-sage"></span>
                            </div>
                            <span class="text-[11px] text-muted-app">Tarif Efektif Rata-rata aktif & tervalidasi.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl bg-cream p-3">
                        <div class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-amber/30 text-amber">
                            <IdCard class="size-4" />
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-ink-app">{{ stats.expiringContracts }} PKWT Berakhir Segera</span>
                                <span v-if="stats.expiringContracts > 0" class="text-[10px] font-bold text-amber">&lt; 30 Hari</span>
                            </div>
                            <span class="text-[11px] text-muted-app">Perlu evaluasi perpanjangan kontrak.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl bg-cream p-3">
                        <div class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-lavender text-purple-700">
                            <MapPin class="size-4" />
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-bold text-ink-app">Geofence Kantor</span>
                                <span class="rounded-full bg-lavender px-2 py-0.5 text-[10px] font-extrabold text-purple-700">Aktif</span>
                            </div>
                            <span class="text-[11px] text-muted-app">Radius toleransi {{ settings.geofenceRadius }} meter aktif.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agenda -->
        <div class="flex flex-col justify-between rounded-2xl bg-white p-6 shadow-soft">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <CalendarDays class="size-5 text-coral" />
                        <h3 class="text-base font-extrabold text-ink-app">Agenda HR</h3>
                    </div>
                    <button v-if="canApproveLeaves" type="button" class="inline-flex items-center gap-1 text-xs font-bold text-coral hover:text-coral-deep">
                        <Plus class="size-3.5" />
                        Tambah
                    </button>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-xl bg-cream p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex size-11 flex-col items-center justify-center rounded-xl bg-white text-center shadow-sm">
                                <span class="text-[9px] font-extrabold text-coral uppercase">{{ todayMonth }}</span>
                                <span class="text-sm leading-none font-extrabold text-ink-app">{{ todayDate }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-ink-app">Review Kehadiran Hari Ini</span>
                                <span class="text-[11px] text-muted-app">Ringkasan absensi harian</span>
                            </div>
                        </div>
                        <span class="rounded-full bg-sage/50 px-2 py-0.5 text-[10px] font-extrabold text-green-700">Internal</span>
                    </div>
                    <div v-if="stats.cutOffDay" class="flex items-center justify-between rounded-xl bg-cream p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex size-11 flex-col items-center justify-center rounded-xl bg-white text-center shadow-sm">
                                <span class="text-[9px] font-extrabold text-amber uppercase">{{ nextMonthMonth }}</span>
                                <span class="text-sm leading-none font-extrabold text-amber">{{ nextMonthDay }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-ink-app">Cut-off Payroll</span>
                                <span class="text-[11px] text-muted-app">Batas approval lembur</span>
                            </div>
                        </div>
                        <span class="rounded-full bg-amber/40 px-2 py-0.5 text-[10px] font-extrabold text-yellow-700">Kritikal</span>
                    </div>
                    <div v-if="stats.expiringContracts > 0" class="flex items-center justify-between rounded-xl bg-cream p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex size-11 flex-col items-center justify-center rounded-xl bg-white text-center shadow-sm">
                                <span class="text-[9px] font-extrabold text-muted-app uppercase">{{ in30DaysMonth }}</span>
                                <span class="text-sm leading-none font-extrabold text-ink-app">{{ in30DaysDay }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-ink-app">Evaluasi PKWT</span>
                                <span class="text-[11px] text-muted-app">{{ stats.expiringContracts }} kontrak berakhir segera</span>
                            </div>
                        </div>
                        <span class="rounded-full bg-blush/40 px-2 py-0.5 text-[10px] font-extrabold text-coral-deep">Perlu Action</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </AppLayout>
</template>
