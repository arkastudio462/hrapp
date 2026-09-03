<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Clock, Calendar, Wallet, User } from 'lucide-vue-next';

const props = defineProps({
    employee: Object,
    todayAttendance: Object,
    leaveBalances: Array,
    recentPayroll: Object,
});

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
    absent: 'Alpa',
};

const statusColors = {
    present: 'bg-moss-50 text-moss-700',
    late: 'bg-yellow-50 text-yellow-700',
    absent: 'bg-red-50 text-red-700',
};
</script>

<template>
    <Head title="Dashboard Saya" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <Link href="/my-dashboard" class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                            <span class="h-2 w-2 rounded-[1px] bg-white" />
                        </span>
                        <span class="font-display text-lg font-bold tracking-tight">HRapp</span>
                    </Link>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link href="/my-dashboard" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Dashboard</Link>
                        <Link href="/my-attendance" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Absensi</Link>
                        <Link href="/my-leave" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Izin/Cuti</Link>
                        <Link href="/my-payslip" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Payslip</Link>
                        <Link href="/my-profile" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Profil</Link>
                        <Link href="/face-attendance" class="rounded-lg bg-ink px-3 py-2 text-sm font-medium text-white">Absen Wajah</Link>
                    </nav>
                </div>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Selamat Datang, {{ employee?.name || 'Karyawan' }}</h1>
                <p class="mt-1 text-sm text-inkmuted">{{ employee?.department?.name }} - {{ employee?.position?.name }}</p>

                <!-- Today's Attendance -->
                <div class="mt-6 rounded-xl border border-stone-200 bg-white p-6">
                    <h2 class="font-display text-lg font-bold text-ink">Absensi Hari Ini</h2>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-xl bg-stone-50 p-4">
                            <div class="flex items-center gap-3">
                                <Clock class="h-5 w-5 text-inkmuted" />
                                <div>
                                    <p class="text-sm text-inkmuted">Jam Masuk</p>
                                    <p class="font-semibold text-ink">{{ todayAttendance?.check_in_time ? new Date(todayAttendance.check_in_time).toLocaleTimeString('id-ID') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl bg-stone-50 p-4">
                            <div class="flex items-center gap-3">
                                <Clock class="h-5 w-5 text-inkmuted" />
                                <div>
                                    <p class="text-sm text-inkmuted">Jam Pulang</p>
                                    <p class="font-semibold text-ink">{{ todayAttendance?.check_out_time ? new Date(todayAttendance.check_out_time).toLocaleTimeString('id-ID') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl bg-stone-50 p-4">
                            <div class="flex items-center gap-3">
                                <Calendar class="h-5 w-5 text-inkmuted" />
                                <div>
                                    <p class="text-sm text-inkmuted">Status</p>
                                    <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[todayAttendance?.status || 'absent']]">
                                        {{ statusLabels[todayAttendance?.status || 'absent'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Balances -->
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-moss-50">
                                <Calendar class="h-5 w-5 text-moss-700" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Cuti Tersisa</p>
                                <p class="font-display text-2xl font-bold">{{ getBalance('annual') }} hari</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-50">
                                <Calendar class="h-5 w-5 text-yellow-700" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Sakit Tersisa</p>
                                <p class="font-display text-2xl font-bold">{{ getBalance('sick') }} hari</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-stone-100">
                                <Wallet class="h-5 w-5 text-ink" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Gaji Terakhir</p>
                                <p class="font-display text-2xl font-bold">{{ recentPayroll ? formatCurrency(recentPayroll.net_salary) : '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 rounded-xl border border-stone-200 bg-white p-6">
                    <h2 class="font-display text-lg font-bold text-ink">Aksi Cepat</h2>
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <Link href="/face-attendance" class="rounded-xl bg-ink p-4 text-center text-sm font-semibold text-white hover:bg-moss-700">
                            Absen Wajah
                        </Link>
                        <Link href="/my-leave/create" class="rounded-xl border border-stone-300 p-4 text-center text-sm font-semibold text-ink hover:bg-stone-50">
                            Ajukan Izin
                        </Link>
                        <Link href="/my-payslip" class="rounded-xl border border-stone-300 p-4 text-center text-sm font-semibold text-ink hover:bg-stone-50">
                            Lihat Payslip
                        </Link>
                        <Link href="/my-profile" class="rounded-xl border border-stone-300 p-4 text-center text-sm font-semibold text-ink hover:bg-stone-50">
                            Profil Saya
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
