<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Mail, Phone, MapPin, Calendar } from 'lucide-vue-next';

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
    permanent: 'bg-sage/50 text-green-700',
    probation: 'bg-amber/40 text-yellow-700',
};
</script>

<template>
    <Head title="Detail Karyawan" />

    <AppLayout title="Detail Karyawan">
    <Link href="/employees" class="inline-flex items-center gap-1 text-sm font-bold text-muted-app hover:text-ink-app">
        <ArrowLeft class="h-4 w-4" />
        Kembali
    </Link>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">{{ employee.name }}</h1>
                    <p class="mt-1 text-sm text-muted-app">{{ employee.nik }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', statusColors[employee.status]]">
                        {{ statusLabels[employee.status] }}
                    </span>
                    <Link
                        :href="`/employees/${employee.id}/edit`"
                        class="rounded-xl border border-outline-app/50 bg-white px-4 py-2 text-sm font-bold text-ink-app hover:bg-cream"
                    >
                        Edit
                    </Link>
                </div>
            </div>
            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Profile Card -->
                <div class="rounded-2xl bg-white p-6 shadow-soft">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-cream text-2xl font-extrabold text-ink-app">
                            {{ employee.name.charAt(0) }}
                        </div>
                        <h2 class="mt-4 text-lg font-extrabold text-ink-app">{{ employee.name }}</h2>
                        <p class="text-sm text-muted-app">{{ employee.department?.name || '-' }}</p>
                        <p class="text-sm text-muted-app">{{ employee.position?.name || '-' }}</p>
                    </div>
                    <div class="mt-6 space-y-3">
                        <div v-if="employee.email_personal" class="flex items-center gap-2 text-sm text-ink-app">
                            <Mail class="h-4 w-4 text-muted-app" />
                            {{ employee.email_personal }}
                        </div>
                        <div v-if="employee.phone" class="flex items-center gap-2 text-sm text-ink-app">
                            <Phone class="h-4 w-4 text-muted-app" />
                            {{ employee.phone }}
                        </div>
                        <div v-if="employee.address" class="flex items-center gap-2 text-sm text-ink-app">
                            <MapPin class="h-4 w-4 text-muted-app" />
                            {{ employee.address }}
                        </div>
                    </div>
                </div>
                <!-- Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-2xl bg-white p-6 shadow-soft">
                        <h3 class="text-lg font-extrabold text-ink-app">Data Pribadi</h3>
                        <dl class="mt-4 space-y-3">
                            <div class="flex justify-between border-b border-outline-app/30 py-2">
                                <dt class="text-sm text-muted-app">Jenis Kelamin</dt>
                                <dd class="text-sm font-bold text-ink-app">{{ employee.gender === 'male' ? 'Laki-laki' : employee.gender === 'female' ? 'Perempuan' : '-' }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-outline-app/30 py-2">
                                <dt class="text-sm text-muted-app">Tanggal Lahir</dt>
                                <dd class="text-sm font-bold text-ink-app">{{ employee.birth_date || '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-2">
                                <dt class="text-sm text-muted-app">Status Aktif</dt>
                                <dd class="text-sm font-bold text-ink-app">{{ employee.is_active ? 'Aktif' : 'Tidak Aktif' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-soft">
                        <h3 class="text-lg font-extrabold text-ink-app">Data Pekerjaan</h3>
                        <dl class="mt-4 space-y-3">
                            <div class="flex justify-between border-b border-outline-app/30 py-2">
                                <dt class="text-sm text-muted-app">Departemen</dt>
                                <dd class="text-sm font-bold text-ink-app">{{ employee.department?.name || '-' }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-outline-app/30 py-2">
                                <dt class="text-sm text-muted-app">Jabatan</dt>
                                <dd class="text-sm font-bold text-ink-app">{{ employee.position?.name || '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-2">
                                <dt class="text-sm text-muted-app">Tanggal Masuk</dt>
                                <dd class="text-sm font-bold text-ink-app">{{ employee.join_date }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
    </AppLayout>
</template>
