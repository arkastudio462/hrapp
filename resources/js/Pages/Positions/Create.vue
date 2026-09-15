<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, AlertCircle, Loader2, CheckCircle } from 'lucide-vue-next';

const props = defineProps({
    departments: Array,
});

const form = useForm({
    name: '',
    department_id: '',
    level: 1,
    salary_grade: '',
});

const getFieldError = (field) => form.errors[field] || null;

const submit = () => form.post('/positions', { preserveScroll: true });
</script>

<template>
    <Head title="Tambah Jabatan" />

    <AppLayout title="Tambah Jabatan">
    <Link href="/positions" class="inline-flex items-center gap-1 text-sm font-semibold text-on-surface-variant transition-colors hover:text-on-surface">
        <ArrowLeft class="h-4 w-4" />
        Kembali
    </Link>
        <div class="w-full py-4 sm:mx-auto sm:max-w-2xl sm:py-8">
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink-app">Tambah Jabatan</h1>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ink-app">Nama Jabatan *</label>
                            <input v-model="form.name" type="text" required :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('name') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']" placeholder="Contoh: Crew, Supervisor, Manager" />
                            <p v-if="getFieldError('name')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('name') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink-app">Departemen *</label>
                            <select v-model="form.department_id" required :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('department_id') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']">
                                <option value="">Pilih Departemen</option>
                                <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                            </select>
                            <p v-if="getFieldError('department_id')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('department_id') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink-app">Level *</label>
                            <input v-model="form.level" type="number" min="1" required :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('level') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']" />
                            <p v-if="getFieldError('level')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('level') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink-app">Salary Grade</label>
                            <input v-model="form.salary_grade" type="text" :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('salary_grade') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']" placeholder="Contoh: B1" />
                            <p v-if="getFieldError('salary_grade')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('salary_grade') }}</p>
                        </div>
                    </div>
                </div>
                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-coral px-4 py-3 text-sm font-semibold text-white shadow-md shadow-coral/20 transition-colors hover:bg-coral-deep disabled:opacity-50"
                    :disabled="form.processing"
                >
                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                    <CheckCircle v-else-if="form.recentlySuccessful" class="size-4" />
                    <span>{{ form.recentlySuccessful ? 'Tersimpan!' : 'Simpan Jabatan' }}</span>
                </button>
            </form>
        </div>
    </AppLayout>
</template>
