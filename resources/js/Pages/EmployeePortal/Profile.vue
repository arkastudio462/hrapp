<script setup>
import { Head, Link } from '@inertiajs/vue3';
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

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <Link href="/my-dashboard" class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                            <span class="h-2 w-2 rounded-[1px] bg-white" />
                        </span>
                        <span class="font-display text-lg font-bold tracking-tight">HRapp</span>
                    </Link>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link href="/my-dashboard" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Dashboard</Link>
                        <Link href="/my-attendance" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Absensi</Link>
                        <Link href="/my-leave" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Izin/Cuti</Link>
                        <Link href="/my-payslip" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Payslip</Link>
                        <Link href="/my-profile" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Profil</Link>
                        <Link href="/face-attendance" class="rounded-lg bg-ink px-3 py-2 text-sm font-medium text-white">Absen Wajah</Link>
                    </nav>
                </div>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Profil Saya</h1>

                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Profile Card -->
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
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
                        <div class="rounded-xl border border-stone-200 bg-white p-6">
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

                        <div class="rounded-xl border border-stone-200 bg-white p-6">
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
            </div>
        </div>
    </div>
</template>
