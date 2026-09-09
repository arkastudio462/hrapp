<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    position: Object,
});

const form = useForm({
    name: props.position.name,
    level: props.position.level,
    salary_grade: props.position.salary_grade || '',
});

const submit = () => form.put(`/positions/${props.position.id}`);
</script>

<template>
    <Head title="Edit Jabatan" />

    <AppLayout title="Edit Jabatan">
    <Link href="/positions" class="inline-flex items-center gap-1 text-sm font-semibold text-on-surface-variant transition-colors hover:text-on-surface">
        <ArrowLeft class="h-4 w-4" />
        Kembali
    </Link>
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Edit Jabatan</h1>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ink">Nama Jabatan *</label>
                            <input v-model="form.name" type="text" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-sky-600" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink">Level *</label>
                            <input v-model="form.level" type="number" min="1" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-sky-600" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink">Salary Grade</label>
                            <input v-model="form.salary_grade" type="text" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-sky-600" />
                        </div>
                    </div>
                </div>
                <button
                    type="submit"
                    class="w-full rounded-full bg-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-sky-500/25 hover:bg-moss-700 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Perbarui Jabatan
                </button>
            </form>
        </div>
    </AppLayout>
</template>
