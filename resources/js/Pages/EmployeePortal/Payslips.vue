<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
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

    <AppLayout title="Payslip Saya">
        <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Payslip Saya</h1>

        <!-- Table -->
        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100 shadow-sm bg-white">
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
                    page === payslips.current_page ? 'bg-sky-600 text-white' : 'text-inkmuted hover:bg-stone-100',
                ]"
            >
                {{ page }}
            </Link>
        </div>
    </AppLayout>
</template>
