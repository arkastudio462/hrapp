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
                    <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Konfirmasi password Anda</h2>
                    <p class="mt-2 text-sm text-slate-500">Silakan masukkan password Anda untuk melanjutkan.</p>
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition-colors placeholder:text-slate-400 focus:border-sky-600 focus:ring-2 focus:ring-sky-100"
                            placeholder="Masukkan password"
                            required
                            autofocus
                        />
                        <div v-if="form.errors.password" class="mt-1.5 text-sm text-red-600">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center rounded-full bg-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-sky-500/25 transition-all hover:-translate-y-0.5 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 disabled:opacity-50"
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
