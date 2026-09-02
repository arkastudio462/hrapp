<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        default: '',
    },
    token: {
        type: String,
        default: '',
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

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
                    Reset password baru
                </h2>
            </div>

            <form class="mt-8 space-y-5" @submit.prevent="submit">
                <div class="space-y-4">
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
                        <label for="password" class="block text-sm font-medium text-ink">
                            Password Baru
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none transition-colors placeholder:text-inkmuted/60 focus:border-ink"
                            placeholder="Minimal 8 karakter"
                            required
                        />
                        <div v-if="form.errors.password" class="mt-1.5 text-sm text-red-600">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-ink">
                            Konfirmasi Password Baru
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none transition-colors placeholder:text-inkmuted/60 focus:border-ink"
                            placeholder="Ulangi password baru"
                            required
                        />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="flex w-full justify-center rounded-full bg-ink px-4 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-moss-700 focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Resetting...</span>
                        <span v-else>Reset Password</span>
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
