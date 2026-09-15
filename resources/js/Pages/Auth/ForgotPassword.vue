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
                    <h2 class="text-ink-app mt-6 text-2xl font-extrabold tracking-tight">Lupa password?</h2>
                    <p class="text-muted-app mt-2 text-sm">Masukkan email Anda dan kami akan mengirimkan link untuk reset password.</p>
                </div>

                <div v-if="status" class="rounded-xl bg-sage/20 p-4 text-sm font-semibold text-green-700">
                    {{ status }}
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div>
                        <label for="email" class="text-ink-app block text-sm font-bold">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                            placeholder="email@perusahaan.com"
                            required
                            autofocus
                        />
                        <div v-if="form.errors.email" class="text-coral-deep mt-1.5 text-sm font-semibold">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center rounded-2xl bg-coral px-4 py-3.5 text-sm font-extrabold text-white shadow-soft transition-all hover:bg-coral-deep active:scale-[0.98] disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Mengirim...</span>
                            <span v-else>Kirim Link Reset</span>
                        </button>
                    </div>

                    <div class="text-center text-sm text-muted-app">
                        <Link href="/login" class="font-bold text-coral hover:text-coral-deep">Kembali ke login</Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
