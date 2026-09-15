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

    <div class="flex min-h-screen items-center justify-center bg-cream px-4 py-12">
        <div class="w-full max-w-md">
            <div class="rounded-3xl bg-white p-8 shadow-card sm:p-10">
                <div class="text-center">
                    <Link href="/" class="inline-flex items-center gap-3">
                        <div class="flex size-11 items-center justify-center rounded-2xl bg-coral text-xl font-extrabold text-white shadow-card shadow-coral/20">
                            H
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-ink-app">HRHub<span class="text-coral">.</span></span>
                    </Link>
                    <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-ink-app">Masuk ke akun Anda</h2>
                    <p class="mt-2 text-sm text-muted-app">Kelola absensi, payroll, dan karyawan Anda.</p>
                </div>

                <div v-if="status" class="mt-6 rounded-xl bg-sage/20 p-4 text-sm font-semibold text-green-700">
                    {{ status }}
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-bold text-ink-app">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                                placeholder="email@perusahaan.com"
                                required
                                autofocus
                            />
                            <div v-if="form.errors.email" class="mt-1.5 text-sm font-semibold text-coral-deep">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-ink-app">Password</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                                placeholder="Masukkan password"
                                required
                            />
                            <div v-if="form.errors.password" class="mt-1.5 text-sm font-semibold text-coral-deep">{{ form.errors.password }}</div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <input
                                    id="remember"
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="size-4 rounded border-outline-app text-coral focus:ring-coral"
                                />
                                <label for="remember" class="text-sm font-semibold text-ink-app">Ingat saya</label>
                            </div>

                            <div v-if="canResetPassword" class="text-sm">
                                <Link href="/forgot-password" class="font-bold text-coral hover:text-coral-deep">Lupa password?</Link>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center rounded-2xl bg-coral px-4 py-3.5 text-sm font-extrabold text-white shadow-soft transition-all hover:bg-coral-deep active:scale-[0.98] disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Masuk...</span>
                            <span v-else>Masuk</span>
                        </button>
                    </div>

                    <div class="text-center text-sm text-muted-app">
                        Belum punya akun?
                        <Link href="/register" class="font-bold text-coral hover:text-coral-deep">Daftar sekarang</Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
