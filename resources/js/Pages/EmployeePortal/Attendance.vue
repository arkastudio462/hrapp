<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    attendances: Object,
});

const statusLabels = {
    present: 'Hadir',
    late: 'Terlambat',
    absent: 'Alpa',
};

const statusColors = {
    present: 'bg-moss-50 text-moss-700',
    late: 'bg-yellow-50 text-yellow-700',
    absent: 'bg-red-50 text-red-700',
};
</script>

<template>
    <Head title="Riwayat Absensi" />

    <AppLayout title="Absensi Saya">
    <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Riwayat Absensi</h1>
    <!-- Table -->
    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100 shadow-sm bg-white">
        <table class="w-full">
            <thead>
                <tr class="border-b border-stone-100 bg-stone-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Check In</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Check Out</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="att in attendances.data" :key="att.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                    <td class="px-4 py-3 text-sm font-semibold text-ink">{{ att.date }}</td>
                    <td class="px-4 py-3 text-sm text-ink">{{ att.check_in_time ? new Date(att.check_in_time).toLocaleTimeString('id-ID') : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-ink">{{ att.check_out_time ? new Date(att.check_out_time).toLocaleTimeString('id-ID') : '-' }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[att.status]]">
                            {{ statusLabels[att.status] }}
                        </span>
                    </td>
                </tr>
                <tr v-if="!attendances.data.length">
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-inkmuted">Belum ada riwayat absensi.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div v-if="attendances.last_page > 1" class="mt-4 flex justify-center gap-1">
        <Link
            v-for="page in attendances.last_page"
            :key="page"
            :href="attendances.path + '?page=' + page"
            :class="[
                'px-3 py-1.5 text-sm font-medium rounded-lg',
                page === attendances.current_page ? 'bg-sky-600 text-white' : 'text-inkmuted hover:bg-stone-100',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    </AppLayout>
</template>
