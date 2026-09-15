<script setup>
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, Wallet } from 'lucide-vue-next';

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

    <EmployeeLayout title="Payslip">
        <h1 class="text-xl font-extrabold tracking-tight text-ink-app">Payslip Saya</h1>
        <p class="mt-0.5 text-sm text-muted-app">Riwayat slip gaji kamu</p>

        <!-- Payslip list -->
        <section class="mt-4 space-y-3 animate-fade-up">
            <Link
                v-for="payroll in payslips.data"
                :key="payroll.id"
                :href="`/my-payslip/${payroll.id}`"
                class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-soft transition-all active:scale-[0.98]"
            >
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-lavender/60 text-purple-700">
                    <Wallet class="size-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-ink-app">{{ months[payroll.period?.month - 1] }} {{ payroll.period?.year }}</p>
                    <p class="mt-0.5 text-xs text-muted-app">
                        <span :class="['mr-1.5 rounded-full px-2 py-0.5 text-[10px] font-extrabold', payroll.status === 'paid' ? 'bg-sage/60 text-green-700' : 'bg-cream text-muted-app']">
                            {{ payroll.status === 'paid' ? 'Dibayar' : 'Draft' }}
                        </span>
                    </p>
                </div>
                <div class="shrink-0 text-right">
                    <p class="text-sm font-extrabold text-ink-app">{{ formatCurrency(payroll.net_salary) }}</p>
                </div>
                <ChevronRight class="size-4 shrink-0 text-muted-app" />
            </Link>
            <div v-if="!payslips.data.length" class="rounded-2xl bg-white p-8 text-center shadow-soft">
                <p class="text-sm font-bold text-ink-app">Belum ada payslip</p>
            </div>
        </section>

        <!-- Pagination -->
        <div v-if="payslips.last_page > 1" class="mt-5 flex justify-center gap-1">
            <Link
                v-for="page in payslips.last_page"
                :key="page"
                :href="payslips.path + '?page=' + page"
                :class="[
                    'px-3 py-1.5 text-sm font-bold rounded-lg',
                    page === payslips.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-white',
                ]"
            >
                {{ page }}
            </Link>
        </div>
    </EmployeeLayout>
</template>
