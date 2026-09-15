<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Users, Globe } from 'lucide-vue-next';

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

    <AppLayout title="Detail Tenant">
    <Link href="/super-admin/tenants" class="inline-flex items-center gap-1 text-sm font-bold text-muted-app hover:text-ink-app"><ArrowLeft class="h-4 w-4" /> Kembali</Link>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">{{ tenant.name }}</h1>
            <p class="mt-1 text-sm text-muted-app">{{ tenant.slug }}.hrhub.id</p>
        </div>
        <span
            :class="[
                'rounded-full px-3 py-1 text-sm font-extrabold',
                tenant.subscription_status === 'active' ? 'bg-sage/50 text-green-700' :
                tenant.subscription_status === 'trial' ? 'bg-lavender text-purple-700' :
                'bg-coral-deep/10 text-coral-deep',
            ]"
        >
            {{ tenant.subscription_status }}
        </span>
    </div>
    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow-soft">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-cream">
                    <Users class="h-5 w-5 text-ink-app" />
                </div>
                <div>
                    <p class="text-sm text-muted-app">Total Users</p>
                    <p class="text-2xl font-extrabold text-ink-app">{{ stats.total_users }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-soft">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sage/30">
                    <Users class="h-5 w-5 text-green-700" />
                </div>
                <div>
                    <p class="text-sm text-muted-app">Active Users</p>
                    <p class="text-2xl font-extrabold text-ink-app">{{ stats.active_users }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-8 space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-soft">
            <h2 class="text-lg font-extrabold text-ink-app">Tenant Details</h2>
            <dl class="mt-4 space-y-4">
                <div class="flex items-center justify-between border-b border-outline-app/30 py-3">
                    <dt class="text-sm text-muted-app">Package</dt>
                    <dd class="text-sm font-semibold text-ink-app">{{ tenant.package?.name || '-' }}</dd>
                </div>
                <div class="flex items-center justify-between border-b border-outline-app/30 py-3">
                    <dt class="text-sm text-muted-app">Trial Ends</dt>
                    <dd class="text-sm font-semibold text-ink-app">{{ tenant.trial_ends_at || '-' }}</dd>
                </div>
                <div class="flex items-center justify-between border-b border-outline-app/30 py-3">
                    <dt class="text-sm text-muted-app">Max Employees</dt>
                    <dd class="text-sm font-semibold text-ink-app">{{ tenant.limits?.max_employees || '-' }}</dd>
                </div>
                <div class="flex items-center justify-between border-b border-outline-app/30 py-3">
                    <dt class="text-sm text-muted-app">Max Storage</dt>
                    <dd class="text-sm font-semibold text-ink-app">{{ tenant.limits?.max_storage_gb || '-' }} GB</dd>
                </div>
                <div class="flex items-center justify-between py-3">
                    <dt class="text-sm text-muted-app">Created</dt>
                    <dd class="text-sm font-semibold text-ink-app">{{ tenant.created_at }}</dd>
                </div>
            </dl>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-soft">
            <h2 class="text-lg font-extrabold text-ink-app">Domains</h2>
            <ul class="mt-4 space-y-2">
                <li v-for="domain in domains" :key="domain.id" class="flex items-center gap-2 text-sm text-ink-app">
                    <Globe class="h-4 w-4 text-muted-app" />
                    {{ domain.domain }}
                </li>
                <li v-if="!domains.length" class="text-sm text-muted-app">No domains configured.</li>
            </ul>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-soft">
            <h2 class="text-lg font-extrabold text-ink-app">Actions</h2>
            <div class="mt-4 flex flex-wrap gap-3">
                <button
                    v-if="tenant.subscription_status !== 'active'"
                    class="rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-coral-deep"
                    @click="updateStatus('active')"
                >
                    Activate
                </button>
                <button
                    v-if="tenant.subscription_status !== 'suspended'"
                    class="rounded-xl border border-outline-app/50 bg-white px-4 py-2 text-sm font-semibold text-ink-app transition-colors hover:bg-cream"
                    @click="updateStatus('suspended')"
                >
                    Suspend
                </button>
                <button
                    class="rounded-xl border border-coral-deep/30 bg-white px-4 py-2 text-sm font-semibold text-coral-deep transition-colors hover:bg-coral-deep/10"
                    @click="deleteTenant"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
    </AppLayout>
</template>
