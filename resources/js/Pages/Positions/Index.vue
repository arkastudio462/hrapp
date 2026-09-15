<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Plus, Search, Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    positions: Object,
    departments: Array,
    filters: Object,
});

const page = usePage();
const isActive = computed(() => page.url.startsWith('/positions'));

const search = ref(props.filters?.search || '');
const departmentId = ref(props.filters?.department_id || '');

const applyFilters = () => {
    router.get('/positions', {
        search: search.value,
        department_id: departmentId.value,
    }, { preserveState: true });
};

const deletePosition = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus jabatan ini?')) {
        router.delete(`/positions/${id}`);
    }
};
</script>

<template>
    <Head title="Jabatan" />

    <AppLayout title="Departemen & Jabatan">
    <!-- Tabs -->
    <div class="flex items-center gap-1 rounded-xl bg-cream p-1">
        <Link
            href="/departments"
            :class="[
                'flex-1 rounded-lg px-4 py-2 text-center text-sm font-semibold transition-all sm:flex-none',
                !isActive ? 'bg-white text-ink-app shadow-sm' : 'text-muted-app hover:text-ink-app',
            ]"
        >
            Departemen
        </Link>
        <Link
            href="/positions"
            :class="[
                'flex-1 rounded-lg px-4 py-2 text-center text-sm font-semibold transition-all sm:flex-none',
                isActive ? 'bg-white text-ink-app shadow-sm' : 'text-muted-app hover:text-ink-app',
            ]"
        >
            Jabatan
        </Link>
    </div>

    <div class="mt-6 flex items-center justify-between">
        <h1 class="font-display text-xl font-extrabold tracking-tight text-ink-app sm:text-2xl">Jabatan</h1>
        <Link
            href="/positions/create"
            class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-colors hover:bg-coral-deep"
        >
            <Plus class="h-4 w-4" />
            <span class="hidden sm:inline">Tambah Jabatan</span>
            <span class="sm:hidden">Tambah</span>
        </Link>
    </div>

    <!-- Filters -->
    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-app" />
            <input
                v-model="search"
                type="text"
                placeholder="Cari jabatan..."
                class="w-full rounded-xl border border-outline-app/50 bg-white pl-10 pr-4 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                @keyup.enter="applyFilters"
            />
        </div>
        <select
            v-model="departmentId"
            class="rounded-xl border border-outline-app/50 bg-white px-4 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">Semua Departemen</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
        </select>
    </div>

    <!-- Table -->
    <div class="max-h-[600px] mt-6 overflow-auto rounded-2xl bg-white shadow-soft">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline-app/30 bg-cream">
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Nama</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Departemen</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Level</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Salary Grade</th>
                    <th class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="pos in positions.data" :key="pos.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                    <td class="px-4 py-3 text-sm font-bold text-ink-app">{{ pos.name }}</td>
                    <td class="px-4 py-3 text-sm text-muted-app">{{ pos.department?.name || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ pos.level }}</td>
                    <td class="px-4 py-3 text-sm text-muted-app">{{ pos.salary_grade || '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Link :href="`/positions/${pos.id}/edit`" class="rounded p-1.5 text-muted-app hover:bg-cream hover:text-ink-app">
                                <Pencil class="h-4 w-4" />
                            </Link>
                            <button class="rounded p-1.5 text-muted-app hover:bg-coral-deep/10 hover:text-coral-deep" @click="deletePosition(pos.id)">
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!positions.data.length">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-app">Tidak ada data jabatan.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div v-if="positions.last_page > 1" class="mt-4 flex justify-center gap-1">
        <Link
            v-for="pg in positions.last_page"
            :key="pg"
            :href="positions.path + '?page=' + pg + '&department_id=' + departmentId"
            :class="[
                'px-3 py-1.5 text-sm font-semibold rounded-xl',
                pg === positions.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
            ]"
        >
            {{ pg }}
        </Link>
    </div>
    </AppLayout>
</template>
