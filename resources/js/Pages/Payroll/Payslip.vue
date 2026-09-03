<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Download, Printer } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    payroll: Object,
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

const printPayslip = () => {
    window.print();
};

const earnings = props.payroll.items?.filter(item => item.type === 'earning') || [];
const deductions = props.payroll.items?.filter(item => item.type === 'deduction') || [];
</script>

<template>
    <Head title="Payslip" />

    <div class="min-h-screen bg-stone-50">
        <header class="no-print sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/payroll" class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
                <button
                    class="inline-flex items-center gap-2 rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-ink hover:bg-stone-50"
                    @click="printPayslip"
                >
                    <Printer class="h-4 w-4" />
                    Cetak
                </button>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-xl border border-stone-200 bg-white p-8 shadow-sm">
                    <!-- Header -->
                    <div class="text-center border-b border-stone-200 pb-6">
                        <h1 class="font-display text-2xl font-bold text-ink">PAYSLIP</h1>
                        <p class="mt-1 text-sm text-inkmuted">Periode: {{ months[payroll.period?.month - 1] }} {{ payroll.period?.year }}</p>
                    </div>

                    <!-- Employee Info -->
                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-inkmuted">Nama Karyawan</p>
                            <p class="font-semibold text-ink">{{ payroll.employee?.name }}</p>
                        </div>
                        <div>
                            <p class="text-inkmuted">NIK</p>
                            <p class="font-semibold text-ink">{{ payroll.employee?.nik }}</p>
                        </div>
                        <div>
                            <p class="text-inkmuted">Departemen</p>
                            <p class="font-semibold text-ink">{{ payroll.employee?.department?.name || '-' }}</p>
                        </div>
                        <div>
                            <p class="text-inkmuted">Jabatan</p>
                            <p class="font-semibold text-ink">{{ payroll.employee?.position?.name || '-' }}</p>
                        </div>
                    </div>

                    <!-- Earnings -->
                    <div class="mt-8">
                        <h3 class="text-sm font-bold text-ink border-b border-stone-200 pb-2">PENDAPATAN</h3>
                        <div class="mt-2 space-y-2">
                            <div v-for="item in earnings" :key="item.id" class="flex justify-between text-sm">
                                <span class="text-ink">{{ item.description }}</span>
                                <span class="font-semibold text-ink">{{ formatCurrency(item.amount) }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold border-t border-stone-200 pt-2">
                                <span class="text-ink">Total Pendapatan</span>
                                <span class="text-ink">{{ formatCurrency(payroll.total_earnings) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="mt-6">
                        <h3 class="text-sm font-bold text-ink border-b border-stone-200 pb-2">POTONGAN</h3>
                        <div class="mt-2 space-y-2">
                            <div v-for="item in deductions" :key="item.id" class="flex justify-between text-sm">
                                <span class="text-ink">{{ item.description }}</span>
                                <span class="font-semibold text-red-600">{{ formatCurrency(item.amount) }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold border-t border-stone-200 pt-2">
                                <span class="text-ink">Total Potongan</span>
                                <span class="text-red-600">{{ formatCurrency(payroll.total_deductions) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Net Salary -->
                    <div class="mt-8 rounded-xl bg-stone-50 p-4">
                        <div class="flex justify-between">
                            <span class="text-lg font-bold text-ink">GAJI BERSIH</span>
                            <span class="text-lg font-bold text-ink">{{ formatCurrency(payroll.net_salary) }}</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-8 text-center text-xs text-inkmuted">
                        <p>Dokumen ini merupakan bukti pembayaran gaji yang sah.</p>
                        <p class="mt-1">Dicetak pada: {{ new Date().toLocaleDateString('id-ID') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: white;
    }
}
</style>
