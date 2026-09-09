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

    <div class="flex min-h-screen items-center justify-center bg-[#fafbff] px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-xl shadow-sky-900/5 sm:p-10">
                <div class="text-center">
                    <Link href="/" class="inline-flex items-center gap-3">
                        <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-sky-600 text-xl font-bold text-white shadow-md shadow-sky-500/20">
                            H
                            <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-sky-300"></span>
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-900">HRHub<span class="text-sky-600">.</span></span>
                    </Link>
                    <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Reset password baru</h2>
                    <p class="mt-2 text-sm text-slate-500">Buat password baru untuk akun Anda.</p>
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition-colors placeholder:text-slate-400 focus:border-sky-600 focus:ring-2 focus:ring-sky-100"
                                placeholder="email@perusahaan.com"
                                required
                                autofocus
                            />
                            <div v-if="form.errors.email" class="mt-1.5 text-sm text-red-600">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition-colors placeholder:text-slate-400 focus:border-sky-600 focus:ring-2 focus:ring-sky-100"
                                placeholder="Minimal 8 karakter"
                                required
                            />
                            <div v-if="form.errors.password" class="mt-1.5 text-sm text-red-600">{{ form.errors.password }}</div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition-colors placeholder:text-slate-400 focus:border-sky-600 focus:ring-2 focus:ring-sky-100"
                                placeholder="Ulangi password baru"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center rounded-full bg-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-sky-500/25 transition-all hover:-translate-y-0.5 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Resetting...</span>
                            <span v-else>Reset Password</span>
                        </button>
                    </div>

                    <div class="text-center text-sm text-slate-500">
                        <Link href="/login" class="font-semibold text-sky-600 hover:text-sky-700">Kembali ke login</Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
