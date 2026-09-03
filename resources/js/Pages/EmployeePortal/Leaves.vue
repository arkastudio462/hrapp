<script setup>
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
                        <Link href="/my-attendance" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Absensi</Link>
                        <Link href="/my-leave" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Izin/Cuti</Link>
                        <Link href="/my-payslip" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Payslip</Link>
                        <Link href="/my-profile" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Profil</Link>
                        <Link href="/face-attendance" class="rounded-lg bg-ink px-3 py-2 text-sm font-medium text-white">Absen Wajah</Link>
                    </nav>
                </div>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Izin & Cuti Saya</h1>
                    <Link
                        href="/leaves/create"
                        class="inline-flex items-center gap-2 rounded-full bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-moss-700"
                    >
                        <Plus class="h-4 w-4" />
                        Ajukan Izin/Cuti
                    </Link>
                </div>

                <!-- Balance -->
                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="rounded-xl border border-stone-200 bg-white p-4 text-center">
                        <p class="text-sm text-inkmuted">Cuti</p>
                        <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('annual') }}</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-4 text-center">
                        <p class="text-sm text-inkmuted">Sakit</p>
                        <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('sick') }}</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-4 text-center">
                        <p class="text-sm text-inkmuted">Izin</p>
                        <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('permission') }}</p>
                    </div>
                </div>

                <!-- Table -->
                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white">
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
                            page === leaves.current_page ? 'bg-ink text-white' : 'text-inkmuted hover:bg-stone-100',
                        ]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
