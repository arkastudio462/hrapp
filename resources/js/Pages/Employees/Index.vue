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
    contract: 'bg-cream text-muted-app',
    permanent: 'bg-sage/50 text-green-700',
    probation: 'bg-amber/40 text-yellow-700',
};
</script>

<template>
    <Head title="Karyawan" />

    <AppLayout title="Karyawan">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Karyawan</h1>
            <p class="text-sm text-muted-app">Kelola data karyawan perusahaan</p>
        </div>
        <Link
            v-if="canManageEmployees"
            href="/employees/create"
            class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-coral-deep"
        >
            <Plus class="h-4 w-4" />
            Tambah Karyawan
        </Link>
    </div>
    <!-- Filters -->
    <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-app" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama, NIK, atau email..."
                    class="w-full rounded-xl border border-outline-app/50 bg-white pl-10 pr-4 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                    @keyup.enter="applyFilters"
                />
            </div>
        </div>
        <select
            v-model="departmentId"
            class="rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">Semua Departemen</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
        </select>
        <select
            v-model="status"
            class="rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">Semua Status</option>
            <option value="permanent">Permanent</option>
            <option value="contract">Kontrak</option>
            <option value="probation">Percobaan</option>
        </select>
    </div>
    <!-- Table -->
    <div class="max-h-[600px] overflow-auto rounded-2xl bg-white shadow-soft">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline-app/30 bg-cream">
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Foto</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">NIK</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Nama</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Departemen</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Jabatan</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Status</th>
                    <th class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="emp in employees.data" :key="emp.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                    <td class="px-4 py-3">
                        <img v-if="emp.photo" :src="`/storage/${emp.photo}`" class="size-8 rounded-lg object-cover" />
                        <div v-else class="flex size-8 items-center justify-center rounded-lg bg-cream text-xs font-bold text-muted-app">
                            {{ emp.name?.charAt(0) }}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm font-mono text-muted-app">{{ emp.nik }}</td>
                    <td class="px-4 py-3 text-sm font-bold text-ink-app">{{ emp.name }}</td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ emp.department?.name || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ emp.position?.name || '-' }}</td>
                    <td class="px-4 py-3">
                        <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', statusColors[emp.status]]">
                            {{ statusLabels[emp.status] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Link :href="`/employees/${emp.id}/edit`" class="rounded p-1.5 text-muted-app hover:bg-cream hover:text-ink-app">
                                <Eye class="h-4 w-4" />
                            </Link>
                            <Link
                                v-if="canManageEmployees"
                                :href="`/employees/${emp.id}/edit`"
                                class="rounded p-1.5 text-muted-app hover:bg-cream hover:text-ink-app"
                            >
                                <Pencil class="h-4 w-4" />
                            </Link>
                            <button
                                v-if="canManageEmployees"
                                class="rounded p-1.5 text-muted-app hover:bg-coral-deep/10 hover:text-coral-deep"
                                @click="deleteEmployee(emp.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!employees.data.length">
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-muted-app">Tidak ada data karyawan.</td>
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
                'px-3 py-1.5 text-sm font-semibold rounded-xl',
                page === employees.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    </AppLayout>
</template>
