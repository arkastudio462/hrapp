<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Building2 } from 'lucide-vue-next';

const props = defineProps({
    tenants: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
    router.get('/super-admin/tenants', {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Tenant Management" />

    <AppLayout title="Tenants">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Tenants</h1>
        <Link
            href="/super-admin/tenants/create"
            class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-coral-deep"
        >
            <Plus class="h-4 w-4" />
            Add Tenant
        </Link>
    </div>
    <div class="mt-6 flex flex-col gap-4 sm:flex-row">
        <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-app" />
            <input
                v-model="search"
                type="text"
                placeholder="Search tenants..."
                class="w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 pl-10 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                @keyup.enter="applyFilters"
            />
        </div>
        <select
            v-model="status"
            class="rounded-xl border border-outline-app/50 bg-white px-4 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="trial">Trial</option>
            <option value="suspended">Suspended</option>
            <option value="terminated">Terminated</option>
        </select>
    </div>
    <div class="max-h-[600px] mt-6 overflow-auto rounded-2xl border border-outline-app/30 shadow-soft">
        <table class="w-full">
            <thead class="bg-cream">
                <tr>
                    <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Name</th>
                    <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Domain</th>
                    <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Package</th>
                    <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Status</th>
                    <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Created</th>
                    <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-app/30 bg-white">
                <tr v-for="tenant in tenants.data" :key="tenant.id" class="hover:bg-cream/50">
                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-ink-app">{{ tenant.name }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-app">{{ tenant.slug }}.hrhub.id</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-app">{{ tenant.package || '-' }}</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span
                            :class="[
                                'rounded-full px-2.5 py-0.5 text-[11px] font-extrabold',
                                tenant.subscription_status === 'active' ? 'bg-sage/50 text-green-700' :
                                tenant.subscription_status === 'trial' ? 'bg-lavender text-purple-700' :
                                'bg-coral-deep/10 text-coral-deep',
                            ]"
                        >
                            {{ tenant.subscription_status }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-app">{{ tenant.created_at }}</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <Link
                            :href="`/super-admin/tenants/${tenant.id}`"
                            class="text-sm font-bold text-muted-app hover:text-ink-app"
                        >
                            View
                        </Link>
                    </td>
                </tr>
                <tr v-if="!tenants.data?.length">
                    <td colspan="6" class="px-6 py-8 text-center text-sm text-muted-app">
                        <Building2 class="mx-auto h-8 w-8 text-muted-app/50" />
                        <p class="mt-2">No tenants found.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-if="tenants.last_page > 1" class="mt-6 flex justify-center">
        <div class="flex items-center gap-2">
            <Link
                v-for="page in tenants.last_page"
                :key="page"
                :href="`/super-admin/tenants?page=${page}`"
                :class="[
                    'flex h-8 w-8 items-center justify-center rounded-xl text-sm font-semibold transition-colors',
                    page === tenants.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
                ]"
            >
                {{ page }}
            </Link>
        </div>
    </div>
    </AppLayout>
</template>
