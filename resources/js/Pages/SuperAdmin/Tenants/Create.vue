<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

defineProps({
    packages: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: '',
    slug: '',
    admin_name: '',
    admin_email: '',
    package_id: '',
});

const generateSlug = () => {
    form.slug = form.name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');
};

const submit = () => {
    form.post('/super-admin/tenants');
};
</script>

<template>
    <Head title="Create Tenant" />

    <AppLayout title="Tambah Tenant">
    <Link href="/super-admin/tenants" class="inline-flex items-center gap-1 text-sm font-bold text-muted-app hover:text-ink-app"><ArrowLeft class="h-4 w-4" /> Kembali</Link>
        <div class="w-full py-4 sm:mx-auto sm:max-w-2xl sm:py-8">
            <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Create Tenant</h1>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div>
                    <label for="name" class="block text-sm font-bold text-ink-app">Company Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors placeholder:text-muted-app/60 focus:border-coral"
                        placeholder="PT Maju Jaya"
                        required
                        @input="generateSlug"
                    />
                    <div v-if="form.errors.name" class="mt-1.5 text-sm text-coral-deep">{{ form.errors.name }}</div>
                </div>
                <div>
                    <label for="slug" class="block text-sm font-bold text-ink-app">Subdomain</label>
                    <div class="mt-1.5 flex items-center rounded-xl border border-outline-app/50 bg-white focus-within:border-coral">
                        <input
                            id="slug"
                            v-model="form.slug"
                            type="text"
                            class="flex-1 bg-transparent px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none placeholder:text-muted-app/60"
                            placeholder="pt-maju-jaya"
                            required
                        />
                        <span class="pr-5 text-sm text-muted-app">.hrhub.id</span>
                    </div>
                    <div v-if="form.errors.slug" class="mt-1.5 text-sm text-coral-deep">{{ form.errors.slug }}</div>
                </div>
                <div>
                    <label for="admin_name" class="block text-sm font-bold text-ink-app">Admin Name</label>
                    <input
                        id="admin_name"
                        v-model="form.admin_name"
                        type="text"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors placeholder:text-muted-app/60 focus:border-coral"
                        placeholder="John Doe"
                        required
                    />
                    <div v-if="form.errors.admin_name" class="mt-1.5 text-sm text-coral-deep">{{ form.errors.admin_name }}</div>
                </div>
                <div>
                    <label for="admin_email" class="block text-sm font-bold text-ink-app">Admin Email</label>
                    <input
                        id="admin_email"
                        v-model="form.admin_email"
                        type="email"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors placeholder:text-muted-app/60 focus:border-coral"
                        placeholder="admin@company.com"
                        required
                    />
                    <div v-if="form.errors.admin_email" class="mt-1.5 text-sm text-coral-deep">{{ form.errors.admin_email }}</div>
                </div>
                <div>
                    <label for="package_id" class="block text-sm font-bold text-ink-app">Package</label>
                    <select
                        id="package_id"
                        v-model="form.package_id"
                        class="mt-1.5 block w-full rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
                        required
                    >
                        <option value="">Select a package</option>
                        <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
                            {{ pkg.name }} - {{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(pkg.price_monthly) }}/bulan
                        </option>
                    </select>
                    <div v-if="form.errors.package_id" class="mt-1.5 text-sm text-coral-deep">{{ form.errors.package_id }}</div>
                </div>
                <div class="flex gap-3">
                    <Link
                        href="/super-admin/tenants"
                        class="flex-1 rounded-xl border border-outline-app/50 bg-white px-4 py-3 text-center text-sm font-semibold text-ink-app transition-colors hover:bg-cream"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        class="flex-1 rounded-xl bg-coral px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-coral-deep disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>Create Tenant</span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
