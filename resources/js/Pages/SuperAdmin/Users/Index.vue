<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Eye, Trash2, ToggleLeft, ToggleRight, KeyRound } from 'lucide-vue-next';

const props = defineProps({
    users: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const role = ref(props.filters?.role || '');
const status = ref(props.filters?.status || '');

const applyFilters = () => {
    router.get('/super-admin/users', {
        search: search.value,
        role: role.value,
        status: status.value,
    }, { preserveState: true });
};

const toggleActive = (id) => {
    router.post(`/super-admin/users/${id}/toggle`);
};

const resetPassword = (id) => {
    if (confirm('Reset password user ini ke "password"?')) {
        router.post(`/super-admin/users/${id}/reset-password`);
    }
};

const deleteUser = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        router.delete(`/super-admin/users/${id}`);
    }
};

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
</script>

<template>
    <Head title="User Management" />

    <AppLayout title="User Management">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">User Management</h1>
        <p class="text-sm text-muted-app">Kelola semua user di platform</p>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-soft">
            <p class="text-sm text-muted-app">Total Users</p>
            <p class="mt-1 text-2xl font-extrabold text-ink-app">{{ stats.total }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-soft">
            <p class="text-sm text-muted-app">Active</p>
            <p class="mt-1 text-2xl font-extrabold text-green-700">{{ stats.active }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-soft">
            <p class="text-sm text-muted-app">Admin</p>
            <p class="mt-1 text-2xl font-extrabold text-ink-app">{{ stats.admin }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-soft">
            <p class="text-sm text-muted-app">Employee</p>
            <p class="mt-1 text-2xl font-extrabold text-ink-app">{{ stats.employee }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        <input
            v-model="search"
            type="text"
            placeholder="Cari nama atau email..."
            class="flex-1 min-w-[200px] rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @keyup.enter="applyFilters"
        />
        <select
            v-model="role"
            class="rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">Semua Role</option>
            <option value="super_admin">Super Admin</option>
            <option value="admin">Administrator</option>
            <option value="hr">HR Manager</option>
            <option value="employee">Karyawan</option>
        </select>
        <select
            v-model="status"
            class="rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
        </select>
    </div>

    <div class="max-h-[600px] overflow-auto rounded-2xl bg-white shadow-soft">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline-app/30 bg-cream">
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Nama</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Email</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Role</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Tenant</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Status</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Login Terakhir</th>
                    <th class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users.data" :key="user.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                    <td class="px-4 py-3 text-sm font-semibold text-ink-app">{{ user.name }}</td>
                    <td class="px-4 py-3 text-sm text-muted-app">{{ user.email }}</td>
                    <td class="px-4 py-3">
                        <span :class="['rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', roleColors[user.role]]">
                            {{ roleLabels[user.role] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ user.tenant_name }}</td>
                    <td class="px-4 py-3">
                        <span
                            :class="[
                                'rounded-full px-2.5 py-0.5 text-[11px] font-extrabold',
                                user.is_active ? 'bg-sage/50 text-green-700' : 'bg-cream text-muted-app',
                            ]"
                        >
                            {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-muted-app">{{ user.last_login_at || '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Link
                                :href="`/super-admin/users/${user.id}`"
                                class="rounded-lg p-1.5 text-muted-app hover:bg-cream hover:text-ink-app"
                            >
                                <Eye class="h-4 w-4" />
                            </Link>
                            <button
                                v-if="user.role !== 'super_admin'"
                                @click="toggleActive(user.id)"
                                class="rounded-lg p-1.5 text-muted-app hover:bg-cream"
                                :title="user.is_active ? 'Nonaktifkan' : 'Aktifkan'"
                            >
                                <ToggleRight v-if="user.is_active" class="h-4 w-4 text-green-700" />
                                <ToggleLeft v-else class="h-4 w-4" />
                            </button>
                            <button
                                v-if="user.role !== 'super_admin'"
                                @click="resetPassword(user.id)"
                                class="rounded-lg p-1.5 text-muted-app hover:bg-amber/40 hover:text-yellow-700"
                                title="Reset Password"
                            >
                                <KeyRound class="h-4 w-4" />
                            </button>
                            <button
                                v-if="user.role !== 'super_admin'"
                                @click="deleteUser(user.id)"
                                class="rounded-lg p-1.5 text-muted-app hover:bg-coral-deep/10 hover:text-coral-deep"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!users.data.length">
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-muted-app">Tidak ada data user.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div v-if="users.last_page > 1" class="flex justify-center gap-1">
        <Link
            v-for="page in users.last_page"
            :key="page"
            :href="users.path + '?page=' + page"
            :class="[
                'px-3 py-1.5 text-sm font-semibold rounded-xl',
                page === users.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    </AppLayout>
</template>
