<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CreditCard, Package, History, ExternalLink } from 'lucide-vue-next';

const props = defineProps({
    tenant: Object,
    invoices: Object,
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
    failed: 'Gagal',
    cancelled: 'Dibatalkan',
};

const statusColors = {
    pending: 'bg-amber/40 text-yellow-700',
    paid: 'bg-sage/50 text-green-700',
    failed: 'bg-coral-deep/10 text-coral-deep',
    cancelled: 'bg-cream text-muted-app',
};

const subscriptionStatusLabels = {
    active: 'Aktif',
    trial: 'Trial',
    suspended: 'Suspended',
    cancelled: 'Dibatalkan',
};

const subscriptionStatusColors = {
    active: 'bg-sage/50 text-green-700',
    trial: 'bg-amber/40 text-yellow-700',
    suspended: 'bg-coral-deep/10 text-coral-deep',
    cancelled: 'bg-cream text-muted-app',
};
</script>

<template>
    <Head title="Subscription" />

    <AppLayout title="Billing & Langganan">
    <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Subscription & Billing</h1>
    <!-- Current Plan -->
    <div class="mt-6 rounded-2xl bg-white p-6 shadow-soft">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-ink-app">Paket Saat Ini</h2>
                <p class="mt-1 text-sm text-muted-app">{{ tenant.name }}</p>
            </div>
            <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', subscriptionStatusColors[tenant.subscription_status]]">
                {{ subscriptionStatusLabels[tenant.subscription_status] }}
            </span>
        </div>
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-cream p-4">
                <div class="flex items-center gap-3">
                    <Package class="h-5 w-5 text-muted-app" />
                    <div>
                        <p class="text-sm text-muted-app">Paket</p>
                        <p class="font-bold text-ink-app">{{ tenant.package?.name || '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-cream p-4">
                <div class="flex items-center gap-3">
                    <CreditCard class="h-5 w-5 text-muted-app" />
                    <div>
                        <p class="text-sm text-muted-app">Harga</p>
                        <p class="font-bold text-ink-app">{{ tenant.package?.price ? formatCurrency(tenant.package.price) + '/bulan' : '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-cream p-4">
                <div class="flex items-center gap-3">
                    <History class="h-5 w-5 text-muted-app" />
                    <div>
                        <p class="text-sm text-muted-app">Trial Berakhir</p>
                        <p class="font-bold text-ink-app">{{ tenant.trial_ends_at || '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <Link
                href="/subscription/packages"
                class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white shadow-md transition-colors hover:bg-coral-deep"
            >
                <ExternalLink class="h-4 w-4" />
                Upgrade Paket
            </Link>
        </div>
    </div>
    <!-- Invoice History -->
    <div class="mt-8">
        <h2 class="text-lg font-extrabold text-ink-app">Riwayat Invoice</h2>
        <div class="max-h-[600px] mt-4 overflow-auto rounded-2xl bg-white shadow-soft">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-app/30 bg-cream">
                        <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Invoice</th>
                        <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Deskripsi</th>
                        <th class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Jumlah</th>
                        <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Status</th>
                        <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                        <td class="px-4 py-3 text-sm font-mono text-muted-app">{{ invoice.invoice_number || `INV-${invoice.id}` }}</td>
                        <td class="px-4 py-3 text-sm text-ink-app">{{ invoice.description || invoice.package?.name || '-' }}</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-ink-app">{{ formatCurrency(invoice.amount) }}</td>
                        <td class="px-4 py-3">
                            <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', statusColors[invoice.status]]">
                                {{ statusLabels[invoice.status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-muted-app">{{ invoice.created_at }}</td>
                    </tr>
                    <tr v-if="!invoices.data.length">
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-app">Belum ada invoice.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    </AppLayout>
</template>
