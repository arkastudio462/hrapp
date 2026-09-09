<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

const props = defineProps({
    leaves: Object,
    balances: Array,
});

const typeLabels = {
    leave: 'Cuti',
    sick: 'Sakit',
    permission: 'Izin',
};

const typeColors = {
    leave: 'bg-moss-50 text-moss-700',
    sick: 'bg-yellow-50 text-yellow-700',
    permission: 'bg-stone-100 text-inkmuted',
};

const statusLabels = {
    pending: 'Menunggu',
    approved: 'Disetujui',
    rejected: 'Ditolak',
};

const statusColors = {
    pending: 'bg-yellow-50 text-yellow-700',
    approved: 'bg-moss-50 text-moss-700',
    rejected: 'bg-red-50 text-red-700',
};

const getBalance = (type) => {
    const balance = props.balances.find(b => b.type === type);
    return balance ? `${balance.remaining}/${balance.total}` : '-';
};
</script>

<template>
    <Head title="Izin & Cuti Saya" />

    <AppLayout title="Izin & Cuti Saya">
        <div class="flex items-center justify-between">
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Izin & Cuti Saya</h1>
            <Link
                href="/leaves/create"
                class="inline-flex items-center gap-2 rounded-full bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-sky-500/25 hover:bg-moss-700"
            >
                <Plus class="h-4 w-4" />
                Ajukan Izin/Cuti
            </Link>
        </div>

        <!-- Balance -->
        <div class="mt-6 grid grid-cols-3 gap-4">
            <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-4 text-center">
                <p class="text-sm text-inkmuted">Cuti</p>
                <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('annual') }}</p>
            </div>
            <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-4 text-center">
                <p class="text-sm text-inkmuted">Sakit</p>
                <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('sick') }}</p>
            </div>
            <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-4 text-center">
                <p class="text-sm text-inkmuted">Izin</p>
                <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('permission') }}</p>
            </div>
        </div>

        <!-- Table -->
        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100 shadow-sm bg-white">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Alasan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="leave in leaves.data" :key="leave.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                        <td class="px-4 py-3">
                            <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', typeColors[leave.type]]">
                                {{ typeLabels[leave.type] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-ink">{{ leave.start_date }} - {{ leave.end_date }}</td>
                        <td class="px-4 py-3 text-sm text-inkmuted">{{ leave.reason }}</td>
                        <td class="px-4 py-3">
                            <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[leave.status]]">
                                {{ statusLabels[leave.status] }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="!leaves.data.length">
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-inkmuted">Belum ada riwayat izin/cuti.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="leaves.last_page > 1" class="mt-4 flex justify-center gap-1">
            <Link
                v-for="page in leaves.last_page"
                :key="page"
                :href="leaves.path + '?page=' + page"
                :class="[
                    'px-3 py-1.5 text-sm font-medium rounded-lg',
                    page === leaves.current_page ? 'bg-sky-600 text-white' : 'text-inkmuted hover:bg-stone-100',
                ]"
            >
                {{ page }}
            </Link>
        </div>
    </AppLayout>
</template>
