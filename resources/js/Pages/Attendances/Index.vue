<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Trash2, QrCode } from 'lucide-vue-next';

const props = defineProps({
    attendances: Object,
    employees: Array,
    filters: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.role === 'admin');
const isHr = computed(() => user.value?.role === 'hr');
const canManageAttendance = computed(() => isAdmin.value || isHr.value);

const date = ref(props.filters?.date || new Date().toISOString().split('T')[0]);
const employeeId = ref(props.filters?.employee_id || '');
const status = ref(props.filters?.status || '');

const showQrModal = ref(false);
const qrCode = ref(null);

const applyFilters = () => {
    router.get('/attendances', {
        date: date.value,
        employee_id: employeeId.value,
        status: status.value,
    }, { preserveState: true });
};

const generateQr = async () => {
    const response = await fetch('/attendances/qr/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });
    qrCode.value = await response.json();
    showQrModal.value = true;
};

const deleteAttendance = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data absensi ini?')) {
        router.delete(`/attendances/${id}`);
    }
};

const statusLabels = {
    present: 'Hadir',
    late: 'Terlambat',
    absent: 'Alpa',
};

const statusColors = {
    present: 'bg-status-success/10 text-status-success',
    late: 'bg-status-warning/10 text-status-warning',
    absent: 'bg-status-error/10 text-status-error',
};
</script>

<template>
    <Head title="Absensi" />

    <AppLayout title="Absensi">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-on-surface">Absensi</h1>
            <p class="text-sm text-on-surface-variant">Kelola data kehadiran karyawan</p>
        </div>
        <button
            v-if="canManageAttendance"
            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-on-primary shadow-sm transition-colors hover:bg-primary-container"
            @click="generateQr"
        >
            <QrCode class="h-4 w-4" />
            Generate QR Code
        </button>
    </div>
    <!-- Filters -->
    <div class="flex flex-wrap gap-4">
        <div>
            <label class="block text-xs font-medium text-on-surface-variant">Tanggal</label>
            <input
                v-model="date"
                type="date"
                class="mt-1 rounded-lg border border-slate-200 bg-surface-card px-3 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                @change="applyFilters"
            />
        </div>
        <div>
            <label class="block text-xs font-medium text-on-surface-variant">Karyawan</label>
            <select
                v-model="employeeId"
                class="mt-1 rounded-lg border border-slate-200 bg-surface-card px-3 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                @change="applyFilters"
            >
                <option value="">Semua Karyawan</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-on-surface-variant">Status</label>
            <select
                v-model="status"
                class="mt-1 rounded-lg border border-slate-200 bg-surface-card px-3 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                @change="applyFilters"
            >
                <option value="">Semua Status</option>
                <option value="present">Hadir</option>
                <option value="late">Terlambat</option>
                <option value="absent">Alpa</option>
            </select>
        </div>
    </div>
    <!-- Table -->
    <div class="overflow-hidden rounded-xl bg-surface-card shadow-sm">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 bg-surface-subtle">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Karyawan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Jam Masuk</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Jam Pulang</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-on-surface-variant">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="att in attendances.data" :key="att.id" class="border-b border-slate-100 last:border-0 hover:bg-surface-subtle/50">
                    <td class="px-4 py-3 text-sm font-medium text-on-surface">{{ att.employee?.name || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-on-surface">{{ att.date }}</td>
                    <td class="px-4 py-3 text-sm text-on-surface">{{ att.check_in_time || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-on-surface">{{ att.check_out_time || '-' }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[att.status]]">
                            {{ statusLabels[att.status] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button
                            v-if="canManageAttendance"
                            class="rounded p-1.5 text-on-surface-variant hover:bg-status-error/10 hover:text-status-error"
                            @click="deleteAttendance(att.id)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </td>
                </tr>
                <tr v-if="!attendances.data.length">
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-on-surface-variant">Tidak ada data absensi.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div v-if="attendances.last_page > 1" class="flex justify-center gap-1">
        <Link
            v-for="page in attendances.last_page"
            :key="page"
            :href="attendances.path + '?page=' + page"
            :class="[
                'px-3 py-1.5 text-sm font-medium rounded-lg',
                page === attendances.current_page ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-subtle',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    <!-- QR Code Modal -->
    <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-sm rounded-2xl bg-surface-card p-6 shadow-xl">
            <h2 class="text-lg font-semibold text-on-surface">QR Code Absensi</h2>
            <p class="mt-1 text-sm text-on-surface-variant">Scan QR ini untuk absensi</p>
            <div class="mt-4 flex justify-center">
                <img v-if="qrCode?.qr" :src="qrCode.qr" alt="QR Code" class="h-64 w-64" />
            </div>
            <div class="mt-4 flex justify-end">
                <button
                    type="button"
                    class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-on-surface transition-colors hover:bg-surface-subtle"
                    @click="showQrModal = false"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </AppLayout>
</template>
