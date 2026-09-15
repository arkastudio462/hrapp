<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, ToggleLeft, ToggleRight } from 'lucide-vue-next';

const props = defineProps({
    packages: Array,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const toggleActive = (id) => {
    router.post(`/super-admin/packages/${id}/toggle`);
};

const deletePackage = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus paket ini?')) {
        router.delete(`/super-admin/packages/${id}`);
    }
};
</script>

<template>
    <Head title="Paket Langganan" />

    <AppLayout title="Paket Langganan">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Paket Langganan</h1>
            <p class="text-sm text-muted-app">Kelola paket subscription yang tersedia</p>
        </div>
        <Link
            href="/super-admin/packages/create"
            class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-coral-deep"
        >
            <Plus class="h-4 w-4" />
            Tambah Paket
        </Link>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div
            v-for="pkg in packages"
            :key="pkg.id"
            class="rounded-2xl bg-white p-6 shadow-soft"
        >
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-ink-app">{{ pkg.name }}</h3>
                    <p class="text-sm text-muted-app">{{ pkg.slug }}</p>
                </div>
                <span
                    :class="[
                        'rounded-full px-2.5 py-0.5 text-[11px] font-extrabold',
                        pkg.is_active ? 'bg-sage/50 text-green-700' : 'bg-cream text-muted-app',
                    ]"
                >
                    {{ pkg.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-muted-app">Harga Bulanan</span>
                    <span class="font-semibold text-ink-app">{{ formatCurrency(pkg.price_monthly) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-app">Harga Tahunan</span>
                    <span class="font-semibold text-ink-app">{{ formatCurrency(pkg.price_yearly) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-app">Max Karyawan</span>
                    <span class="font-semibold text-ink-app">{{ pkg.max_employees }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-app">Max Storage</span>
                    <span class="font-semibold text-ink-app">{{ pkg.max_storage_gb }} GB</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-app">Tenant Terdaftar</span>
                    <span class="font-semibold text-ink-app">{{ pkg.tenants_count }}</span>
                </div>
            </div>

            <div v-if="pkg.features" class="mt-4">
                <p class="text-[11px] font-extrabold text-muted-app mb-2">Fitur:</p>
                <div class="flex flex-wrap gap-1">
                    <span
                        v-for="(value, key) in pkg.features"
                        :key="key"
                        v-show="value"
                        class="rounded-lg bg-coral/10 px-2 py-0.5 text-xs font-semibold text-coral-deep"
                    >
                        {{ key }}
                    </span>
                </div>
            </div>

            <div class="mt-4 flex gap-2 border-t border-outline-app/30 pt-4">
                <button
                    @click="toggleActive(pkg.id)"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors hover:bg-cream"
                >
                    <ToggleRight v-if="pkg.is_active" class="h-4 w-4 text-green-700" />
                    <ToggleLeft v-else class="h-4 w-4 text-muted-app" />
                    {{ pkg.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
                <Link
                    :href="`/super-admin/packages/${pkg.id}/edit`"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold text-muted-app hover:bg-cream hover:text-ink-app"
                >
                    <Pencil class="h-3.5 w-3.5" />
                    Edit
                </Link>
                <button
                    @click="deletePackage(pkg.id)"
                    class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold text-coral-deep hover:bg-coral-deep/10"
                >
                    <Trash2 class="h-3.5 w-3.5" />
                    Hapus
                </button>
            </div>
        </div>
    </div>
    </AppLayout>
</template>
