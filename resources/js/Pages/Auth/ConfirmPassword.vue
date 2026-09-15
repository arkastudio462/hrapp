<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post('/confirm-password', {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Konfirmasi Password" />

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
                    <h2 class="text-ink-app mt-6 text-2xl font-extrabold tracking-tight">Konfirmasi password Anda</h2>
                    <p class="text-muted-app mt-2 text-sm">Silakan masukkan password Anda untuk melanjutkan.</p>
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div>
                        <label for="password" class="text-ink-app block text-sm font-bold">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                            placeholder="Masukkan password"
                            required
                            autofocus
                        />
                        <div v-if="form.errors.password" class="text-coral-deep mt-1.5 text-sm font-semibold">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center rounded-2xl bg-coral px-4 py-3.5 text-sm font-extrabold text-white shadow-soft transition-all hover:bg-coral-deep active:scale-[0.98] disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Konfirmasi...</span>
                            <span v-else>Konfirmasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
