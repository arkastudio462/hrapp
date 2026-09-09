<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Eye, Calculator, Plus } from 'lucide-vue-next';

const props = defineProps({
    periods: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.role === 'admin');
const isHr = computed(() => user.value?.role === 'hr');
const canManagePayroll = computed(() => isAdmin.value || isHr.value);

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
    draft: 'bg-surface-container text-on-surface-variant',
    processing: 'bg-status-warning/10 text-status-warning',
    completed: 'bg-status-success/10 text-status-success',
};
</script>

<template>
    <Head title="Penggajian" />

    <AppLayout title="Penggajian">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-on-surface">Penggajian</h1>
            <p class="text-sm text-on-surface-variant">Kelola periode dan proses penggajian</p>
        </div>
        <button
            v-if="canManagePayroll"
            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-on-primary shadow-sm transition-colors hover:bg-primary-container"
            @click="showModal = true"
        >
            <Plus class="h-4 w-4" />
            Buat Periode
        </button>
    </div>
    <!-- Table -->
    <div class="overflow-hidden rounded-xl bg-surface-card shadow-sm">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 bg-surface-subtle">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Diproses Oleh</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Tanggal Proses</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-on-surface-variant">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="period in periods.data" :key="period.id" class="border-b border-slate-100 last:border-0 hover:bg-surface-subtle/50">
                    <td class="px-4 py-3 text-sm font-medium text-on-surface">{{ months[period.month - 1] }} {{ period.year }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[period.status]]">
                            {{ statusLabels[period.status] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-on-surface-variant">{{ period.processed_by || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-on-surface-variant">{{ period.processed_at || '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Link
                                :href="`/payroll/${period.id}`"
                                class="rounded p-1.5 text-on-surface-variant hover:bg-surface-subtle hover:text-on-surface"
                            >
                                <Eye class="h-4 w-4" />
                            </Link>
                            <button
                                v-if="canManagePayroll && period.status === 'draft'"
                                class="rounded p-1.5 text-status-success hover:bg-status-success/10"
                                @click="processPayroll(period.id)"
                            >
                                <Calculator class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!periods.data.length">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-on-surface-variant">Belum ada periode gajian.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div v-if="periods.last_page > 1" class="flex justify-center gap-1">
        <Link
            v-for="page in periods.last_page"
            :key="page"
            :href="periods.path + '?page=' + page"
            :class="[
                'px-3 py-1.5 text-sm font-medium rounded-lg',
                page === periods.current_page ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-subtle',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    <!-- Create Period Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-2xl bg-surface-card p-6 shadow-xl">
            <h2 class="text-lg font-semibold text-on-surface">Buat Periode Gajian</h2>
            <form class="mt-4 space-y-4" @submit.prevent="createPeriod">
                <div>
                    <label class="block text-sm font-medium text-on-surface">Bulan</label>
                    <select v-model="form.month" class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-surface-card px-3 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        <option v-for="(name, i) in months" :key="i" :value="i + 1">{{ name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-on-surface">Tahun</label>
                    <input v-model="form.year" type="number" min="2020" max="2030" class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-surface-card px-3 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" />
                </div>
                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        class="flex-1 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-on-surface transition-colors hover:bg-surface-subtle"
                        @click="showModal = false"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-on-primary shadow-sm transition-colors hover:bg-primary-container"
                    >
                        Buat
                    </button>
                </div>
            </form>
        </div>
    </div>
    </AppLayout>
</template>
