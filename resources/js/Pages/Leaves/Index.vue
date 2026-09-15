<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Check, X, Trash2, Plus } from 'lucide-vue-next';
import { leaveTypeLabels, leaveTypeBadges, leaveStatusLabels, leaveStatusBadges } from '@/lib/constants';

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
    leave: 'bg-sage/50 text-green-700',
    sick: 'bg-amber/40 text-yellow-700',
    permission: 'bg-cream text-muted-app',
};

const statusLabels = leaveStatusLabels;
const statusColors = leaveStatusBadges;
</script>

<template>
    <Head title="Izin & Cuti" />

    <AppLayout title="Izin & Cuti">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Izin & Cuti</h1>
            <p class="text-sm text-muted-app">Kelola pengajuan izin dan cuti karyawan</p>
        </div>
        <Link
            href="/leaves/create"
            class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-coral-deep"
        >
            <Plus class="h-4 w-4" />
            Ajukan Izin/Cuti
        </Link>
    </div>
    <!-- Filters -->
    <div class="flex flex-wrap gap-3">
        <select
            v-model="statusFilter"
            class="rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">Semua Status</option>
            <option value="pending">Menunggu</option>
            <option value="approved">Disetujui</option>
            <option value="rejected">Ditolak</option>
        </select>
        <select
            v-model="typeFilter"
            class="rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">Semua Tipe</option>
            <option value="leave">Cuti</option>
            <option value="sick">Sakit</option>
            <option value="permission">Izin</option>
        </select>
    </div>
    <!-- Table -->
    <div class="max-h-[600px] overflow-auto rounded-2xl bg-white shadow-soft">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline-app/30 bg-cream">
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Karyawan</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Tipe</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Tanggal</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Status</th>
                    <th class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="leave in leaveRequests.data" :key="leave.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                    <td class="px-4 py-3 text-sm font-bold text-ink-app">{{ leave.employee?.name || '-' }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', typeColors[leave.type]]">
                            {{ typeLabels[leave.type] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ leave.start_date }} - {{ leave.end_date }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', statusColors[leave.status]]">
                            {{ statusLabels[leave.status] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div v-if="canApprove" class="flex items-center justify-end gap-1">
                            <button
                                v-if="leave.status === 'pending'"
                                class="rounded p-1.5 text-green-700 hover:bg-sage/50"
                                @click="approve(leave.id)"
                            >
                                <Check class="h-4 w-4" />
                            </button>
                            <button
                                v-if="leave.status === 'pending'"
                                class="rounded p-1.5 text-coral-deep hover:bg-coral-deep/10"
                                @click="reject(leave.id)"
                            >
                                <X class="h-4 w-4" />
                            </button>
                            <button
                                v-if="isAdmin"
                                class="rounded p-1.5 text-muted-app hover:bg-coral-deep/10 hover:text-coral-deep"
                                @click="deleteLeave(leave.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!leaveRequests.data.length">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-app">Tidak ada data izin/cuti.</td>
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
                'px-3 py-1.5 text-sm font-semibold rounded-xl',
                page === leaveRequests.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    </AppLayout>
</template>
