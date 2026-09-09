<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

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

const submit = () => form.post('/leaves');

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
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Ajukan Izin/Cuti</h1>
            <!-- Balance -->
            <div class="mt-6 grid grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-4 text-center">
                    <p class="text-sm text-inkmuted">Cuti Tersisa</p>
                    <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('annual') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-4 text-center">
                    <p class="text-sm text-inkmuted">Sakit Tersisa</p>
                    <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('sick') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-4 text-center">
                    <p class="text-sm text-inkmuted">Izin Tersisa</p>
                    <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('permission') }}</p>
                </div>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ink">Tipe *</label>
                            <select v-model="form.type" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-sky-600">
                                <option value="leave">Cuti</option>
                                <option value="sick">Sakit</option>
                                <option value="permission">Izin</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-ink">Tanggal Mulai *</label>
                                <input v-model="form.start_date" type="date" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-sky-600" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Tanggal Selesai *</label>
                                <input v-model="form.end_date" type="date" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-sky-600" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink">Alasan *</label>
                            <textarea v-model="form.reason" rows="3" required class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-sky-600" placeholder="Jelaskan alasan pengajuan..." />
                        </div>
                    </div>
                </div>
                <button
                    type="submit"
                    class="w-full rounded-full bg-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-sky-500/25 hover:bg-moss-700 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Kirim Pengajuan
                </button>
            </form>
        </div>
    </AppLayout>
</template>
