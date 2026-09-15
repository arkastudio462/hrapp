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
    draft: 'bg-cream text-muted-app',
    processing: 'bg-amber/40 text-yellow-700',
    completed: 'bg-sage/50 text-green-700',
};
</script>

<template>
    <Head title="Penggajian" />

    <AppLayout title="Penggajian">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Penggajian</h1>
            <p class="text-sm text-muted-app">Kelola periode dan proses penggajian</p>
        </div>
        <button
            v-if="canManagePayroll"
            class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-coral-deep"
            @click="showModal = true"
        >
            <Plus class="h-4 w-4" />
            Buat Periode
        </button>
    </div>
    <!-- Table -->
    <div class="max-h-[600px] overflow-auto rounded-2xl bg-white shadow-soft">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline-app/30 bg-cream">
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Periode</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Status</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Diproses Oleh</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Tanggal Proses</th>
                    <th class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="period in periods.data" :key="period.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                    <td class="px-4 py-3 text-sm font-bold text-ink-app">{{ months[period.month - 1] }} {{ period.year }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', statusColors[period.status]]">
                            {{ statusLabels[period.status] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-muted-app">{{ period.processed_by || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-muted-app">{{ period.processed_at || '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Link
                                :href="`/payroll/${period.id}`"
                                class="rounded p-1.5 text-muted-app hover:bg-cream hover:text-ink-app"
                            >
                                <Eye class="h-4 w-4" />
                            </Link>
                            <button
                                v-if="canManagePayroll && period.status === 'draft'"
                                class="rounded p-1.5 text-green-700 hover:bg-sage/50"
                                @click="processPayroll(period.id)"
                            >
                                <Calculator class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!periods.data.length">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-app">Belum ada periode gajian.</td>
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
                'px-3 py-1.5 text-sm font-semibold rounded-xl',
                page === periods.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    <!-- Create Period Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <h2 class="text-lg font-extrabold text-ink-app">Buat Periode Gajian</h2>
            <form class="mt-4 space-y-4" @submit.prevent="createPeriod">
                <div>
                    <label class="block text-sm font-bold text-ink-app">Bulan</label>
                    <select v-model="form.month" class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral">
                        <option v-for="(name, i) in months" :key="i" :value="i + 1">{{ name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-ink-app">Tahun</label>
                    <input v-model="form.year" type="number" min="2020" max="2030" class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral" />
                </div>
                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        class="flex-1 rounded-xl border border-outline-app/50 bg-white px-4 py-2 text-sm font-bold text-ink-app transition-colors hover:bg-cream"
                        @click="showModal = false"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-coral-deep"
                    >
                        Buat
                    </button>
                </div>
            </form>
        </div>
    </div>
    </AppLayout>
</template>
