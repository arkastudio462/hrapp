<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Building2, Globe, User, Mail, Lock, CreditCard, Check } from 'lucide-vue-next';

const props = defineProps({
    packages: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    company_name: '',
    subdomain: '',
    admin_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    package_id: null,
});

const selectedPackage = computed(() => {
    return props.packages.find((p) => p.id === form.package_id);
});

const generateSubdomain = () => {
    if (form.company_name) {
        form.subdomain = form.company_name
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '')
            .substring(0, 30);
    }
};

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Daftar - HRHub" />

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
                    <h2 class="text-ink-app mt-6 text-2xl font-extrabold tracking-tight">Daftarkan Perusahaan Anda</h2>
                    <p class="text-muted-app mt-2 text-sm">Mulai kelola SDM perusahaan Anda secara gratis selama 14 hari.</p>
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div class="space-y-4">
                        <!-- Company Name -->
                        <div>
                            <label for="company_name" class="text-ink-app block text-sm font-bold">
                                <Building2 class="mr-1 inline h-4 w-4" />
                                Nama Perusahaan
                            </label>
                            <input
                                id="company_name"
                                v-model="form.company_name"
                                type="text"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                                placeholder="PT Maju Jaya"
                                required
                                autofocus
                                @blur="generateSubdomain"
                            />
                            <div v-if="form.errors.company_name" class="text-coral-deep mt-1.5 text-sm font-semibold">{{ form.errors.company_name }}</div>
                        </div>

                        <!-- Subdomain -->
                        <div>
                            <label for="subdomain" class="text-ink-app block text-sm font-bold">
                                <Globe class="mr-1 inline h-4 w-4" />
                                Subdomain
                            </label>
                            <div class="mt-1.5 flex items-center">
                                <input
                                    id="subdomain"
                                    v-model="form.subdomain"
                                    type="text"
                                    class="block w-full rounded-l-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                                    placeholder="maju-jaya"
                                    required
                                />
                                <span class="rounded-r-xl border border-l-0 border-outline-app/50 bg-gray-50 px-3 py-3 text-sm font-semibold text-muted-app">.hrhub.id</span>
                            </div>
                            <p class="text-muted-app mt-1 text-xs">Ini akan menjadi alamat login perusahaan Anda</p>
                            <div v-if="form.errors.subdomain" class="text-coral-deep mt-1.5 text-sm font-semibold">{{ form.errors.subdomain }}</div>
                        </div>

                        <!-- Admin Name -->
                        <div>
                            <label for="admin_name" class="text-ink-app block text-sm font-bold">
                                <User class="mr-1 inline h-4 w-4" />
                                Nama Admin
                            </label>
                            <input
                                id="admin_name"
                                v-model="form.admin_name"
                                type="text"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                                placeholder="Nama lengkap admin"
                                required
                            />
                            <div v-if="form.errors.admin_name" class="text-coral-deep mt-1.5 text-sm font-semibold">{{ form.errors.admin_name }}</div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="text-ink-app block text-sm font-bold">
                                <Mail class="mr-1 inline h-4 w-4" />
                                Email
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                                placeholder="email@perusahaan.com"
                                required
                            />
                            <div v-if="form.errors.email" class="text-coral-deep mt-1.5 text-sm font-semibold">{{ form.errors.email }}</div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="text-ink-app block text-sm font-bold">
                                <Lock class="mr-1 inline h-4 w-4" />
                                Password
                            </label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                                placeholder="Minimal 8 karakter"
                                required
                            />
                            <div v-if="form.errors.password" class="text-coral-deep mt-1.5 text-sm font-semibold">{{ form.errors.password }}</div>
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="text-ink-app block text-sm font-bold">
                                <Lock class="mr-1 inline h-4 w-4" />
                                Konfirmasi Password
                            </label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-sm font-semibold text-ink-app shadow-sm outline-none transition-colors placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20"
                                placeholder="Ulangi password"
                                required
                            />
                        </div>

                        <!-- Package Selection -->
                        <div>
                            <label class="text-ink-app block text-sm font-bold">
                                <CreditCard class="mr-1 inline h-4 w-4" />
                                Pilih Paket
                            </label>
                            <div class="mt-2 space-y-3">
                                <label
                                    v-for="pkg in packages"
                                    :key="pkg.id"
                                    :class="[
                                        'flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition-all',
                                        form.package_id === pkg.id
                                            ? 'border-coral bg-coral/5'
                                            : 'border-outline-app/30 hover:border-outline-app/50',
                                    ]"
                                >
                                    <input
                                        v-model="form.package_id"
                                        type="radio"
                                        :value="pkg.id"
                                        class="sr-only"
                                    />
                                    <div
                                        :class="[
                                            'flex h-5 w-5 items-center justify-center rounded-full border-2',
                                            form.package_id === pkg.id ? 'border-coral bg-coral' : 'border-outline-app/50',
                                        ]"
                                    >
                                        <Check v-if="form.package_id === pkg.id" class="h-3 w-3 text-white" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-ink-app text-sm font-extrabold">{{ pkg.name }}</div>
                                        <div class="text-muted-app text-xs">
                                            {{ pkg.max_employees }} karyawan &middot; {{ pkg.max_storage_gb }} GB storage
                                        </div>
                                    </div>
                                    <div class="text-ink-app text-sm font-extrabold">
                                        {{ pkg.price_monthly == 0 ? 'Gratis' : 'Rp ' + Number(pkg.price_monthly).toLocaleString('id-ID') + '/bln' }}
                                    </div>
                                </label>
                            </div>
                            <div v-if="form.errors.package_id" class="text-coral-deep mt-1.5 text-sm font-semibold">{{ form.errors.package_id }}</div>
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center rounded-2xl bg-coral px-4 py-3.5 text-sm font-extrabold text-white shadow-soft transition-all hover:bg-coral-deep active:scale-[0.98] disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Mendaftar...</span>
                            <span v-else>Mulai Gratis 14 Hari</span>
                        </button>
                    </div>

                    <div class="text-center text-sm text-muted-app">
                        Sudah punya akun?
                        <Link href="/login" class="font-bold text-coral hover:text-coral-deep">Masuk</Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
