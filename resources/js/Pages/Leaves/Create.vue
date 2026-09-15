<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, AlertCircle, Loader2, CheckCircle } from 'lucide-vue-next';

const props = defineProps({
    employee: Object,
    balances: Array,
});

const form = useForm({
    type: 'leave',
    start_date: '',
    end_date: '',
    reason: '',
});

const getFieldError = (field) => form.errors[field] || null;

const submit = () => form.post('/leaves', { preserveScroll: true });

const getBalance = (type) => {
    const balance = props.balances.find(b => b.type === type);
    return balance ? `${balance.remaining}/${balance.total}` : '-';
};
</script>

<template>
    <Head title="Ajukan Izin/Cuti" />

    <AppLayout title="Ajukan Izin">
    <Link href="/leaves" class="inline-flex items-center gap-1 text-sm font-semibold text-on-surface-variant transition-colors hover:text-on-surface">
        <ArrowLeft class="h-4 w-4" />
        Kembali
    </Link>
        <div class="w-full py-4 sm:mx-auto sm:max-w-2xl sm:py-8">
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink-app">Ajukan Izin/Cuti</h1>
            <!-- Balance -->
            <div class="mt-6 grid grid-cols-3 gap-4">
                <div class="rounded-2xl border border-outline-app/30 shadow-sm bg-white p-4 text-center">
                    <p class="text-sm text-muted-app">Cuti Tersisa</p>
                    <p class="mt-1 font-display text-xl font-bold text-ink-app">{{ getBalance('annual') }}</p>
                </div>
                <div class="rounded-2xl border border-outline-app/30 shadow-sm bg-white p-4 text-center">
                    <p class="text-sm text-muted-app">Sakit Tersisa</p>
                    <p class="mt-1 font-display text-xl font-bold text-ink-app">{{ getBalance('sick') }}</p>
                </div>
                <div class="rounded-2xl border border-outline-app/30 shadow-sm bg-white p-4 text-center">
                    <p class="text-sm text-muted-app">Izin Tersisa</p>
                    <p class="mt-1 font-display text-xl font-bold text-ink-app">{{ getBalance('permission') }}</p>
                </div>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="rounded-2xl border border-outline-app/30 shadow-sm bg-white p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ink-app">Tipe *</label>
                            <select v-model="form.type" required :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('type') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']">
                                <option value="leave">Cuti</option>
                                <option value="sick">Sakit</option>
                                <option value="permission">Izin</option>
                            </select>
                            <p v-if="getFieldError('type')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('type') }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-ink-app">Tanggal Mulai *</label>
                                <input v-model="form.start_date" type="date" required :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('start_date') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']" />
                                <p v-if="getFieldError('start_date')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('start_date') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink-app">Tanggal Selesai *</label>
                                <input v-model="form.end_date" type="date" required :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('end_date') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']" />
                                <p v-if="getFieldError('end_date')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('end_date') }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink-app">Alasan *</label>
                            <textarea v-model="form.reason" rows="3" required :class="['mt-1.5 block w-full rounded-xl border bg-white px-5 py-3 text-ink-app outline-none', getFieldError('reason') ? 'border-coral' : 'border-outline-app/50 focus:border-coral']" placeholder="Jelaskan alasan pengajuan..." />
                            <p v-if="getFieldError('reason')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('reason') }}</p>
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
                    <span>{{ form.recentlySuccessful ? 'Tersimpan!' : 'Kirim Pengajuan' }}</span>
                </button>
            </form>
        </div>
    </AppLayout>
</template>
