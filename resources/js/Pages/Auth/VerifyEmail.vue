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

    <div class="flex min-h-screen items-center justify-center bg-cream px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="rounded-3xl bg-white p-8 shadow-card sm:p-10">
                <div class="text-center">
                    <Link href="/" class="inline-flex items-center gap-3">
                        <div class="bg-coral flex h-10 w-10 items-center justify-center rounded-2xl text-xl font-bold text-white shadow-card shadow-coral/20">
                            H
                            <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-coral-light"></span>
                        </div>
                        <span class="text-ink-app text-2xl font-extrabold tracking-tight">HRHub<span class="text-coral">.</span></span>
                    </Link>
                    <h2 class="text-ink-app mt-6 text-2xl font-extrabold tracking-tight">Verifikasi email Anda</h2>
                    <p class="text-muted-app mt-2 text-sm">Terima kasih telah mendaftar! Silakan verifikasi email Anda dengan mengklik link yang kami kirimkan.</p>
                </div>

                <div v-if="status === 'verification-link-sent'" class="rounded-xl bg-sage/20 p-4 text-sm font-semibold text-green-700">
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center rounded-2xl bg-coral px-4 py-3.5 text-sm font-extrabold text-white shadow-soft transition-all hover:bg-coral-deep active:scale-[0.98] disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Mengirim...</span>
                            <span v-else>Kirim Ulang Link Verifikasi</span>
                        </button>
                    </div>

                    <div class="text-center text-sm text-muted-app">
                        <Link href="/logout" method="post" as="button" class="font-bold text-coral hover:text-coral-deep">Logout</Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
