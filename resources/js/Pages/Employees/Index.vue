<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Plus, Search, Eye, Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    employees: Object,
    departments: Array,
    positions: Array,
    filters: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.role === 'admin');
const isHr = computed(() => user.value?.role === 'hr');
const canManageEmployees = computed(() => isAdmin.value);

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
    contract: 'bg-surface-container text-on-surface-variant',
    permanent: 'bg-status-success/10 text-status-success',
    probation: 'bg-status-warning/10 text-status-warning',
};
</script>

<template>
    <Head title="Karyawan" />

    <AppLayout title="Karyawan">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-on-surface">Karyawan</h1>
            <p class="text-sm text-on-surface-variant">Kelola data karyawan perusahaan</p>
        </div>
        <Link
            v-if="canManageEmployees"
            href="/employees/create"
            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-on-primary shadow-sm transition-colors hover:bg-primary-container"
        >
            <Plus class="h-4 w-4" />
            Tambah Karyawan
        </Link>
    </div>
    <!-- Filters -->
    <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-on-surface-variant" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama, NIK, atau email..."
                    class="w-full rounded-lg border border-slate-200 bg-surface-card pl-10 pr-4 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    @keyup.enter="applyFilters"
                />
            </div>
        </div>
        <select
            v-model="departmentId"
            class="rounded-lg border border-slate-200 bg-surface-card px-3 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
            @change="applyFilters"
        >
            <option value="">Semua Departemen</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
        </select>
        <select
            v-model="status"
            class="rounded-lg border border-slate-200 bg-surface-card px-3 py-2 text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
            @change="applyFilters"
        >
            <option value="">Semua Status</option>
            <option value="permanent">Permanent</option>
            <option value="contract">Kontrak</option>
            <option value="probation">Percobaan</option>
        </select>
    </div>
    <!-- Table -->
    <div class="overflow-hidden rounded-xl bg-surface-card shadow-sm">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 bg-surface-subtle">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">NIK</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Departemen</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Jabatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-on-surface-variant">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-on-surface-variant">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="emp in employees.data" :key="emp.id" class="border-b border-slate-100 last:border-0 hover:bg-surface-subtle/50">
                    <td class="px-4 py-3 text-sm font-mono text-on-surface-variant">{{ emp.nik }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-on-surface">{{ emp.name }}</td>
                    <td class="px-4 py-3 text-sm text-on-surface">{{ emp.department?.name || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-on-surface">{{ emp.position?.name || '-' }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', statusColors[emp.status]]">
                            {{ statusLabels[emp.status] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Link :href="`/employees/${emp.id}`" class="rounded p-1.5 text-on-surface-variant hover:bg-surface-subtle hover:text-on-surface">
                                <Eye class="h-4 w-4" />
                            </Link>
                            <Link
                                v-if="canManageEmployees"
                                :href="`/employees/${emp.id}/edit`"
                                class="rounded p-1.5 text-on-surface-variant hover:bg-surface-subtle hover:text-on-surface"
                            >
                                <Pencil class="h-4 w-4" />
                            </Link>
                            <button
                                v-if="canManageEmployees"
                                class="rounded p-1.5 text-on-surface-variant hover:bg-status-error/10 hover:text-status-error"
                                @click="deleteEmployee(emp.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!employees.data.length">
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-on-surface-variant">Tidak ada data karyawan.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div v-if="employees.last_page > 1" class="flex justify-center gap-1">
        <Link
            v-for="page in employees.last_page"
            :key="page"
            :href="employees.path + '?page=' + page"
            :class="[
                'px-3 py-1.5 text-sm font-medium rounded-lg',
                page === employees.current_page ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-subtle',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    </AppLayout>
</template>
