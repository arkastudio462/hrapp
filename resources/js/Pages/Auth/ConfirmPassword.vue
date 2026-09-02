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
                    Konfirmasi password Anda
                </h2>
                <p class="mt-2 text-sm text-inkmuted">
                    Silakan masukkan password Anda untuk melanjutkan.
                </p>
            </div>

            <form class="mt-8 space-y-5" @submit.prevent="submit">
                <div>
                    <label for="password" class="block text-sm font-medium text-ink">
                        Password
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none transition-colors placeholder:text-inkmuted/60 focus:border-ink"
                        placeholder="Masukkan password"
                        required
                        autofocus
                    />
                    <div v-if="form.errors.password" class="mt-1.5 text-sm text-red-600">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="flex w-full justify-center rounded-full bg-ink px-4 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-moss-700 focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Konfirmasi...</span>
                        <span v-else>Konfirmasi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
