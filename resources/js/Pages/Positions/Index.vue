<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    positions: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const applySearch = () => {
    router.get('/positions', { search: search.value }, { preserveState: true });
};

const deletePosition = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus jabatan ini?')) {
        router.delete(`/positions/${id}`);
    }
};
</script>

<template>
    <Head title="Jabatan" />

    <AppLayout title="Jabatan">
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Jabatan</h1>
        <Link
            href="/positions/create"
            class="inline-flex items-center gap-2 rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-sky-500/25 hover:bg-moss-700"
        >
            <Plus class="h-4 w-4" />
            Tambah Jabatan
        </Link>
    </div>
    <!-- Search -->
    <div class="mt-6 max-w-md">
        <div class="relative">
            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-inkmuted" />
            <input
                v-model="search"
                type="text"
                placeholder="Cari jabatan..."
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
                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Level</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-inkmuted">Salary Grade</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-inkmuted">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="pos in positions.data" :key="pos.id" class="border-b border-stone-100 last:border-0 hover:bg-stone-50">
                    <td class="px-4 py-3 text-sm font-semibold text-ink">{{ pos.name }}</td>
                    <td class="px-4 py-3 text-sm text-ink">{{ pos.level }}</td>
                    <td class="px-4 py-3 text-sm text-inkmuted">{{ pos.salary_grade || '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Link :href="`/positions/${pos.id}/edit`" class="rounded p-1.5 text-inkmuted hover:bg-stone-100 hover:text-ink">
                                <Pencil class="h-4 w-4" />
                            </Link>
                            <button class="rounded p-1.5 text-inkmuted hover:bg-red-50 hover:text-red-600" @click="deletePosition(pos.id)">
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!positions.data.length">
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-inkmuted">Tidak ada data jabatan.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div v-if="positions.last_page > 1" class="mt-4 flex justify-center gap-1">
        <Link
            v-for="page in positions.last_page"
            :key="page"
            :href="positions.path + '?page=' + page"
            :class="[
                'px-3 py-1.5 text-sm font-medium rounded-lg',
                page === positions.current_page ? 'bg-sky-600 text-white' : 'text-inkmuted hover:bg-stone-100',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    </AppLayout>
</template>
