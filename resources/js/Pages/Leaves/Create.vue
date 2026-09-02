<script setup>
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

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <Link href="/leaves" class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Ajukan Izin/Cuti</h1>

                <!-- Balance -->
                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="rounded-xl border border-stone-200 bg-white p-4 text-center">
                        <p class="text-sm text-inkmuted">Cuti Tersisa</p>
                        <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('annual') }}</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-4 text-center">
                        <p class="text-sm text-inkmuted">Sakit Tersisa</p>
                        <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('sick') }}</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-4 text-center">
                        <p class="text-sm text-inkmuted">Izin Tersisa</p>
                        <p class="mt-1 font-display text-xl font-bold text-ink">{{ getBalance('permission') }}</p>
                    </div>
                </div>

                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-ink">Tipe *</label>
                                <select v-model="form.type" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink">
                                    <option value="leave">Cuti</option>
                                    <option value="sick">Sakit</option>
                                    <option value="permission">Izin</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-ink">Tanggal Mulai *</label>
                                    <input v-model="form.start_date" type="date" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink">Tanggal Selesai *</label>
                                    <input v-model="form.end_date" type="date" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Alasan *</label>
                                <textarea v-model="form.reason" rows="3" required class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" placeholder="Jelaskan alasan pengajuan..." />
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
