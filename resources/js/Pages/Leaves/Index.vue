<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Check, X, Trash2, Plus } from 'lucide-vue-next';

const props = defineProps({
    leaveRequests: Object,
    filters: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.role === 'admin');
const isHr = computed(() => user.value?.role === 'hr');
const canApprove = computed(() => isAdmin.value || isHr.value);

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
    leave: 'bg-status-success/10 text-status-success',
    sick: 'bg-status-warning/10 text-status-warning',
    permission: 'bg-surface-container text-on-surface-variant',
};

const statusLabels = {
    pending: 'Menunggu',
    approved: 'Disetujui',
    rejected: 'Ditolak',
};

const statusColors = {
    pending: 'bg-status-warning/10 text-status-warning',
    approved: 'bg-status-success/10 text-status-success',
    rejected: 'bg-status-error/10 text-status-error',
};
</script>

<template>
    <Head title="Izin & Cuti" />

    <AppLayout title="Izin & Cuti">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-on-surface">Izin & Cuti</h1>
            <p class="text-sm text-on-surface-variant">Kelola pengajuan izin dan cuti karyawan</p>
        </div>
        <Link
            href="/leaves/create"
            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-on-primary shadow-sm transition-colors hover:bg-primary-container"
        >
            <Plus class="h-4 w-4" />
            Ajukan Izin/Cuti
        </Link>
    </div>
    <!-- Filters -->
    <div class="flex flex-wrap gap-3">
        <select
            v-model="statusFilter"
            class="rounded-lg border border-slate-200 bg-surface-card px-3 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
            @change="applyFilters"
        >
            <option value="">Semua Status</option>
            <option value="pending">Menunggu</option>
            <option value="approved">Disetujui</option>
            <option value="rejected">Ditolak</option>
        </select>
        <select
            v-model="typeFilter"
            class="rounded-lg border border-slate-200 bg-surface-card px-3 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
            @change="applyFilters"
        >
            <option value="">Semua Tipe</option>
            <option value="leave">Cuti</option>
            <option value="sick">Sakit</option>
            <option value="permission">Izin</option>
        </select>
    </div>
    <!-- Table -->
    <div class="overflow-hidden rounded-xl bg-surface-card shadow-sm">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 bg-surface-subtle">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Karyawan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-on-surface-variant">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="leave in leaveRequests.data" :key="leave.id" class="border-b border-slate-100 last:border-0 hover:bg-surface-subtle/50">
                    <td class="px-4 py-3 text-sm font-medium text-on-surface">{{ leave.employee?.name || '-' }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', typeColors[leave.type]]">
                            {{ typeLabels[leave.type] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-on-surface">{{ leave.start_date }} - {{ leave.end_date }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[leave.status]]">
                            {{ statusLabels[leave.status] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div v-if="canApprove" class="flex items-center justify-end gap-1">
                            <button
                                v-if="leave.status === 'pending'"
                                class="rounded p-1.5 text-status-success hover:bg-status-success/10"
                                @click="approve(leave.id)"
                            >
                                <Check class="h-4 w-4" />
                            </button>
                            <button
                                v-if="leave.status === 'pending'"
                                class="rounded p-1.5 text-status-error hover:bg-status-error/10"
                                @click="reject(leave.id)"
                            >
                                <X class="h-4 w-4" />
                            </button>
                            <button
                                v-if="isAdmin"
                                class="rounded p-1.5 text-on-surface-variant hover:bg-status-error/10 hover:text-status-error"
                                @click="deleteLeave(leave.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!leaveRequests.data.length">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-on-surface-variant">Tidak ada data izin/cuti.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div v-if="leaveRequests.last_page > 1" class="flex justify-center gap-1">
        <Link
            v-for="page in leaveRequests.last_page"
            :key="page"
            :href="leaveRequests.path + '?page=' + page"
            :class="[
                'px-3 py-1.5 text-sm font-medium rounded-lg',
                page === leaveRequests.current_page ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-subtle',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    </AppLayout>
</template>
