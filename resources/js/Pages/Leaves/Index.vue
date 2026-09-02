<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Check, X } from 'lucide-vue-next';

const props = defineProps({
    leaveRequests: Object,
    filters: Object,
});

const statusFilter = ref(props.filters?.status || '');
const typeFilter = ref(props.filters?.type || '');

const applyFilters = () => {
    router.get('/leaves', {
        status: statusFilter.value,
        type: typeFilter.value,
    }, { preserveState: true });
};

const approve = (id) => {
    if (confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')) {
        router.post(`/leaves/${id}/approve`);
    }
};

const reject = (id) => {
    if (confirm('Apakah Anda yakin ingin menolak pengajuan ini?')) {
        router.post(`/leaves/${id}/reject`);
    }
};

const deleteLeave = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')) {
        router.delete(`/leaves/${id}`);
    }
};

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
</script>

<template>
    <Head title="Izin & Cuti" />

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
                        <Link href="/departments" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Departemen</Link>
                        <Link href="/positions" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Jabatan</Link>
                        <Link href="/attendances" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Absensi</Link>
                        <Link href="/leaves" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Izin/Cuti</Link>
                    </nav>
                </div>
                <Link
                    href="/leaves/create"
                    class="inline-flex items-center gap-2 rounded-full bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-moss-700"
                >
                    <Plus class="h-4 w-4" />
                    Ajukan Izin/Cuti
                </Link>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Izin & Cuti</h1>

                <!-- Filters -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <select
                        v-model="statusFilter"
                        class="rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                        @change="applyFilters"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                    <select
                        v-model="typeFilter"
                        class="rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                        @change="applyFilters"
                    >
                        <option value="">Semua Tipe</option>
                        <option value="leave">Cuti</option>
                        <option value="sick">Sakit</option>
                        <option value="permission">Izin</option>
                    </select>
                </div>

                <!-- Table -->
                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-stone-100 bg-stone-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Karyawan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="leave in leaveRequests.data" :key="leave.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                                <td class="px-4 py-3 text-sm font-semibold text-ink">{{ leave.employee?.name || '-' }}</td>
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', typeColors[leave.type]]">
                                        {{ typeLabels[leave.type] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-ink">{{ leave.start_date }} - {{ leave.end_date }}</td>
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[leave.status]]">
                                        {{ statusLabels[leave.status] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            v-if="leave.status === 'pending'"
                                            class="rounded p-1.5 text-moss-700 hover:bg-moss-50"
                                            @click="approve(leave.id)"
                                        >
                                            <Check class="h-4 w-4" />
                                        </button>
                                        <button
                                            v-if="leave.status === 'pending'"
                                            class="rounded p-1.5 text-red-600 hover:bg-red-50"
                                            @click="reject(leave.id)"
                                        >
                                            <X class="h-4 w-4" />
                                        </button>
                                        <button class="rounded p-1.5 text-inkmuted hover:bg-red-50 hover:text-red-600" @click="deleteLeave(leave.id)">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!leaveRequests.data.length">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-inkmuted">Tidak ada data izin/cuti.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="leaveRequests.last_page > 1" class="mt-4 flex justify-center gap-1">
                    <Link
                        v-for="page in leaveRequests.last_page"
                        :key="page"
                        :href="leaveRequests.path + '?page=' + page"
                        :class="[
                            'px-3 py-1.5 text-sm font-medium rounded-lg',
                            page === leaveRequests.current_page ? 'bg-ink text-white' : 'text-inkmuted hover:bg-stone-100',
                        ]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
