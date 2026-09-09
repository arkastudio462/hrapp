<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
        default: '',
    },
});

const form = useForm({});

const submit = () => {
    form.post('/email/verification-notification');
};
</script>

<template>
    <Head title="Verifikasi Email" />

    <div class="flex min-h-screen items-center justify-center bg-[#fafbff] px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-xl shadow-sky-900/5 sm:p-10">
                <div class="text-center">
                    <Link href="/" class="inline-flex items-center gap-3">
                        <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-sky-600 text-xl font-bold text-white shadow-md shadow-sky-500/20">
                            H
                            <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-sky-300"></span>
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-900">HRHub<span class="text-sky-600">.</span></span>
                    </Link>
                    <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Verifikasi email Anda</h2>
                    <p class="mt-2 text-sm text-slate-500">Terima kasih telah mendaftar! Silakan verifikasi email Anda dengan mengklik link yang kami kirimkan.</p>
                </div>

                <div v-if="status === 'verification-link-sent'" class="mt-6 rounded-xl border border-sky-100 bg-sky-50 p-4 text-sm text-sky-700">
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center rounded-full bg-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-sky-500/25 transition-all hover:-translate-y-0.5 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Mengirim...</span>
                            <span v-else>Kirim Ulang Link Verifikasi</span>
                        </button>
                    </div>

                    <div class="text-center text-sm text-slate-500">
                        <Link href="/logout" method="post" as="button" class="font-semibold text-sky-600 hover:text-sky-700">Logout</Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
