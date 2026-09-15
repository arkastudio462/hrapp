<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Plus, Search, Pencil, Trash2, Building2 } from 'lucide-vue-next';

const props = defineProps({
    departments: Object,
    filters: Object,
});

const page = usePage();
const isActive = computed(() => page.url.startsWith('/departments'));

const search = ref(props.filters?.search || '');
const typeFilter = ref(props.filters?.type || '');

const applyFilters = () => {
    router.get('/departments', {
        search: search.value,
        type: typeFilter.value,
    }, { preserveState: true });
};

const deleteDepartment = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus departemen ini?')) {
        router.delete(`/departments/${id}`);
    }
};
</script>

<template>
    <Head title="Departemen" />

    <AppLayout title="Departemen & Jabatan">
    <!-- Tabs -->
    <div class="flex items-center gap-1 rounded-xl bg-cream p-1">
        <Link
            href="/departments"
            :class="[
                'flex-1 rounded-lg px-4 py-2 text-center text-sm font-semibold transition-all sm:flex-none',
                isActive ? 'bg-white text-ink-app shadow-sm' : 'text-muted-app hover:text-ink-app',
            ]"
        >
            Departemen
        </Link>
        <Link
            href="/positions"
            :class="[
                'flex-1 rounded-lg px-4 py-2 text-center text-sm font-semibold transition-all sm:flex-none',
                !isActive ? 'bg-white text-ink-app shadow-sm' : 'text-muted-app hover:text-ink-app',
            ]"
        >
            Jabatan
        </Link>
    </div>

    <div class="mt-6 flex items-center justify-between">
        <h1 class="font-display text-xl font-extrabold tracking-tight text-ink-app sm:text-2xl">Departemen</h1>
        <Link
            href="/departments/create"
            class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-colors hover:bg-coral-deep"
        >
            <Plus class="h-4 w-4" />
            <span class="hidden sm:inline">Tambah Departemen</span>
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
                placeholder="Cari departemen..."
                class="w-full rounded-xl border border-outline-app/50 bg-white pl-10 pr-4 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                @keyup.enter="applyFilters"
            />
        </div>
        <div class="flex gap-2">
            <button
                :class="[
                    'rounded-xl px-4 py-2.5 text-sm font-semibold transition-all',
                    typeFilter === '' ? 'bg-coral text-white shadow-md' : 'bg-white text-muted-app border border-outline-app/50 hover:text-ink-app',
                ]"
                @click="typeFilter = ''; applyFilters()"
            >
                Semua
            </button>
            <button
                :class="[
                    'rounded-xl px-4 py-2.5 text-sm font-semibold transition-all',
                    typeFilter === 'parent' ? 'bg-coral text-white shadow-md' : 'bg-white text-muted-app border border-outline-app/50 hover:text-ink-app',
                ]"
                @click="typeFilter = 'parent'; applyFilters()"
            >
                Induk
            </button>
            <button
                :class="[
                    'rounded-xl px-4 py-2.5 text-sm font-semibold transition-all',
                    typeFilter === 'child' ? 'bg-coral text-white shadow-md' : 'bg-white text-muted-app border border-outline-app/50 hover:text-ink-app',
                ]"
                @click="typeFilter = 'child'; applyFilters()"
            >
                Sub-Departemen
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="max-h-[600px] mt-6 overflow-auto rounded-2xl bg-white shadow-soft">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline-app/30 bg-cream">
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Nama</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Parent</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Budget</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Sub</th>
                    <th class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="dept in departments.data" :key="dept.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div v-if="!dept.parent_id" class="flex size-7 items-center justify-center rounded-lg bg-coral/10 text-coral">
                                <Building2 class="h-3.5 w-3.5" />
                            </div>
                            <span class="text-sm font-bold text-ink-app">{{ dept.name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-muted-app">{{ dept.parent?.name || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ dept.budget ? `Rp ${Number(dept.budget).toLocaleString('id-ID')}` : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-muted-app">{{ dept.children?.length || 0 }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Link :href="`/departments/${dept.id}/edit`" class="rounded p-1.5 text-muted-app hover:bg-cream hover:text-ink-app">
                                <Pencil class="h-4 w-4" />
                            </Link>
                            <button class="rounded p-1.5 text-muted-app hover:bg-coral-deep/10 hover:text-coral-deep" @click="deleteDepartment(dept.id)">
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!departments.data.length">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-app">Tidak ada data departemen.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div v-if="departments.last_page > 1" class="mt-4 flex justify-center gap-1">
        <Link
            v-for="pg in departments.last_page"
            :key="pg"
            :href="departments.path + '?page=' + pg + '&type=' + typeFilter"
            :class="[
                'px-3 py-1.5 text-sm font-semibold rounded-xl',
                pg === departments.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
            ]"
        >
            {{ pg }}
        </Link>
    </div>
    </AppLayout>
</template>
