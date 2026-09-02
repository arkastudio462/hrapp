<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
        default: '',
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>
    <Head title="Lupa Password" />

    <div class="flex min-h-screen items-center justify-center bg-white px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <Link href="/" class="inline-flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-[3px] bg-ink">
                        <span class="h-2.5 w-2.5 rounded-[1px] bg-white" />
                    </span>
                    <span class="font-display text-xl font-bold tracking-tight">HRapp</span>
                </Link>
                <h2 class="mt-8 font-display text-2xl font-bold tracking-tight text-ink">
                    Lupa password?
                </h2>
                <p class="mt-2 text-sm text-inkmuted">
                    Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
                </p>
            </div>

            <div v-if="status" class="rounded-xl border border-stone-200 bg-moss-50 p-4 text-sm text-moss-700">
                {{ status }}
            </div>

            <form class="mt-8 space-y-5" @submit.prevent="submit">
                <div>
                    <label for="email" class="block text-sm font-medium text-ink">
                        Email
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none transition-colors placeholder:text-inkmuted/60 focus:border-ink"
                        placeholder="email@perusahaan.com"
                        required
                        autofocus
                    />
                    <div v-if="form.errors.email" class="mt-1.5 text-sm text-red-600">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="flex w-full justify-center rounded-full bg-ink px-4 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-moss-700 focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Mengirim...</span>
                        <span v-else>Kirim Link Reset</span>
                    </button>
                </div>

                <div class="text-center text-sm text-inkmuted">
                    <Link
                        href="/login"
                        class="font-semibold text-ink hover:underline"
                    >
                        Kembali ke login
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
