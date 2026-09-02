<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, QrCode, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    attendances: Object,
    employees: Array,
    filters: Object,
});

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
    present: 'bg-moss-50 text-moss-700',
    late: 'bg-yellow-50 text-yellow-700',
    absent: 'bg-red-50 text-red-700',
};
</script>

<template>
    <Head title="Absensi" />

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
                        <Link href="/attendances" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Absensi</Link>
                        <Link href="/leaves" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Izin/Cuti</Link>
                    </nav>
                </div>
                <button
                    class="inline-flex items-center gap-2 rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-ink hover:bg-stone-50"
                    @click="generateQr"
                >
                    <QrCode class="h-4 w-4" />
                    Generate QR
                </button>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Absensi</h1>

                <!-- Filters -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <div>
                        <label class="block text-sm font-medium text-ink">Tanggal</label>
                        <input
                            v-model="date"
                            type="date"
                            class="mt-1.5 rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                            @change="applyFilters"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Karyawan</label>
                        <select
                            v-model="employeeId"
                            class="mt-1.5 rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                            @change="applyFilters"
                        >
                            <option value="">Semua Karyawan</option>
                            <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Status</label>
                        <select
                            v-model="status"
                            class="mt-1.5 rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                            @change="applyFilters"
                        >
                            <option value="">Semua</option>
                            <option value="present">Hadir</option>
                            <option value="late">Terlambat</option>
                            <option value="absent">Alpa</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-stone-100 bg-stone-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Karyawan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Check In</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Check Out</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="att in attendances.data" :key="att.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                                <td class="px-4 py-3 text-sm font-semibold text-ink">{{ att.employee?.name || '-' }}</td>
                                <td class="px-4 py-3 text-sm text-inkmuted">{{ att.date }}</td>
                                <td class="px-4 py-3 text-sm text-ink">{{ att.check_in_time ? new Date(att.check_in_time).toLocaleTimeString('id-ID') : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-ink">{{ att.check_out_time ? new Date(att.check_out_time).toLocaleTimeString('id-ID') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[att.status]]">
                                        {{ statusLabels[att.status] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button class="rounded p-1.5 text-inkmuted hover:bg-red-50 hover:text-red-600" @click="deleteAttendance(att.id)">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!attendances.data.length">
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-inkmuted">Tidak ada data absensi.</td>
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

        <!-- QR Modal -->
        <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="rounded-xl bg-white p-8 text-center">
                <h2 class="font-display text-lg font-bold text-ink">QR Code Absensi</h2>
                <p class="mt-2 text-sm text-inkmuted">Berlaku hingga {{ qrCode?.expires_at }}</p>
                <div class="mt-4">
                    <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${qrCode?.code}`" alt="QR Code" class="mx-auto" />
                </div>
                <button
                    class="mt-6 rounded-full bg-ink px-6 py-2 text-sm font-semibold text-white hover:bg-moss-700"
                    @click="showQrModal = false"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
