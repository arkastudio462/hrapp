<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Download } from 'lucide-vue-next';

const props = defineProps({
    period: Object,
    stats: Object,
});

const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const statusLabels = {
    draft: 'Draft',
    processing: 'Diproses',
    completed: 'Selesai',
};

const statusColors = {
    draft: 'bg-stone-100 text-inkmuted',
    processing: 'bg-yellow-50 text-yellow-700',
    completed: 'bg-moss-50 text-moss-700',
};

const payrollStatusLabels = {
    draft: 'Draft',
    paid: 'Dibayar',
};

const payrollStatusColors = {
    draft: 'bg-stone-100 text-inkmuted',
    paid: 'bg-moss-50 text-moss-700',
};
</script>

<template>
    <Head title="Detail Penggajian" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <Link href="/payroll" class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="font-display text-2xl font-bold tracking-tight text-ink">
                            {{ months[period.month - 1] }} {{ period.year }}
                        </h1>
                        <span :class="['mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold', statusColors[period.status]]">
                            {{ statusLabels[period.status] }}
                        </span>
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <p class="text-sm text-inkmuted">Total Karyawan</p>
                        <p class="mt-1 font-display text-2xl font-bold text-ink">{{ stats.total_earnings > 0 ? period.payrolls?.length || 0 : 0 }}</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <p class="text-sm text-inkmuted">Total Earnings</p>
                        <p class="mt-1 font-display text-2xl font-bold text-moss-700">{{ formatCurrency(stats.total_earnings) }}</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <p class="text-sm text-inkmuted">Total Deductions</p>
                        <p class="mt-1 font-display text-2xl font-bold text-red-600">{{ formatCurrency(stats.total_deductions) }}</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <p class="text-sm text-inkmuted">Net Salary</p>
                        <p class="mt-1 font-display text-2xl font-bold text-ink">{{ formatCurrency(stats.net_salary) }}</p>
                    </div>
                </div>

                <!-- Payslip List -->
                <div class="mt-8 overflow-hidden rounded-xl border border-stone-200 bg-white">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-stone-100 bg-stone-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">NIK</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Departemen</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Earnings</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Deductions</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Net Salary</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="payroll in period.payrolls" :key="payroll.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                                <td class="px-4 py-3 text-sm font-mono text-inkmuted">{{ payroll.employee?.nik }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-ink">{{ payroll.employee?.name }}</td>
                                <td class="px-4 py-3 text-sm text-inkmuted">{{ payroll.employee?.department?.name || '-' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-moss-700">{{ formatCurrency(payroll.total_earnings) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-red-600">{{ formatCurrency(payroll.total_deductions) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-ink">{{ formatCurrency(payroll.net_salary) }}</td>
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', payrollStatusColors[payroll.status]]">
                                        {{ payrollStatusLabels[payroll.status] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="`/payroll/payslip/${payroll.id}`"
                                        class="inline-flex items-center gap-1 rounded-full border border-stone-300 px-3 py-1 text-xs font-semibold text-ink hover:bg-stone-50"
                                    >
                                        <Download class="h-3 w-3" />
                                        Payslip
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!period.payrolls?.length">
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-inkmuted">Belum ada data gajian.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
