<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Building2, Users, DollarSign, Clock, ArrowRight, TrendingUp } from 'lucide-vue-next';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalTenants: 0,
            activeTenants: 0,
            trialTenants: 0,
            totalUsers: 0,
            monthlyRevenue: 0,
            pendingInvoices: 0,
        }),
    },
    recentTenants: {
        type: Array,
        default: () => [],
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <Head title="Super Admin Dashboard" />

    <div class="min-h-screen bg-white">
        <!-- Header -->
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
                        <Link
                            href="/super-admin/dashboard"
                            class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink"
                        >
                            Dashboard
                        </Link>
                        <Link
                            href="/super-admin/tenants"
                            class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted transition-colors hover:bg-stone-50 hover:text-ink"
                        >
                            Tenants
                        </Link>
                    </nav>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/super-admin/logout"
                        method="post"
                        as="button"
                        class="flex items-center gap-2 rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-ink transition-colors hover:bg-stone-50"
                    >
                        Logout
                    </Link>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Dashboard</h1>

                <!-- Stats -->
                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-100">
                                <Building2 class="h-5 w-5 text-ink" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Total Tenants</p>
                                <p class="font-display text-2xl font-bold">{{ stats.totalTenants }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-moss-50">
                                <TrendingUp class="h-5 w-5 text-moss-700" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Active Tenants</p>
                                <p class="font-display text-2xl font-bold">{{ stats.activeTenants }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-100">
                                <Clock class="h-5 w-5 text-ink" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Trial Tenants</p>
                                <p class="font-display text-2xl font-bold">{{ stats.trialTenants }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-100">
                                <Users class="h-5 w-5 text-ink" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Total Users</p>
                                <p class="font-display text-2xl font-bold">{{ stats.totalUsers }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-moss-50">
                                <DollarSign class="h-5 w-5 text-moss-700" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Monthly Revenue</p>
                                <p class="font-display text-2xl font-bold">{{ formatCurrency(stats.monthlyRevenue) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-100">
                                <Clock class="h-5 w-5 text-ink" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Pending Invoices</p>
                                <p class="font-display text-2xl font-bold">{{ stats.pendingInvoices }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tenants -->
                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-lg font-bold text-ink">Recent Tenants</h2>
                        <Link
                            href="/super-admin/tenants"
                            class="flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink"
                        >
                            View all
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-xl border border-stone-200">
                        <table class="w-full">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Slug</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Created</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 bg-white">
                                <tr v-for="tenant in recentTenants" :key="tenant.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-ink">{{ tenant.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-inkmuted">{{ tenant.slug }}.hrhub.id</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            :class="[
                                                'inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                                tenant.subscription_status === 'active' ? 'bg-moss-50 text-moss-700' :
                                                tenant.subscription_status === 'trial' ? 'bg-stone-100 text-inkmuted' :
                                                'bg-red-50 text-red-700',
                                            ]"
                                        >
                                            {{ tenant.subscription_status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-inkmuted">{{ tenant.created_at }}</td>
                                </tr>
                                <tr v-if="recentTenants.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-inkmuted">No tenants yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
