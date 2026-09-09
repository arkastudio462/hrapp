<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import {
    Clock,
    Calendar,
    Wallet,
    ArrowRight,
    BadgeCheck,
    FileText,
    UserCircle,
} from 'lucide-vue-next';

const props = defineProps({
    employee: Object,
    todayAttendance: Object,
    leaveBalances: Array,
    recentPayroll: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const getBalance = (type) => {
    const balance = props.leaveBalances.find(b => b.type === type);
    return balance ? balance.remaining : 0;
};

const statusLabels = {
    present: 'Hadir',
    late: 'Terlambat',
    absent: 'Tidak Hadir',
};

const statusColors = {
    present: 'bg-status-success/10 text-status-success',
    late: 'bg-status-warning/10 text-status-warning',
    absent: 'bg-status-error/10 text-status-error',
};

const todayLabel = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});
</script>

<template>
    <Head title="Dashboard Saya" />

    <AppLayout title="Dashboard Saya">
    <!-- Welcome Banner -->
    <section class="rounded-xl bg-surface-card p-6 shadow-sm">
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-1.5">
                <span class="rounded-full bg-surface-container px-2 py-0.5 text-xs font-semibold text-primary">Portal Karyawan</span>
                <span class="text-xs text-on-surface-variant">•</span>
                <span class="text-xs text-on-surface-variant">{{ todayLabel }}</span>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-on-surface">Selamat datang, {{ employee?.name || user?.name || 'Karyawan' }} 👋</h1>
            <p class="text-sm text-on-surface-variant">
                {{ employee?.department?.name }} - {{ employee?.position?.name }}
            </p>
        </div>
    </section>
    <!-- Quick Actions -->
    <section class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <Link href="/face-attendance" class="flex items-center gap-3 rounded-xl bg-primary p-4 text-on-primary shadow-sm transition-all hover:bg-primary-container">
            <BadgeCheck class="h-5 w-5" />
            <span class="text-sm font-medium">Absen Wajah</span>
        </Link>
        <Link href="/my-leave" class="flex items-center gap-3 rounded-xl bg-surface-card p-4 shadow-sm transition-colors hover:bg-surface-subtle">
            <FileText class="h-5 w-5 text-primary" />
            <span class="text-sm font-medium text-on-surface">Ajukan Izin</span>
        </Link>
        <Link href="/my-payslip" class="flex items-center gap-3 rounded-xl bg-surface-card p-4 shadow-sm transition-colors hover:bg-surface-subtle">
            <Wallet class="h-5 w-5 text-primary" />
            <span class="text-sm font-medium text-on-surface">Lihat Payslip</span>
        </Link>
        <Link href="/my-profile" class="flex items-center gap-3 rounded-xl bg-surface-card p-4 shadow-sm transition-colors hover:bg-surface-subtle">
            <UserCircle class="h-5 w-5 text-primary" />
            <span class="text-sm font-medium text-on-surface">Profil Saya</span>
        </Link>
    </section>
    <!-- Today's Attendance -->
    <section class="rounded-xl bg-surface-card p-6 shadow-sm">
        <h2 class="mb-4 text-base font-semibold text-on-surface">Absensi Hari Ini</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-lg bg-surface-subtle p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-container-low text-primary">
                        <Clock class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant">Jam Masuk</p>
                        <p class="text-sm font-semibold text-on-surface">{{ todayAttendance?.check_in_time ? new Date(todayAttendance.check_in_time).toLocaleTimeString('id-ID') : '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg bg-surface-subtle p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-container-low text-primary">
                        <Clock class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant">Jam Pulang</p>
                        <p class="text-sm font-semibold text-on-surface">{{ todayAttendance?.check_out_time ? new Date(todayAttendance.check_out_time).toLocaleTimeString('id-ID') : '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg bg-surface-subtle p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-container-low text-primary">
                        <Calendar class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant">Status</p>
                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[todayAttendance?.status || 'absent']]">
                            {{ statusLabels[todayAttendance?.status || 'absent'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Leave Balances & Recent Payroll -->
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl bg-surface-card p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-on-surface">Saldo Izin & Cuti</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between rounded-lg bg-surface-subtle p-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-status-success/10">
                            <Calendar class="h-4 w-4 text-status-success" />
                        </div>
                        <span class="text-sm text-on-surface">Cuti Tahunan</span>
                    </div>
                    <span class="text-sm font-semibold text-on-surface">{{ getBalance('annual') }} hari</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-surface-subtle p-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-status-warning/10">
                            <Calendar class="h-4 w-4 text-status-warning" />
                        </div>
                        <span class="text-sm text-on-surface">Izin Sakit</span>
                    </div>
                    <span class="text-sm font-semibold text-on-surface">{{ getBalance('sick') }} hari</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-surface-subtle p-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-status-info/10">
                            <Calendar class="h-4 w-4 text-status-info" />
                        </div>
                        <span class="text-sm text-on-surface">Izin Pribadi</span>
                    </div>
                    <span class="text-sm font-semibold text-on-surface">{{ getBalance('personal') }} hari</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-surface-card p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-on-surface">Gaji Terakhir</h2>
            <div v-if="recentPayroll" class="rounded-lg bg-surface-subtle p-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-xs text-on-surface-variant">Periode</span>
                    <span class="text-xs font-semibold text-on-surface">{{ recentPayroll.period?.month }}/{{ recentPayroll.period?.year }}</span>
                </div>
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-xs text-on-surface-variant">Gaji Bersih</span>
                    <span class="text-lg font-bold text-on-surface">{{ formatCurrency(recentPayroll.net_salary) }}</span>
                </div>
                <Link href="/my-payslip" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-container">
                    <span>Lihat Detail</span>
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
            <div v-else class="py-6 text-center text-sm text-on-surface-variant">
                Belum ada data gaji.
            </div>
        </div>
    </section>
    </AppLayout>
</template>
