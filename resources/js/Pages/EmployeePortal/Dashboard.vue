<script setup>
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import {
    Clock,
    Calendar,
    Wallet,
    ArrowRight,
    ScanFace,
    FileText,
    LogIn,
    LogOut,
} from 'lucide-vue-next';

const props = defineProps({
    employee: Object,
    todayAttendance: Object,
    leaveBalances: Array,
    recentPayroll: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const tenant = computed(() => page.props.tenant);
const isFaceLocation = computed(() => (tenant.value?.attendance_method || 'face_location') === 'face_location');

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
    present: 'bg-sage/60 text-green-700',
    late: 'bg-amber/40 text-yellow-700',
    absent: 'bg-blush/60 text-coral-deep',
};

const todayLabel = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

const firstName = computed(() => {
    const name = props.employee?.name || user.value?.name || 'Karyawan';
    return name.split(' ')[0];
});
</script>

<template>
    <Head title="Dashboard Saya" />

    <EmployeeLayout title="Beranda">
        <!-- Greeting -->
        <section class="relative overflow-hidden rounded-2xl bg-lavender p-5 shadow-soft animate-fade-up">
            <div class="pointer-events-none absolute -right-6 -bottom-8 size-32 rounded-full bg-white/30"></div>
            <div class="relative">
                <p class="text-xs font-semibold text-ink-app/60">{{ todayLabel }}</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-ink-app">Halo, {{ firstName }} 👋</h1>
                <p class="mt-1 text-sm text-ink-app/70">
                    {{ employee?.department?.name }} · {{ employee?.position?.name }}
                </p>

                <!-- Check-in status -->
                <div class="mt-4 flex items-center gap-3 rounded-xl bg-white/70 p-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-white/80 text-coral">
                        <LogIn class="size-5" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold text-ink-app/60">Presensi Hari Ini</p>
                        <p class="text-sm font-extrabold text-ink-app">
                            {{ todayAttendance?.check_in_time ? `Masuk ${new Date(todayAttendance.check_in_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}` : 'Belum absen masuk' }}
                        </p>
                    </div>
                    <span :class="['rounded-full px-2.5 py-1 text-[10px] font-extrabold', statusColors[todayAttendance?.status || 'absent']]">
                        {{ statusLabels[todayAttendance?.status || 'absent'] }}
                    </span>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="mt-4 grid gap-3 animate-fade-up" :class="isFaceLocation ? 'grid-cols-3' : 'grid-cols-2'" style="animation-delay: 80ms">
            <Link v-if="isFaceLocation" href="/face-attendance" class="flex flex-col items-center gap-2 rounded-2xl bg-coral p-4 text-white shadow-soft transition-all active:scale-95">
                <ScanFace class="size-6" />
                <span class="text-xs font-extrabold">Absen Wajah</span>
            </Link>
            <Link href="/my-leave" class="flex flex-col items-center gap-2 rounded-2xl bg-white p-4 shadow-soft transition-all active:scale-95">
                <FileText class="size-6 text-coral" />
                <span class="text-xs font-extrabold text-ink-app">Ajukan Izin</span>
            </Link>
            <Link href="/my-payslip" class="flex flex-col items-center gap-2 rounded-2xl bg-white p-4 shadow-soft transition-all active:scale-95">
                <Wallet class="size-6 text-coral" />
                <span class="text-xs font-extrabold text-ink-app">Payslip</span>
            </Link>
        </section>

        <!-- Attendance detail -->
        <section class="mt-5 animate-fade-up" style="animation-delay: 140ms">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-extrabold text-ink-app">Absensi Hari Ini</h2>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3">
                <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-soft">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-sage/60 text-green-700">
                        <LogIn class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Jam Masuk</p>
                        <p class="text-sm font-extrabold text-ink-app">
                            {{ todayAttendance?.check_in_time ? new Date(todayAttendance.check_in_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-soft">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-lavender/60 text-purple-700">
                        <LogOut class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Jam Pulang</p>
                        <p class="text-sm font-extrabold text-ink-app">
                            {{ todayAttendance?.check_out_time ? new Date(todayAttendance.check_out_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Leave balances -->
        <section class="mt-5 animate-fade-up" style="animation-delay: 200ms">
            <h2 class="text-base font-extrabold text-ink-app">Saldo Izin & Cuti</h2>
            <div class="mt-3 space-y-3">
                <div class="flex items-center justify-between rounded-2xl bg-white p-4 shadow-soft">
                    <div class="flex items-center gap-3">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-sage/60 text-green-700">
                            <Calendar class="size-5" />
                        </span>
                        <span class="text-sm font-bold text-ink-app">Cuti Tahunan</span>
                    </div>
                    <span class="text-sm font-extrabold text-coral">{{ getBalance('annual') }} hari</span>
                </div>
                <div class="flex items-center justify-between rounded-2xl bg-white p-4 shadow-soft">
                    <div class="flex items-center gap-3">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-amber/40 text-yellow-700">
                            <Calendar class="size-5" />
                        </span>
                        <span class="text-sm font-bold text-ink-app">Izin Sakit</span>
                    </div>
                    <span class="text-sm font-extrabold text-coral">{{ getBalance('sick') }} hari</span>
                </div>
                <div class="flex items-center justify-between rounded-2xl bg-white p-4 shadow-soft">
                    <div class="flex items-center gap-3">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-blush/50 text-coral-deep">
                            <Calendar class="size-5" />
                        </span>
                        <span class="text-sm font-bold text-ink-app">Cuti Melahirkan</span>
                    </div>
                    <span class="text-sm font-extrabold text-coral">{{ getBalance('maternity') }} hari</span>
                </div>
            </div>
        </section>

        <!-- Recent payroll -->
        <section class="mt-5 animate-fade-up" style="animation-delay: 260ms">
            <h2 class="text-base font-extrabold text-ink-app">Gaji Terakhir</h2>
            <div v-if="recentPayroll" class="mt-3 rounded-2xl bg-white p-4 shadow-soft">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Periode {{ recentPayroll.period?.month }}/{{ recentPayroll.period?.year }}</p>
                        <p class="mt-0.5 text-xl font-extrabold tracking-tight text-ink-app">{{ formatCurrency(recentPayroll.net_salary) }}</p>
                    </div>
                    <Link href="/my-payslip" class="inline-flex items-center gap-1 rounded-full bg-coral/10 px-3 py-1.5 text-xs font-bold text-coral transition-colors hover:bg-coral/20">
                        Detail
                        <ArrowRight class="size-3.5" />
                    </Link>
                </div>
            </div>
            <div v-else class="mt-3 rounded-2xl bg-white p-6 text-center shadow-soft">
                <p class="text-sm font-bold text-ink-app">Belum ada data gaji</p>
            </div>
        </section>
    </EmployeeLayout>
</template>
