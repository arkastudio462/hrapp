<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: '',
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="flex min-h-screen items-center justify-center bg-background px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div>
                <h1 class="text-center text-3xl font-bold text-primary">HRHub</h1>
                <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-foreground">
                    Masuk ke akun Anda
                </h2>
            </div>

            <div v-if="status" class="rounded-md bg-green-50 p-4">
                <div class="text-sm text-green-700">{{ status }}</div>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="space-y-4 rounded-md shadow-sm">
                    <div>
                        <label for="email" class="block text-sm font-medium text-foreground">
                            Email
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full rounded-md border border-border bg-card px-3 py-2 text-foreground shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            required
                            autofocus
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-destructive">
                            {{ form.errors.email }}
                        </div>
                    </div>

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
                        />
                        <div v-if="form.errors.password" class="mt-1 text-sm text-destructive">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember"
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-border text-primary focus:ring-primary"
                            />
                            <label for="remember" class="ml-2 block text-sm text-foreground">
                                Ingat saya
                            </label>
                        </div>

                        <div v-if="canResetPassword" class="text-sm">
                            <Link
                                href="/forgot-password"
                                class="font-medium text-primary hover:text-primary/80"
                            >
                                Lupa password?
                            </Link>
                        </div>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="group relative flex w-full justify-center rounded-md bg-primary px-4 py-3 text-sm font-semibold text-primary-foreground hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Masuk...</span>
                        <span v-else>Masuk</span>
                    </button>
                </div>

                <div class="text-center text-sm text-muted-foreground">
                    Belum punya akun?
                    <Link
                        href="/register"
                        class="font-medium text-primary hover:text-primary/80"
                    >
                        Daftar sekarang
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
