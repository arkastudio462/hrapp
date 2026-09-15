<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';

const form = ref({
    name: '',
    slug: '',
    price_monthly: 0,
    price_yearly: 0,
    max_employees: 10,
    max_storage_gb: 1,
    features: {
        attendance: true,
        payroll: true,
        face_detection: false,
        reports: true,
        api_access: false,
    },
    is_active: true,
});

const generateSlug = () => {
    form.value.slug = form.value.name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');
};

const submit = () => {
    router.post('/super-admin/packages', form.value);
};
</script>

<template>
    <Head title="Tambah Paket" />

    <AppLayout title="Tambah Paket">
    <Link href="/super-admin/packages" class="inline-flex items-center gap-1 text-sm font-bold text-muted-app hover:text-ink-app">
        <ArrowLeft class="h-4 w-4" />
        Kembali
    </Link>

    <div class="rounded-2xl bg-white p-6 shadow-soft">
        <h2 class="text-lg font-extrabold text-ink-app">Tambah Paket Baru</h2>

        <form class="mt-6 space-y-6" @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-bold text-ink-app">Nama Paket</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                        @input="generateSlug"
                        required
                    />
                </div>
                <div>
                    <label class="block text-sm font-bold text-ink-app">Slug</label>
                    <input
                        v-model="form.slug"
                        type="text"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                        required
                    />
                </div>
                <div>
                    <label class="block text-sm font-bold text-ink-app">Harga Bulanan (IDR)</label>
                    <input
                        v-model.number="form.price_monthly"
                        type="number"
                        min="0"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                        required
                    />
                </div>
                <div>
                    <label class="block text-sm font-bold text-ink-app">Harga Tahunan (IDR)</label>
                    <input
                        v-model.number="form.price_yearly"
                        type="number"
                        min="0"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                        required
                    />
                </div>
                <div>
                    <label class="block text-sm font-bold text-ink-app">Max Karyawan</label>
                    <input
                        v-model.number="form.max_employees"
                        type="number"
                        min="1"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                        required
                    />
                </div>
                <div>
                    <label class="block text-sm font-bold text-ink-app">Max Storage (GB)</label>
                    <input
                        v-model.number="form.max_storage_gb"
                        type="number"
                        min="1"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                        required
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-ink-app mb-3">Fitur</label>
                <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                    <label
                        v-for="(value, key) in form.features"
                        :key="key"
                        class="flex items-center gap-2 rounded-xl border border-outline-app/30 p-3 cursor-pointer hover:bg-cream/50"
                    >
                        <input
                            v-model="form.features[key]"
                            type="checkbox"
                            class="h-4 w-4 rounded border-outline-app/50 text-coral focus:ring-coral"
                        />
                        <span class="text-sm text-ink-app">{{ key.replace(/_/g, ' ') }}</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-outline-app/30">
                <Link
                    href="/super-admin/packages"
                    class="flex-1 rounded-xl border border-outline-app/50 bg-white px-4 py-2 text-center text-sm font-semibold text-ink-app transition-colors hover:bg-cream"
                >
                    Batal
                </Link>
                <button
                    type="submit"
                    class="flex-1 rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-coral-deep"
                >
                    Simpan Paket
                </button>
            </div>
        </form>
    </div>
    </AppLayout>
</template>
