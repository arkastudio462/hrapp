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
    present: 'bg-sage/50 text-green-700',
    late: 'bg-amber/40 text-yellow-700',
    absent: 'bg-coral-deep/10 text-coral-deep',
};
</script>

<template>
    <Head title="Absensi" />

    <AppLayout title="Absensi">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Absensi</h1>
            <p class="text-sm text-muted-app">Kelola data kehadiran karyawan</p>
        </div>
        <button
            v-if="canManageAttendance"
            class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2 text-sm font-bold text-white shadow-soft transition-colors hover:bg-coral-deep"
            @click="generateQr"
        >
            <QrCode class="size-4" />
            Generate QR Code
        </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4">
        <div>
            <label class="block text-xs font-bold text-muted-app">Tanggal</label>
            <input
                v-model="date"
                type="date"
                class="mt-1 rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                @change="applyFilters"
            />
        </div>
        <div>
            <label class="block text-xs font-bold text-muted-app">Karyawan</label>
            <select
                v-model="employeeId"
                class="mt-1 rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                @change="applyFilters"
            >
                <option value="">Semua Karyawan</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-muted-app">Status</label>
            <select
                v-model="status"
                class="mt-1 rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
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
    <div class="max-h-[600px] overflow-auto rounded-2xl bg-white shadow-soft">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline-app/30 bg-cream">
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Tanggal</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Karyawan</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Jam Masuk</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Jam Keluar</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Status</th>
                    <th v-if="canManageAttendance" class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="att in attendances.data" :key="att.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                    <td class="px-4 py-3 text-sm font-semibold text-ink-app">{{ att.date }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-ink-app">{{ att.employee?.name || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ att.check_in || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ att.check_out || '-' }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', statusColors[att.status]]">
                            {{ statusLabels[att.status] }}
                        </span>
                    </td>
                    <td v-if="canManageAttendance" class="px-4 py-3 text-right">
                        <button
                            class="rounded-lg p-1.5 text-muted-app transition-colors hover:bg-coral-deep/10 hover:text-coral-deep"
                            @click="deleteAttendance(att.id)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </td>
                </tr>
                <tr v-if="!attendances.data?.length">
                    <td :colspan="canManageAttendance ? 6 : 5" class="px-4 py-8 text-center text-sm text-muted-app">
                        Tidak ada data absensi.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div v-if="attendances.last_page > 1" class="flex justify-center gap-1">
        <Link
            v-for="p in attendances.last_page"
            :key="p"
            :href="attendances.path + '?page=' + p"
            :class="[
                'px-3 py-1.5 text-sm font-bold rounded-xl',
                p === attendances.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
            ]"
        >
            {{ p }}
        </Link>
    </div>

    <!-- QR Modal -->
    <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showQrModal = false">
        <div class="rounded-2xl bg-white p-6 shadow-card">
            <h3 class="text-lg font-extrabold text-ink-app">QR Code Absensi</h3>
            <p class="mt-1 text-sm text-muted-app">Scan QR ini untuk absensi masuk/keluar.</p>
            <div v-if="qrCode" class="mt-4 flex justify-center">
                <img :src="qrCode.qr" alt="QR Code" class="h-64 w-64 rounded-xl" />
            </div>
            <div class="mt-4 flex justify-end">
                <button
                    class="rounded-xl border border-outline-app/50 bg-white px-4 py-2 text-sm font-bold text-ink-app transition-colors hover:bg-cream"
                    @click="showQrModal = false"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </AppLayout>
</template>
