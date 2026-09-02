<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Building2, Users, Globe, Calendar } from 'lucide-vue-next';

const props = defineProps({
    tenant: {
        type: Object,
        default: () => ({}),
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
    domains: {
        type: Array,
        default: () => [],
    },
});

const updateStatus = (status) => {
    if (confirm(`Are you sure you want to change tenant status to ${status}?`)) {
        router.put(`/super-admin/tenants/${props.tenant.id}`, {
            subscription_status: status,
        });
    }
};

const deleteTenant = () => {
    if (confirm('Are you sure you want to delete this tenant? This action cannot be undone.')) {
        router.delete(`/super-admin/tenants/${props.tenant.id}`);
    }
};
</script>

<template>
    <Head title="Tenant Detail" />

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
                            class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted transition-colors hover:bg-stone-50 hover:text-ink"
                        >
                            Dashboard
                        </Link>
                        <Link
                            href="/super-admin/tenants"
                            class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink"
                        >
                            Tenants
                        </Link>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <Link
                    href="/super-admin/tenants"
                    class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back to tenants
                </Link>

                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <h1 class="font-display text-2xl font-bold tracking-tight text-ink">{{ tenant.name }}</h1>
                        <p class="mt-1 text-sm text-inkmuted">{{ tenant.slug }}.hrhub.id</p>
                    </div>
                    <span
                        :class="[
                            'inline-flex rounded-full px-3 py-1 text-sm font-semibold',
                            tenant.subscription_status === 'active' ? 'bg-moss-50 text-moss-700' :
                            tenant.subscription_status === 'trial' ? 'bg-stone-100 text-inkmuted' :
                            'bg-red-50 text-red-700',
                        ]"
                    >
                        {{ tenant.subscription_status }}
                    </span>
                </div>

                <!-- Stats -->
                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-100">
                                <Users class="h-5 w-5 text-ink" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Total Users</p>
                                <p class="font-display text-2xl font-bold">{{ stats.total_users }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-moss-50">
                                <Users class="h-5 w-5 text-moss-700" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">Active Users</p>
                                <p class="font-display text-2xl font-bold">{{ stats.active_users }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="mt-8 space-y-6">
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Tenant Details</h2>
                        <dl class="mt-4 space-y-4">
                            <div class="flex items-center justify-between border-b border-stone-100 py-3">
                                <dt class="text-sm text-inkmuted">Package</dt>
                                <dd class="text-sm font-semibold text-ink">{{ tenant.package?.name || '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between border-b border-stone-100 py-3">
                                <dt class="text-sm text-inkmuted">Trial Ends</dt>
                                <dd class="text-sm font-semibold text-ink">{{ tenant.trial_ends_at || '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between border-b border-stone-100 py-3">
                                <dt class="text-sm text-inkmuted">Max Employees</dt>
                                <dd class="text-sm font-semibold text-ink">{{ tenant.limits?.max_employees || '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between border-b border-stone-100 py-3">
                                <dt class="text-sm text-inkmuted">Max Storage</dt>
                                <dd class="text-sm font-semibold text-ink">{{ tenant.limits?.max_storage_gb || '-' }} GB</dd>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-inkmuted">Created</dt>
                                <dd class="text-sm font-semibold text-ink">{{ tenant.created_at }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Domains -->
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Domains</h2>
                        <ul class="mt-4 space-y-2">
                            <li v-for="domain in domains" :key="domain.id" class="flex items-center gap-2 text-sm text-ink">
                                <Globe class="h-4 w-4 text-inkmuted" />
                                {{ domain.domain }}
                            </li>
                            <li v-if="!domains.length" class="text-sm text-inkmuted">No domains configured.</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Actions</h2>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <button
                                v-if="tenant.subscription_status !== 'active'"
                                class="rounded-full bg-moss-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-moss-700"
                                @click="updateStatus('active')"
                            >
                                Activate
                            </button>
                            <button
                                v-if="tenant.subscription_status !== 'suspended'"
                                class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-ink transition-colors hover:bg-stone-50"
                                @click="updateStatus('suspended')"
                            >
                                Suspend
                            </button>
                            <button
                                class="rounded-full border border-red-300 px-4 py-2 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50"
                                @click="deleteTenant"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
