<script setup>
import { Head, useForm } from '@inertiajs/vue3';

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

    <div class="flex min-h-screen items-center justify-center bg-background px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div>
                <h1 class="text-center text-3xl font-bold text-primary">HRHub</h1>
                <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-foreground">
                    Konfirmasi password Anda
                </h2>
                <p class="mt-2 text-center text-sm text-muted-foreground">
                    Silakan masukkan password Anda untuk melanjutkan.
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="space-y-4 rounded-md shadow-sm">
                    <div>
                        <label for="password" class="block text-sm font-medium text-foreground">
                            Password
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full rounded-md border border-border bg-card px-3 py-2 text-foreground shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            required
                            autofocus
                        />
                        <div v-if="form.errors.password" class="mt-1 text-sm text-destructive">
                            {{ form.errors.password }}
                        </div>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="group relative flex w-full justify-center rounded-md bg-primary px-4 py-3 text-sm font-semibold text-primary-foreground hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
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
