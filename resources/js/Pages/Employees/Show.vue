<script setup>
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
    contract: 'bg-stone-100 text-inkmuted',
    permanent: 'bg-moss-50 text-moss-700',
    probation: 'bg-yellow-50 text-yellow-700',
};
</script>

<template>
    <Head title="Detail Karyawan" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <Link href="/employees" class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="font-display text-2xl font-bold tracking-tight text-ink">{{ employee.name }}</h1>
                        <p class="mt-1 text-sm text-inkmuted">{{ employee.nik }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span :class="['inline-flex rounded-full px-3 py-1 text-sm font-semibold', statusColors[employee.status]]">
                            {{ statusLabels[employee.status] }}
                        </span>
                        <Link
                            :href="`/employees/${employee.id}/edit`"
                            class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-ink hover:bg-stone-50"
                        >
                            Edit
                        </Link>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Profile Card -->
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <div class="flex flex-col items-center text-center">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-stone-100 text-2xl font-bold text-ink">
                                {{ employee.name.charAt(0) }}
                            </div>
                            <h2 class="mt-4 font-display text-lg font-bold text-ink">{{ employee.name }}</h2>
                            <p class="text-sm text-inkmuted">{{ employee.department?.name || '-' }}</p>
                            <p class="text-sm text-inkmuted">{{ employee.position?.name || '-' }}</p>
                        </div>
                        <div class="mt-6 space-y-3">
                            <div v-if="employee.email_personal" class="flex items-center gap-2 text-sm text-ink">
                                <Mail class="h-4 w-4 text-inkmuted" />
                                {{ employee.email_personal }}
                            </div>
                            <div v-if="employee.phone" class="flex items-center gap-2 text-sm text-ink">
                                <Phone class="h-4 w-4 text-inkmuted" />
                                {{ employee.phone }}
                            </div>
                            <div v-if="employee.address" class="flex items-center gap-2 text-sm text-ink">
                                <MapPin class="h-4 w-4 text-inkmuted" />
                                {{ employee.address }}
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="rounded-xl border border-stone-200 bg-white p-6">
                            <h3 class="font-display text-lg font-bold text-ink">Data Pribadi</h3>
                            <dl class="mt-4 space-y-3">
                                <div class="flex justify-between border-b border-stone-100 py-2">
                                    <dt class="text-sm text-inkmuted">Jenis Kelamin</dt>
                                    <dd class="text-sm font-semibold text-ink">{{ employee.gender === 'male' ? 'Laki-laki' : employee.gender === 'female' ? 'Perempuan' : '-' }}</dd>
                                </div>
                                <div class="flex justify-between border-b border-stone-100 py-2">
                                    <dt class="text-sm text-inkmuted">Tanggal Lahir</dt>
                                    <dd class="text-sm font-semibold text-ink">{{ employee.birth_date || '-' }}</dd>
                                </div>
                                <div class="flex justify-between py-2">
                                    <dt class="text-sm text-inkmuted">Status Aktif</dt>
                                    <dd class="text-sm font-semibold text-ink">{{ employee.is_active ? 'Aktif' : 'Tidak Aktif' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="rounded-xl border border-stone-200 bg-white p-6">
                            <h3 class="font-display text-lg font-bold text-ink">Data Pekerjaan</h3>
                            <dl class="mt-4 space-y-3">
                                <div class="flex justify-between border-b border-stone-100 py-2">
                                    <dt class="text-sm text-inkmuted">Departemen</dt>
                                    <dd class="text-sm font-semibold text-ink">{{ employee.department?.name || '-' }}</dd>
                                </div>
                                <div class="flex justify-between border-b border-stone-100 py-2">
                                    <dt class="text-sm text-inkmuted">Jabatan</dt>
                                    <dd class="text-sm font-semibold text-ink">{{ employee.position?.name || '-' }}</dd>
                                </div>
                                <div class="flex justify-between py-2">
                                    <dt class="text-sm text-inkmuted">Tanggal Masuk</dt>
                                    <dd class="text-sm font-semibold text-ink">{{ employee.join_date }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
