<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, AlertCircle, Loader2, CheckCircle } from 'lucide-vue-next';

const props = defineProps({
    departments: Array,
});

const form = useForm({
    name: '',
    parent_id: '',
    budget: '',
});

const getFieldError = (field) => form.errors[field] || null;

const submit = () => form.post('/departments', { preserveScroll: true });
</script>

<template>
    <Head title="Tambah Departemen" />

    <AppLayout title="Tambah Departemen">
    <Link href="/departments" class="inline-flex items-center gap-1 text-sm font-semibold text-on-surface-variant transition-colors hover:text-on-surface">
        <ArrowLeft class="h-4 w-4" />
        Kembali
    </Link>
        <div class="w-full py-4 sm:mx-auto sm:max-w-2xl sm:py-8">
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink-app">Tambah Departemen</h1>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ink-app">Nama Departemen *</label>
                            <input v-model="form.name" type="text" required :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('name') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']" placeholder="Contoh: Marketing" />
                            <p v-if="getFieldError('name')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('name') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink-app">Parent Department</label>
                            <select v-model="form.parent_id" :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('parent_id') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']">
                                <option value="">Tidak ada (Top Level / Induk)</option>
                                <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                            </select>
                            <p v-if="getFieldError('parent_id')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('parent_id') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink-app">Budget (Opsional)</label>
                            <input v-model="form.budget" type="number" min="0" :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('budget') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']" placeholder="0" />
                            <p v-if="getFieldError('budget')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('budget') }}</p>
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
                    <span>{{ form.recentlySuccessful ? 'Tersimpan!' : 'Simpan Departemen' }}</span>
                </button>
            </form>
        </div>
    </AppLayout>
</template>
