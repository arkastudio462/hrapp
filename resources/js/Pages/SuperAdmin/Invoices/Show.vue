<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    invoice: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const statusLabels = {
    pending: 'Menunggu',
    paid: 'Dibayar',
    overdue: 'Jatuh Tempo',
};

const statusColors = {
    pending: 'bg-amber/40 text-yellow-700',
    paid: 'bg-sage/50 text-green-700',
    overdue: 'bg-coral-deep/10 text-coral-deep',
};

const markAsPaid = () => {
    if (confirm('Tandai invoice ini sebagai sudah dibayar?')) {
        router.put(`/super-admin/invoices/${props.invoice.id}/status`, {
            status: 'paid',
            payment_method: 'manual',
        });
    }
};

const markAsOverdue = () => {
    if (confirm('Tandai invoice ini sebagai jatuh tempo?')) {
        router.put(`/super-admin/invoices/${props.invoice.id}/status`, {
            status: 'overdue',
        });
    }
};
</script>

<template>
    <Head title="Detail Invoice" />

    <AppLayout title="Detail Invoice">
    <Link href="/super-admin/invoices" class="inline-flex items-center gap-1 text-sm font-bold text-muted-app hover:text-ink-app">
        <ArrowLeft class="h-4 w-4" />
        Kembali
    </Link>

    <div class="rounded-2xl bg-white p-6 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-ink-app">Invoice {{ invoice.invoice_number }}</h2>
                <span :class="['mt-2 inline-flex rounded-full px-3 py-1 text-sm font-extrabold', statusColors[invoice.status]]">
                    {{ statusLabels[invoice.status] }}
                </span>
            </div>
            <div v-if="invoice.status !== 'paid'" class="flex gap-2">
                <button
                    @click="markAsPaid"
                    class="rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-green-700"
                >
                    Tandai Dibayar
                </button>
                <button
                    v-if="invoice.status === 'pending'"
                    @click="markAsOverdue"
                    class="rounded-xl bg-coral-deep px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-coral-deep/90"
                >
                    Tandai Jatuh Tempo
                </button>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-muted-app">Tenant</p>
                    <p class="text-sm font-semibold text-ink-app">{{ invoice.tenant.name }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-app">Paket</p>
                    <p class="text-sm font-semibold text-ink-app">{{ invoice.package.name }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-app">Jumlah</p>
                    <p class="text-xl font-extrabold text-ink-app">{{ formatCurrency(invoice.amount) }}</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-muted-app">Tanggal Dibuat</p>
                    <p class="text-sm font-semibold text-ink-app">{{ invoice.created_at }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-app">Jatuh Tempo</p>
                    <p class="text-sm font-semibold text-ink-app">{{ invoice.due_date }}</p>
                </div>
                <div v-if="invoice.paid_at">
                    <p class="text-sm text-muted-app">Dibayar Pada</p>
                    <p class="text-sm font-semibold text-ink-app">{{ invoice.paid_at }}</p>
                </div>
                <div v-if="invoice.payment_method">
                    <p class="text-sm text-muted-app">Metode Pembayaran</p>
                    <p class="text-sm font-semibold text-ink-app">{{ invoice.payment_method }}</p>
                </div>
            </div>
        </div>
    </div>
    </AppLayout>
</template>
