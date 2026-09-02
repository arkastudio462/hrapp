<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    name: '',
    level: 1,
    salary_grade: '',
});

const submit = () => form.post('/positions');
</script>

<template>
    <Head title="Tambah Jabatan" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <Link href="/positions" class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Tambah Jabatan</h1>

                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-ink">Nama Jabatan *</label>
                                <input v-model="form.name" type="text" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" placeholder="Contoh: Staff Marketing" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Level *</label>
                                <input v-model="form.level" type="number" min="1" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Salary Grade</label>
                                <input v-model="form.salary_grade" type="text" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" placeholder="Contoh: B1" />
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Simpan Jabatan
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
