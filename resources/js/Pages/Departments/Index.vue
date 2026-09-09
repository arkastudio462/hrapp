<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    departments: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const applySearch = () => {
    router.get('/departments', { search: search.value }, { preserveState: true });
};

const deleteDepartment = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus departemen ini?')) {
        router.delete(`/departments/${id}`);
    }
};
</script>

<template>
    <Head title="Departemen" />

    <AppLayout title="Departemen">
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Departemen</h1>
        <Link
            href="/departments/create"
            class="inline-flex items-center gap-2 rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-sky-500/25 hover:bg-moss-700"
        >
            <Plus class="h-4 w-4" />
            Tambah Departemen
        </Link>
    </div>
    <!-- Search -->
    <div class="mt-6 max-w-md">
        <div class="relative">
            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-inkmuted" />
            <input
                v-model="search"
                type="text"
                placeholder="Cari departemen..."
                class="w-full rounded-full border border-stone-300 bg-white pl-10 pr-4 py-2.5 text-sm text-ink outline-none focus:border-sky-600"
                @keyup.enter="applySearch"
            />
        </div>
    </div>
    <!-- Table -->
    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100 shadow-sm bg-white">
        <table class="w-full">
            <thead>
                <tr class="border-b border-stone-100 bg-stone-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Parent</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Budget</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="dept in departments.data" :key="dept.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                    <td class="px-4 py-3 text-sm font-semibold text-ink">{{ dept.name }}</td>
                    <td class="px-4 py-3 text-sm text-inkmuted">{{ dept.parent?.name || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-ink">{{ dept.budget ? `Rp ${Number(dept.budget).toLocaleString('id-ID')}` : '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Link :href="`/departments/${dept.id}/edit`" class="rounded p-1.5 text-inkmuted hover:bg-stone-100 hover:text-ink">
                                <Pencil class="h-4 w-4" />
                            </Link>
                            <button class="rounded p-1.5 text-inkmuted hover:bg-red-50 hover:text-red-600" @click="deleteDepartment(dept.id)">
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!departments.data.length">
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-inkmuted">Tidak ada data departemen.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div v-if="departments.last_page > 1" class="mt-4 flex justify-center gap-1">
        <Link
            v-for="page in departments.last_page"
            :key="page"
            :href="departments.path + '?page=' + page"
            :class="[
                'px-3 py-1.5 text-sm font-medium rounded-lg',
                page === departments.current_page ? 'bg-sky-600 text-white' : 'text-inkmuted hover:bg-stone-100',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    </AppLayout>
</template>
