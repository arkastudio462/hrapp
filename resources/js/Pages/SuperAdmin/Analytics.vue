<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { BarChart3, Users, Building2, DollarSign, TrendingUp } from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    revenueChart: Array,
    revenueByPackage: Array,
    recentTenants: Array,
    recentInvoices: Array,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const statusLabels = {
    active: 'Aktif',
    trial: 'Trial',
    suspended: 'Suspended',
    cancelled: 'Dibatalkan',
};

const statusColors = {
    active: 'bg-moss-50 text-moss-700',
    trial: 'bg-yellow-50 text-yellow-700',
    suspended: 'bg-red-50 text-red-700',
    cancelled: 'bg-stone-100 text-inkmuted',
};
</script>

<template>
    <Head title="Analytics" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <Link href="/super-admin/dashboard" class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                            <span class="h-2 w-2 rounded-[1px] bg-white" />
                        </span>
                        <span class="font-display text-lg font-bold tracking-tight">HRapp</span>
                    </Link>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link href="/super-admin/dashboard" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Dashboard</Link>
                        <Link href="/super-admin/tenants" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Tenants</Link>
                        <Link href="/super-admin/analytics" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Analytics</Link>
                    </nav>
                </div>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <BarChart3 class="h-6 w-6 text-ink" />
                    <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Platform Analytics</h1>
                </div>

                <!-- Stats Cards -->
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-stone-100">
                                <Building2 class="h-5 w-5 text-ink" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Total Tenants</p>
                                <p class="font-display text-2xl font-bold">{{ stats.total_tenants }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-moss-50">
                                <Users class="h-5 w-5 text-moss-700" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Active Tenants</p>
                                <p class="font-display text-2xl font-bold">{{ stats.active_tenants }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-stone-100">
                                <Users class="h-5 w-5 text-ink" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Total Users</p>
                                <p class="font-display text-2xl font-bold">{{ stats.total_users }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-moss-50">
                                <DollarSign class="h-5 w-5 text-moss-700" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">MRR</p>
                                <p class="font-display text-2xl font-bold">{{ formatCurrency(stats.mrr) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue by Package -->
                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Revenue by Package</h2>
                        <div class="mt-4 space-y-4">
                            <div v-for="pkg in revenueByPackage" :key="pkg.package_name" class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-ink">{{ pkg.package_name }}</p>
                                    <p class="text-xs text-inkmuted">{{ pkg.invoice_count }} invoices</p>
                                </div>
                                <p class="font-semibold text-ink">{{ formatCurrency(pkg.total_revenue) }}</p>
                            </div>
                            <div v-if="!revenueByPackage.length" class="py-4 text-center text-sm text-inkmuted">
                                Belum ada data revenue.
                            </div>
                        </div>
                    </div>

                    <!-- Recent Tenants -->
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Recent Tenants</h2>
                        <div class="mt-4 space-y-3">
                            <div v-for="tenant in recentTenants" :key="tenant.id" class="flex items-center justify-between border-b border-stone-100 pb-3 last:border-0">
                                <div>
                                    <p class="text-sm font-semibold text-ink">{{ tenant.name }}</p>
                                    <p class="text-xs text-inkmuted">{{ tenant.package?.name || '-' }}</p>
                                </div>
                                <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[tenant.subscription_status]]">
                                    {{ statusLabels[tenant.subscription_status] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Invoices -->
                <div class="mt-8 rounded-xl border border-stone-200 bg-white p-6">
                    <h2 class="font-display text-lg font-bold text-ink">Recent Invoices</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-stone-100">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tenant</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Paket</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="invoice in recentInvoices" :key="invoice.id" class="border-b border-stone-100 last:border-0">
                                    <td class="px-4 py-3 text-sm font-semibold text-ink">{{ invoice.tenant?.name || '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-inkmuted">{{ invoice.package?.name || '-' }}</td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-ink">{{ formatCurrency(invoice.amount) }}</td>
                                    <td class="px-4 py-3">
                                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[invoice.status] || 'bg-stone-100 text-inkmuted']">
                                            {{ invoice.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-inkmuted">{{ invoice.created_at }}</td>
                                </tr>
                                <tr v-if="!recentInvoices.length">
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
