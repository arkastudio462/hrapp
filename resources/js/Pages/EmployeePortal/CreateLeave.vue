<script setup>
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    employee: Object,
});

const form = useForm({
    type: 'permission',
    start_date: '',
    end_date: '',
    reason: '',
});

const submit = () => {
    form.post('/my-leave');
};
</script>

<template>
    <Head title="Ajukan Izin & Cuti" />

    <EmployeeLayout title="Ajukan Izin">
        <Link href="/my-leave" class="inline-flex items-center gap-1 text-sm font-bold text-muted-app transition-colors hover:text-ink-app">
            <ArrowLeft class="size-4" />
            Kembali
        </Link>

        <h1 class="mt-3 text-xl font-extrabold tracking-tight text-ink-app">Ajukan Izin/Cuti</h1>
        <p class="mt-0.5 text-sm text-muted-app">Isi form berikut untuk mengajukan izin atau cuti.</p>

        <form class="mt-5 space-y-4" @submit.prevent="submit">
            <div class="rounded-2xl bg-white p-5 shadow-soft space-y-4">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-bold text-ink-app">Tipe Pengajuan</label>
                    <select
                        v-model="form.type"
                        class="mt-1.5 block w-full rounded-xl border-2 border-outline-app bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                    >
                        <option value="permission">Izin / Dinas</option>
                        <option value="sick">Izin Sakit</option>
                        <option value="leave">Cuti Tahunan</option>
                    </select>
                    <p v-if="form.errors.type" class="mt-1 text-xs font-semibold text-coral-deep">{{ form.errors.type }}</p>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-bold text-ink-app">Tanggal Mulai</label>
                        <input
                            v-model="form.start_date"
                            type="date"
                            class="mt-1.5 block w-full rounded-xl border-2 border-outline-app bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                        />
                        <p v-if="form.errors.start_date" class="mt-1 text-xs font-semibold text-coral-deep">{{ form.errors.start_date }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink-app">Tanggal Akhir</label>
                        <input
                            v-model="form.end_date"
                            type="date"
                            class="mt-1.5 block w-full rounded-xl border-2 border-outline-app bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                        />
                        <p v-if="form.errors.end_date" class="mt-1 text-xs font-semibold text-coral-deep">{{ form.errors.end_date }}</p>
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-bold text-ink-app">Alasan</label>
                    <textarea
                        v-model="form.reason"
                        rows="3"
                        placeholder="Jelaskan alasan pengajuan izin/cuti..."
                        class="mt-1.5 block w-full rounded-xl border-2 border-outline-app bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app placeholder:text-muted-app outline-none focus:border-coral"
                    ></textarea>
                    <p v-if="form.errors.reason" class="mt-1 text-xs font-semibold text-coral-deep">{{ form.errors.reason }}</p>
                </div>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-2xl bg-coral px-4 py-3.5 text-sm font-extrabold text-white shadow-soft transition-all hover:bg-coral-deep active:scale-[0.98] disabled:opacity-50"
            >
                {{ form.processing ? 'Mengirim...' : 'Kirim Pengajuan' }}
            </button>
        </form>
    </EmployeeLayout>
</template>
