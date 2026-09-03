<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Download } from 'lucide-vue-next';

const props = defineProps({
    payslips: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];
</script>

<template>
    <Head title="Payslip Saya" />

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
                        <Link href="/my-dashboard" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Dashboard</Link>
                        <Link href="/my-attendance" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Absensi</Link>
                        <Link href="/my-leave" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Izin/Cuti</Link>
                        <Link href="/my-payslip" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Payslip</Link>
                        <Link href="/my-profile" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Profil</Link>
                        <Link href="/face-attendance" class="rounded-lg bg-ink px-3 py-2 text-sm font-medium text-white">Absen Wajah</Link>
                    </nav>
                </div>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Payslip Saya</h1>

                <!-- Table -->
                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-stone-100 bg-stone-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Periode</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Gaji Bersih</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="payroll in payslips.data" :key="payroll.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                                <td class="px-4 py-3 text-sm font-semibold text-ink">{{ months[payroll.period?.month - 1] }} {{ payroll.period?.year }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-ink">{{ formatCurrency(payroll.net_salary) }}</td>
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', payroll.status === 'paid' ? 'bg-moss-50 text-moss-700' : 'bg-stone-100 text-inkmuted']">
                                        {{ payroll.status === 'paid' ? 'Dibayar' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="`/payroll/payslip/${payroll.id}`"
                                        class="inline-flex items-center gap-1 rounded-full border border-stone-300 px-3 py-1 text-xs font-semibold text-ink hover:bg-stone-50"
                                    >
                                        <Download class="h-3 w-3" />
                                        Lihat
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!payslips.data.length">
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-inkmuted">Belum ada payslip.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="payslips.last_page > 1" class="mt-4 flex justify-center gap-1">
                    <Link
                        v-for="page in payslips.last_page"
                        :key="page"
                        :href="payslips.path + '?page=' + page"
                        :class="[
                            'px-3 py-1.5 text-sm font-medium rounded-lg',
                            page === payslips.current_page ? 'bg-ink text-white' : 'text-inkmuted hover:bg-stone-100',
                        ]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
