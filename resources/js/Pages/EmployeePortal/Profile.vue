<script setup>
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
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
    contract: 'bg-cream text-muted-app',
    permanent: 'bg-sage/60 text-green-700',
    probation: 'bg-amber/40 text-yellow-700',
};
</script>

<template>
    <Head title="Profil Saya" />

    <EmployeeLayout title="Profil">
        <h1 class="text-xl font-extrabold tracking-tight text-ink-app">Profil Saya</h1>
        <p class="mt-0.5 text-sm text-muted-app">Informasi pribadi dan pekerjaan kamu</p>

        <!-- Profile header -->
        <section class="mt-4 rounded-2xl bg-white p-6 text-center shadow-soft animate-fade-up">
            <div class="mx-auto flex size-24 items-center justify-center rounded-full bg-lavender text-3xl font-extrabold text-ink-app">
                {{ employee?.name?.charAt(0) || 'U' }}
            </div>
            <h2 class="mt-3 text-lg font-extrabold text-ink-app">{{ employee?.name }}</h2>
            <p class="text-sm text-muted-app">{{ employee?.nik }}</p>
            <span :class="['mt-2 inline-flex rounded-full px-3 py-1 text-xs font-extrabold', statusColors[employee?.status]]">
                {{ statusLabels[employee?.status] }}
            </span>
        </section>

        <!-- Personal data -->
        <section class="mt-4 rounded-2xl bg-white p-5 shadow-soft animate-fade-up" style="animation-delay: 60ms">
            <h3 class="text-base font-extrabold text-ink-app">Data Pribadi</h3>
            <div class="mt-3 space-y-3">
                <div class="flex items-center gap-3 border-b border-outline-app/50 pb-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-lavender/60 text-purple-700">
                        <Mail class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Email</p>
                        <p class="text-sm font-bold text-ink-app">{{ employee?.email_personal || '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 border-b border-outline-app/50 pb-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-sage/60 text-green-700">
                        <Phone class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Telepon</p>
                        <p class="text-sm font-bold text-ink-app">{{ employee?.phone || '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 border-b border-outline-app/50 pb-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-blush/50 text-coral-deep">
                        <MapPin class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Alamat</p>
                        <p class="text-sm font-bold text-ink-app">{{ employee?.address || '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-amber/40 text-yellow-700">
                        <Calendar class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Tanggal Lahir</p>
                        <p class="text-sm font-bold text-ink-app">{{ employee?.birth_date || '-' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Work data -->
        <section class="mt-4 rounded-2xl bg-white p-5 shadow-soft animate-fade-up" style="animation-delay: 120ms">
            <h3 class="text-base font-extrabold text-ink-app">Data Pekerjaan</h3>
            <div class="mt-3 space-y-3">
                <div class="flex items-center gap-3 border-b border-outline-app/50 pb-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-lavender/60 text-purple-700">
                        <Building2 class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Departemen</p>
                        <p class="text-sm font-bold text-ink-app">{{ employee?.department?.name || '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 border-b border-outline-app/50 pb-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-sage/60 text-green-700">
                        <Briefcase class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Jabatan</p>
                        <p class="text-sm font-bold text-ink-app">{{ employee?.position?.name || '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-amber/40 text-yellow-700">
                        <Calendar class="size-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-medium text-muted-app">Tanggal Masuk</p>
                        <p class="text-sm font-bold text-ink-app">{{ employee?.join_date || '-' }}</p>
                    </div>
                </div>
            </div>
        </section>
    </EmployeeLayout>
</template>
