<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Check } from 'lucide-vue-next';

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

const selectPackage = (packageId) => {
    if (confirm('Apakah Anda y ingin upgrade ke paket ini?')) {
        router.post(`/subscription/upgrade/${packageId}`);
    }
};
</script>

<template>
    <Head title="Pilih Paket" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <Link href="/subscription" class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="font-display text-3xl font-bold tracking-tight text-ink">Pilih Paket Anda</h1>
                    <p class="mt-2 text-lg text-inkmuted">Skalakan bisnis Anda dengan paket yang tepat.</p>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="pkg in packages"
                        :key="pkg.id"
                        class="relative rounded-xl border border-stone-200 bg-white p-6 transition-shadow hover:shadow-lg"
                    >
                        <div v-if="pkg.is_popular" class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-ink px-3 py-1 text-xs font-bold text-white">
                            POPULER
                        </div>

                        <h3 class="font-display text-lg font-bold text-ink">{{ pkg.name }}</h3>
                        <div class="mt-4">
                            <span class="font-display text-3xl font-bold text-ink">{{ formatCurrency(pkg.price) }}</span>
                            <span class="text-sm text-inkmuted">/bulan</span>
                        </div>

                        <ul class="mt-6 space-y-3">
                            <li class="flex items-center gap-2 text-sm text-ink">
                                <Check class="h-4 w-4 text-moss-600" />
                                {{ pkg.max_employees }} Karyawan
                            </li>
                            <li class="flex items-center gap-2 text-sm text-ink">
                                <Check class="h-4 w-4 text-moss-600" />
                                {{ pkg.max_storage_gb }} GB Storage
                            </li>
                            <li v-if="pkg.features" v-for="feature in pkg.features" :key="feature" class="flex items-center gap-2 text-sm text-ink">
                                <Check class="h-4 w-4 text-moss-600" />
                                {{ feature }}
                            </li>
                        </ul>

                        <button
                            class="mt-8 w-full rounded-full border border-stone-300 px-4 py-3 text-sm font-semibold text-ink transition-colors hover:bg-stone-50"
                            @click="selectPackage(pkg.id)"
                        >
                            Pilih Paket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
