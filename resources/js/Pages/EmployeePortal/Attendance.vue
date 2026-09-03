<script setup>
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

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <Link href="/my-dashboard" class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                            <span class="h-2 w-2 rounded-[1px] bg-white" />
                        </span>
                        <span class="font-display text-lg font-bold tracking-tight">HRapp</span>
                    </Link>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link href="/my-dashboard" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Dashboard</Link>
                        <Link href="/my-attendance" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Absensi</Link>
                        <Link href="/my-leave" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Izin/Cuti</Link>
                        <Link href="/my-payslip" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Payslip</Link>
                        <Link href="/my-profile" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Profil</Link>
                        <Link href="/face-attendance" class="rounded-lg bg-ink px-3 py-2 text-sm font-medium text-white">Absen Wajah</Link>
                    </nav>
                </div>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Riwayat Absensi</h1>

                <!-- Table -->
                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white">
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
                            page === attendances.current_page ? 'bg-ink text-white' : 'text-inkmuted hover:bg-stone-100',
                        ]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
