<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Eye, Calculator } from 'lucide-vue-next';

const props = defineProps({
    periods: Object,
});

const showModal = ref(false);
const form = ref({
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
});

const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];

const createPeriod = () => {
    router.post('/payroll', form.value, {
        onFinish: () => {
            showModal.value = false;
        },
    });
};

const processPayroll = (id) => {
    if (confirm('Apakah Anda yakin ingin memproses gajian untuk periode ini?')) {
        router.post(`/payroll/${id}/process`);
    }
};

const statusLabels = {
    draft: 'Draft',
    processing: 'Diproses',
    completed: 'Selesai',
};

const statusColors = {
    draft: 'bg-stone-100 text-inkmuted',
    processing: 'bg-yellow-50 text-yellow-700',
    completed: 'bg-moss-50 text-moss-700',
};
</script>

<template>
    <Head title="Penggajian" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <Link href="/dashboard" class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                            <span class="h-2 w-2 rounded-[1px] bg-white" />
                        </span>
                        <span class="font-display text-lg font-bold tracking-tight">HRapp</span>
                    </Link>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link href="/dashboard" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Dashboard</Link>
                        <Link href="/employees" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Karyawan</Link>
                        <Link href="/attendances" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Absensi</Link>
                        <Link href="/leaves" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Izin/Cuti</Link>
                        <Link href="/payroll" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Penggajian</Link>
                        <Link href="/settings" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Pengaturan</Link>
                    </nav>
                </div>
                <button
                    class="inline-flex items-center gap-2 rounded-full bg-ink px-4 py-2.5 text-sm font-semibold text-white hover:bg-moss-700"
                    @click="showModal = true"
                >
                    <Plus class="h-4 w-4" />
                    Buat Periode
                </button>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Penggajian</h1>

                <!-- Table -->
                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-stone-100 bg-stone-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Periode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Diproses Oleh</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tanggal Proses</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="period in periods.data" :key="period.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                                <td class="px-4 py-3 text-sm font-semibold text-ink">{{ months[period.month - 1] }} {{ period.year }}</td>
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[period.status]]">
                                        {{ statusLabels[period.status] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-inkmuted">{{ period.processed_by || '-' }}</td>
                                <td class="px-4 py-3 text-sm text-inkmuted">{{ period.processed_at || '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="`/payroll/${period.id}`"
                                            class="rounded p-1.5 text-inkmuted hover:bg-stone-100 hover:text-ink"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Link>
                                        <button
                                            v-if="period.status === 'draft'"
                                            class="rounded p-1.5 text-moss-700 hover:bg-moss-50"
                                            @click="processPayroll(period.id)"
                                        >
                                            <Calculator class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!periods.data.length">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-inkmuted">Belum ada periode gajian.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="periods.last_page > 1" class="mt-4 flex justify-center gap-1">
                    <Link
                        v-for="page in periods.last_page"
                        :key="page"
                        :href="periods.path + '?page=' + page"
                        :class="[
                            'px-3 py-1.5 text-sm font-medium rounded-lg',
                            page === periods.current_page ? 'bg-ink text-white' : 'text-inkmuted hover:bg-stone-100',
                        ]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>

        <!-- Create Period Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-md rounded-xl bg-white p-6">
                <h2 class="font-display text-lg font-bold text-ink">Buat Periode Gajian</h2>
                <form class="mt-4 space-y-4" @submit.prevent="createPeriod">
                    <div>
                        <label class="block text-sm font-medium text-ink">Bulan</label>
                        <select v-model="form.month" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink">
                            <option v-for="(name, i) in months" :key="i" :value="i + 1">{{ name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Tahun</label>
                        <input v-model="form.year" type="number" min="2020" max="2030" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button
                            type="button"
                            class="flex-1 rounded-full border border-stone-300 px-4 py-3 text-sm font-semibold text-ink hover:bg-stone-50"
                            @click="showModal = false"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="flex-1 rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700"
                        >
                            Buat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
