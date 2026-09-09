<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Mail, Phone, MapPin, Calendar, Building2, Briefcase } from 'lucide-vue-next';

const props = defineProps({
    employee: Object,
});

const statusLabels = {
    contract: 'Kontrak',
    permanent: 'Permanent',
    probation: 'Percobaan',
};

const statusColors = {
    contract: 'bg-stone-100 text-inkmuted',
    permanent: 'bg-moss-50 text-moss-700',
    probation: 'bg-yellow-50 text-yellow-700',
};
</script>

<template>
    <Head title="Profil Saya" />

    <AppLayout title="Profil Saya">
    <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Profil Saya</h1>
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Profile Card -->
        <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-6">
            <div class="flex flex-col items-center text-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-stone-100 text-3xl font-bold text-ink">
                    {{ employee?.name?.charAt(0) || 'U' }}
                </div>
                <h2 class="mt-4 font-display text-xl font-bold text-ink">{{ employee?.name }}</h2>
                <p class="text-sm text-inkmuted">{{ employee?.nik }}</p>
                <span :class="['mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold', statusColors[employee?.status]]">
                    {{ statusLabels[employee?.status] }}
                </span>
            </div>
        </div>
        <!-- Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-6">
                <h3 class="font-display text-lg font-bold text-ink">Data Pribadi</h3>
                <dl class="mt-4 space-y-3">
                    <div class="flex items-center gap-3 border-b border-stone-100 py-2">
                        <Mail class="h-4 w-4 text-inkmuted" />
                        <div>
                            <p class="text-xs text-inkmuted">Email</p>
                            <p class="text-sm font-semibold text-ink">{{ employee?.email_personal || '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 border-b border-stone-100 py-2">
                        <Phone class="h-4 w-4 text-inkmuted" />
                        <div>
                            <p class="text-xs text-inkmuted">Telepon</p>
                            <p class="text-sm font-semibold text-ink">{{ employee?.phone || '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 border-b border-stone-100 py-2">
                        <MapPin class="h-4 w-4 text-inkmuted" />
                        <div>
                            <p class="text-xs text-inkmuted">Alamat</p>
                            <p class="text-sm font-semibold text-ink">{{ employee?.address || '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 py-2">
                        <Calendar class="h-4 w-4 text-inkmuted" />
                        <div>
                            <p class="text-xs text-inkmuted">Tanggal Lahir</p>
                            <p class="text-sm font-semibold text-ink">{{ employee?.birth_date || '-' }}</p>
                        </div>
                    </div>
                </dl>
            </div>
            <div class="rounded-2xl border border-slate-100 shadow-sm bg-white p-6">
                <h3 class="font-display text-lg font-bold text-ink">Data Pekerjaan</h3>
                <dl class="mt-4 space-y-3">
                    <div class="flex items-center gap-3 border-b border-stone-100 py-2">
                        <Building2 class="h-4 w-4 text-inkmuted" />
                        <div>
                            <p class="text-xs text-inkmuted">Departemen</p>
                            <p class="text-sm font-semibold text-ink">{{ employee?.department?.name || '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 border-b border-stone-100 py-2">
                        <Briefcase class="h-4 w-4 text-inkmuted" />
                        <div>
                            <p class="text-xs text-inkmuted">Jabatan</p>
                            <p class="text-sm font-semibold text-ink">{{ employee?.position?.name || '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 py-2">
                        <Calendar class="h-4 w-4 text-inkmuted" />
                        <div>
                            <p class="text-xs text-inkmuted">Tanggal Masuk</p>
                            <p class="text-sm font-semibold text-ink">{{ employee?.join_date }}</p>
                        </div>
                    </div>
                </dl>
            </div>
        </div>
    </div>
    </AppLayout>
</template>
