<script setup>
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
                <div class="flex items-center justify-between">
                    <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Tenants</h1>
                    <Link
                        href="/super-admin/tenants/create"
                        class="flex items-center gap-2 rounded-full bg-ink px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-moss-700"
                    >
                        <Plus class="h-4 w-4" />
                        Add Tenant
                    </Link>
                </div>

                <!-- Filters -->
                <div class="mt-6 flex flex-col gap-4 sm:flex-row">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-inkmuted" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search tenants..."
                            class="w-full rounded-full border border-stone-300 bg-white py-2.5 pl-10 pr-4 text-sm text-ink outline-none placeholder:text-inkmuted/60 focus:border-ink"
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    <select
                        v-model="status"
                        class="rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                        @change="applyFilters"
                    >
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="trial">Trial</option>
                        <option value="suspended">Suspended</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>

                <!-- Table -->
                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200">
                    <table class="w-full">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Domain</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Package</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-inkmuted">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            <tr v-for="tenant in tenants.data" :key="tenant.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-ink">{{ tenant.name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-inkmuted">{{ tenant.slug }}.hrhub.id</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-inkmuted">{{ tenant.package || '-' }}</td>
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
                                <td class="whitespace-nowrap px-6 py-4">
                                    <Link
                                        :href="`/super-admin/tenants/${tenant.id}`"
                                        class="text-sm font-semibold text-ink hover:underline"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!tenants.data?.length">
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-inkmuted">
                                    <Building2 class="mx-auto h-8 w-8 text-inkmuted/50" />
                                    <p class="mt-2">No tenants found.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="tenants.last_page > 1" class="mt-6 flex justify-center">
                    <div class="flex items-center gap-2">
                        <Link
                            v-for="page in tenants.last_page"
                            :key="page"
                            :href="`/super-admin/tenants?page=${page}`"
                            :class="[
                                'flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium transition-colors',
                                page === tenants.current_page ? 'bg-ink text-white' : 'text-inkmuted hover:bg-stone-100',
                            ]"
                        >
                            {{ page }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
