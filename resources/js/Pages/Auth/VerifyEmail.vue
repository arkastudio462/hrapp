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

    <div class="flex min-h-screen items-center justify-center bg-background px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div>
                <h1 class="text-center text-3xl font-bold text-primary">HRHub</h1>
                <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-foreground">
                    Verifikasi email Anda
                </h2>
                <p class="mt-2 text-center text-sm text-muted-foreground">
                    Terima kasih telah mendaftar! Silakan verifikasi email Anda dengan mengklik link yang kami kirimkan.
                </p>
            </div>

            <div v-if="status === 'verification-link-sent'" class="rounded-md bg-green-50 p-4">
                <div class="text-sm text-green-700">
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div>
                    <button
                        type="submit"
                        class="group relative flex w-full justify-center rounded-md bg-primary px-4 py-3 text-sm font-semibold text-primary-foreground hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Mengirim...</span>
                        <span v-else>Kirim Ulang Link Verifikasi</span>
                    </button>
                </div>

                <div class="text-center text-sm text-muted-foreground">
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="font-medium text-primary hover:text-primary/80"
                    >
                        Logout
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
