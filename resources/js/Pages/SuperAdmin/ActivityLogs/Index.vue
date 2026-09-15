<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    logs: Object,
    actions: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const action = ref(props.filters?.action || '');

const applyFilters = () => {
    router.get('/super-admin/activity-logs', {
        search: search.value,
        action: action.value,
    }, { preserveState: true });
};

const actionColors = {
    'tenant.created': 'bg-sage/50 text-green-700',
    'tenant.updated': 'bg-lavender text-purple-700',
    'tenant.deleted': 'bg-coral-deep/10 text-coral-deep',
    'user.login': 'bg-coral/10 text-coral-deep',
    'user.logout': 'bg-cream text-muted-app',
    'invoice.created': 'bg-amber/40 text-yellow-700',
    'invoice.paid': 'bg-sage/50 text-green-700',
};
</script>

<template>
    <Head title="Activity Logs" />

    <AppLayout title="Activity Logs">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Activity Logs</h1>
        <p class="text-sm text-muted-app">Riwayat aktivitas di platform</p>
    </div>

    <div class="flex flex-wrap gap-4">
        <input
            v-model="search"
            type="text"
            placeholder="Cari aktivitas..."
            class="flex-1 min-w-[200px] rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @keyup.enter="applyFilters"
        />
        <select
            v-model="action"
            class="rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">Semua Aksi</option>
            <option v-for="a in actions" :key="a" :value="a">{{ a }}</option>
        </select>
    </div>

    <div class="max-h-[600px] overflow-auto rounded-2xl bg-white shadow-soft">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline-app/30 bg-cream">
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Waktu</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">User</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Aksi</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Deskripsi</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">IP Address</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="log in logs.data" :key="log.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                    <td class="px-4 py-3 text-sm text-muted-app whitespace-nowrap">{{ log.created_at }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-ink-app">{{ log.user_name }}</td>
                    <td class="px-4 py-3">
                        <span :class="['rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', actionColors[log.action] || 'bg-cream text-muted-app']">
                            {{ log.action }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ log.description || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-muted-app font-mono">{{ log.ip_address || '-' }}</td>
                </tr>
                <tr v-if="!logs.data.length">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-app">Tidak ada data aktivitas.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div v-if="logs.last_page > 1" class="flex justify-center gap-1">
        <button
            v-for="page in logs.last_page"
            :key="page"
            @click="router.get(logs.path + '?page=' + page, { preserveState: true })"
            :class="[
                'px-3 py-1.5 text-sm font-semibold rounded-xl',
                page === logs.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
            ]"
        >
            {{ page }}
        </button>
    </div>
    </AppLayout>
</template>
