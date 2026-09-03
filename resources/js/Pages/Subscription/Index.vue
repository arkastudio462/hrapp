<script setup>
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
    pending: 'bg-yellow-50 text-yellow-700',
    paid: 'bg-moss-50 text-moss-700',
    failed: 'bg-red-50 text-red-700',
    cancelled: 'bg-stone-100 text-inkmuted',
};

const subscriptionStatusLabels = {
    active: 'Aktif',
    trial: 'Trial',
    suspended: 'Suspended',
    cancelled: 'Dibatalkan',
};

const subscriptionStatusColors = {
    active: 'bg-moss-50 text-moss-700',
    trial: 'bg-yellow-50 text-yellow-700',
    suspended: 'bg-red-50 text-red-700',
    cancelled: 'bg-stone-100 text-inkmuted',
};
</script>

<template>
    <Head title="Subscription" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <Link href="/dashboard" class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                            <span class="h-2 w-2 rounded-[1px] bg-white" />
                        </span>
                        <span class="font-display text-lg font-bold tracking-tight">HRapp</span>
                    </Link>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link href="/dashboard" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Dashboard</Link>
                        <Link href="/employees" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Karyawan</Link>
                        <Link href="/subscription" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Subscription</Link>
                        <Link href="/settings" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Pengaturan</Link>
                    </nav>
                </div>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Subscription & Billing</h1>

                <!-- Current Plan -->
                <div class="mt-6 rounded-xl border border-stone-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink">Paket Saat Ini</h2>
                            <p class="mt-1 text-sm text-inkmuted">{{ tenant.name }}</p>
                        </div>
                        <span :class="['inline-flex rounded-full px-3 py-1 text-sm font-semibold', subscriptionStatusColors[tenant.subscription_status]]">
                            {{ subscriptionStatusLabels[tenant.subscription_status] }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-xl bg-stone-50 p-4">
                            <div class="flex items-center gap-3">
                                <Package class="h-5 w-5 text-inkmuted" />
                                <div>
                                    <p class="text-sm text-inkmuted">Paket</p>
                                    <p class="font-semibold text-ink">{{ tenant.package?.name || '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl bg-stone-50 p-4">
                            <div class="flex items-center gap-3">
                                <CreditCard class="h-5 w-5 text-inkmuted" />
                                <div>
                                    <p class="text-sm text-inkmuted">Harga</p>
                                    <p class="font-semibold text-ink">{{ tenant.package?.price ? formatCurrency(tenant.package.price) + '/bulan' : '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl bg-stone-50 p-4">
                            <div class="flex items-center gap-3">
                                <History class="h-5 w-5 text-inkmuted" />
                                <div>
                                    <p class="text-sm text-inkmuted">Trial Berakhir</p>
                                    <p class="font-semibold text-ink">{{ tenant.trial_ends_at || '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <Link
                            href="/subscription/packages"
                            class="inline-flex items-center gap-2 rounded-full bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-moss-700"
                        >
                            <ExternalLink class="h-4 w-4" />
                            Upgrade Paket
                        </Link>
                    </div>
                </div>

                <!-- Invoice History -->
                <div class="mt-8">
                    <h2 class="font-display text-lg font-bold text-ink">Riwayat Invoice</h2>

                    <div class="mt-4 overflow-hidden rounded-xl border border-stone-200 bg-white">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-stone-100 bg-stone-50">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Invoice</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Deskripsi</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                                    <td class="px-4 py-3 text-sm font-mono text-inkmuted">{{ invoice.invoice_number || `INV-${invoice.id}` }}</td>
                                    <td class="px-4 py-3 text-sm text-ink">{{ invoice.description || invoice.package?.name || '-' }}</td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-ink">{{ formatCurrency(invoice.amount) }}</td>
                                    <td class="px-4 py-3">
                                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[invoice.status]]">
                                            {{ statusLabels[invoice.status] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-inkmuted">{{ invoice.created_at }}</td>
                                </tr>
                                <tr v-if="!invoices.data.length">
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-inkmuted">Belum ada invoice.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
