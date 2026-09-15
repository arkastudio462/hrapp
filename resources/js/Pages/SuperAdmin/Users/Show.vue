<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, KeyRound, ToggleLeft, ToggleRight, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    user: Object,
});

const roleLabels = {
    super_admin: 'Super Admin',
    admin: 'Administrator',
    hr: 'HR Manager',
    employee: 'Karyawan',
};

const roleColors = {
    super_admin: 'bg-coral/10 text-coral-deep',
    admin: 'bg-sage/50 text-green-700',
    hr: 'bg-amber/40 text-yellow-700',
    employee: 'bg-cream text-muted-app',
};

const toggleActive = () => {
    router.post(`/super-admin/users/${props.user.id}/toggle`);
};

const resetPassword = () => {
    if (confirm('Reset password user ini ke "password"?')) {
        router.post(`/super-admin/users/${props.user.id}/reset-password`);
    }
};

const deleteUser = () => {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        router.delete(`/super-admin/users/${props.user.id}`);
    }
};
</script>

<template>
    <Head title="Detail User" />

    <AppLayout title="Detail User">
    <Link href="/super-admin/users" class="inline-flex items-center gap-1 text-sm font-bold text-muted-app hover:text-ink-app">
        <ArrowLeft class="h-4 w-4" />
        Kembali
    </Link>

    <div class="rounded-2xl bg-white p-6 shadow-soft">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-ink-app">{{ user.name }}</h2>
                <span :class="['mt-2 inline-flex rounded-full px-3 py-1 text-sm font-extrabold', roleColors[user.role]]">
                    {{ roleLabels[user.role] }}
                </span>
            </div>
            <div v-if="user.role !== 'super_admin'" class="flex gap-2">
                <button
                    @click="toggleActive"
                    class="flex items-center gap-1 rounded-xl border border-outline-app/50 bg-white px-4 py-2 text-sm font-semibold transition-colors hover:bg-cream"
                >
                    <ToggleRight v-if="user.is_active" class="h-4 w-4 text-green-700" />
                    <ToggleLeft v-else class="h-4 w-4" />
                    {{ user.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
                <button
                    @click="resetPassword"
                    class="flex items-center gap-1 rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-amber-600"
                >
                    <KeyRound class="h-4 w-4" />
                    Reset Password
                </button>
                <button
                    @click="deleteUser"
                    class="flex items-center gap-1 rounded-xl bg-coral-deep px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-coral-deep/90"
                >
                    <Trash2 class="h-4 w-4" />
                    Hapus
                </button>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-muted-app">Email</p>
                    <p class="text-sm font-semibold text-ink-app">{{ user.email }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-app">Tenant</p>
                    <p class="text-sm font-semibold text-ink-app">{{ user.tenant_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-app">Status</p>
                    <span
                        :class="[
                            'rounded-full px-2.5 py-0.5 text-[11px] font-extrabold',
                            user.is_active ? 'bg-sage/50 text-green-700' : 'bg-cream text-muted-app',
                        ]"
                    >
                        {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-muted-app">Login Terakhir</p>
                    <p class="text-sm font-semibold text-ink-app">{{ user.last_login_at || '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-app">Dibuat Pada</p>
                    <p class="text-sm font-semibold text-ink-app">{{ user.created_at }}</p>
                </div>
            </div>
        </div>
    </div>
    </AppLayout>
</template>
