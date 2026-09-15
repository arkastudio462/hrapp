<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Printer } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    payroll: Object,
});

const page = usePage();
const isEmployee = computed(() => page.props.auth?.user?.role === 'employee');

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

    <component :is="isEmployee ? EmployeeLayout : AppLayout" :title="'Payslip'">
        <div class="flex items-center justify-between">
            <Link
                :href="isEmployee ? '/my-payslip' : '/payroll'"
                class="inline-flex items-center gap-1 text-sm font-bold text-muted-app transition-colors hover:text-ink-app"
            >
                <ArrowLeft class="size-4" />
                Kembali
            </Link>
            <button
                type="button"
                class="no-print inline-flex items-center gap-2 rounded-full border border-outline-app px-4 py-2 text-sm font-bold text-ink-app transition-colors hover:bg-cream"
                @click="printPayslip"
            >
                <Printer class="size-4" />
                Cetak
            </button>
        </div>

        <div class="mx-auto mt-4 max-w-xl">
            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <!-- Header -->
                <div class="border-b border-outline-app/50 pb-5 text-center">
                    <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">PAYSLIP</h1>
                    <p class="mt-1 text-sm text-muted-app">Periode: {{ months[payroll.period?.month - 1] }} {{ payroll.period?.year }}</p>
                </div>

                <!-- Employee Info -->
                <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Nama Karyawan</p>
                        <p class="font-bold text-ink-app">{{ payroll.employee?.name }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">NIK</p>
                        <p class="font-bold text-ink-app">{{ payroll.employee?.nik }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Departemen</p>
                        <p class="font-bold text-ink-app">{{ payroll.employee?.department?.name || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Jabatan</p>
                        <p class="font-bold text-ink-app">{{ payroll.employee?.position?.name || '-' }}</p>
                    </div>
                </div>

                <!-- Earnings -->
                <div class="mt-6">
                    <h3 class="border-b border-outline-app/50 pb-2 text-sm font-extrabold text-ink-app">PENDAPATAN</h3>
                    <div class="mt-2 space-y-2">
                        <div v-for="item in earnings" :key="item.id" class="flex justify-between text-sm">
                            <span class="text-ink-app">{{ item.description }}</span>
                            <span class="font-bold text-ink-app">{{ formatCurrency(item.amount) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-outline-app/50 pt-2 text-sm font-extrabold">
                            <span class="text-ink-app">Total Pendapatan</span>
                            <span class="text-ink-app">{{ formatCurrency(payroll.total_earnings) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Deductions -->
                <div class="mt-6">
                    <h3 class="border-b border-outline-app/50 pb-2 text-sm font-extrabold text-ink-app">POTONGAN</h3>
                    <div class="mt-2 space-y-2">
                        <div v-for="item in deductions" :key="item.id" class="flex justify-between text-sm">
                            <span class="text-ink-app">{{ item.description }}</span>
                            <span class="font-bold text-coral-deep">{{ formatCurrency(item.amount) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-outline-app/50 pt-2 text-sm font-extrabold">
                            <span class="text-ink-app">Total Potongan</span>
                            <span class="text-coral-deep">{{ formatCurrency(payroll.total_deductions) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Net Salary -->
                <div class="mt-6 rounded-2xl bg-lavender/50 p-4">
                    <div class="flex justify-between">
                        <span class="text-lg font-extrabold text-ink-app">GAJI BERSIH</span>
                        <span class="text-lg font-extrabold text-ink-app">{{ formatCurrency(payroll.net_salary) }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center text-xs text-muted-app">
                    <p>Dokumen ini merupakan bukti pembayaran gaji yang sah.</p>
                    <p class="mt-1">Dicetak pada: {{ new Date().toLocaleDateString('id-ID') }}</p>
                </div>
            </div>
        </div>
    </component>
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
