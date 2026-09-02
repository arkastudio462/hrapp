<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Filter, Eye, Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    employees: Object,
    departments: Array,
    positions: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const departmentId = ref(props.filters?.department_id || '');
const status = ref(props.filters?.status || '');

const applyFilters = () => {
    router.get('/employees', {
        search: search.value,
        department_id: departmentId.value,
        status: status.value,
    }, {
        preserveState: true,
    });
};

const deleteEmployee = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus karyawan ini?')) {
        router.delete(`/employees/${id}`);
    }
};

const statusLabels = {
    contract: 'Kontrak',
    permanent: 'Permanent',
    probation: 'Percobaan',
};

const statusColors = {
    contract: 'bg-stone-100 text-inkmuted',
    permanent: 'bg-moss-50 text-moss-700',
    probation: 'bg-yellow-50 text-yellow-700',
};
</script>

<template>
    <Head title="Karyawan" />

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
                        <Link href="/employees" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Karyawan</Link>
                        <Link href="/departments" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Departemen</Link>
                        <Link href="/positions" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Jabatan</Link>
                        <Link href="/attendances" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Absensi</Link>
                        <Link href="/leaves" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Izin/Cuti</Link>
                    </nav>
                </div>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Karyawan</h1>
                    <Link
                        href="/employees/create"
                        class="inline-flex items-center gap-2 rounded-full bg-ink px-4 py-2.5 text-sm font-semibold text-white hover:bg-moss-700"
                    >
                        <Plus class="h-4 w-4" />
                        Tambah Karyawan
                    </Link>
                </div>

                <!-- Filters -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-inkmuted" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari nama, NIK, atau email..."
                                class="w-full rounded-full border border-stone-300 bg-white pl-10 pr-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                    </div>
                    <select
                        v-model="departmentId"
                        class="rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                        @change="applyFilters"
                    >
                        <option value="">Semua Departemen</option>
                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                    </select>
                    <select
                        v-model="status"
                        class="rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                        @change="applyFilters"
                    >
                        <option value="">Semua Status</option>
                        <option value="permanent">Permanent</option>
                        <option value="contract">Kontrak</option>
                        <option value="probation">Percobaan</option>
                    </select>
                </div>

                <!-- Table -->
                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-stone-100 bg-stone-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">NIK</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Departemen</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Jabatan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="emp in employees.data" :key="emp.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                                <td class="px-4 py-3 text-sm font-mono text-inkmuted">{{ emp.nik }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-ink">{{ emp.name }}</td>
                                <td class="px-4 py-3 text-sm text-ink">{{ emp.department?.name || '-' }}</td>
                                <td class="px-4 py-3 text-sm text-ink">{{ emp.position?.name || '-' }}</td>
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[emp.status]]">
                                        {{ statusLabels[emp.status] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="`/employees/${emp.id}`" class="rounded p-1.5 text-inkmuted hover:bg-stone-100 hover:text-ink">
                                            <Eye class="h-4 w-4" />
                                        </Link>
                                        <Link :href="`/employees/${emp.id}/edit`" class="rounded p-1.5 text-inkmuted hover:bg-stone-100 hover:text-ink">
                                            <Pencil class="h-4 w-4" />
                                        </Link>
                                        <button class="rounded p-1.5 text-inkmuted hover:bg-red-50 hover:text-red-600" @click="deleteEmployee(emp.id)">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!employees.data.length">
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-inkmuted">Tidak ada data karyawan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="employees.last_page > 1" class="mt-4 flex justify-center gap-1">
                    <Link
                        v-for="page in employees.last_page"
                        :key="page"
                        :href="employees.path + '?page=' + page"
                        :class="[
                            'px-3 py-1.5 text-sm font-medium rounded-lg',
                            page === employees.current_page ? 'bg-ink text-white' : 'text-inkmuted hover:bg-stone-100',
                        ]"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
