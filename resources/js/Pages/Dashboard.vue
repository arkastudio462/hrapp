<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Users, CircleCheck, CalendarClock, Clock, Video, FileText, CalendarCheck } from 'lucide-vue-next';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalEmployees: 0,
            presentToday: 0,
            onLeave: 0,
            pendingApprovals: 0,
        }),
    },
});

const statCards = [
    { key: 'totalEmployees', label: 'Total Karyawan', icon: Users, color: 'bg-stone-100 text-ink' },
    { key: 'presentToday', label: 'Hadir Hari Ini', icon: CircleCheck, color: 'bg-moss-50 text-moss-700' },
    { key: 'onLeave', label: 'Sedang Izin', icon: CalendarClock, color: 'bg-stone-100 text-ink' },
    { key: 'pendingApprovals', label: 'Menunggu Approval', icon: Clock, color: 'bg-stone-100 text-ink' },
];

const quickActions = [
    { href: '/absen', label: 'Absen Sekarang', description: 'Face Detection / QR Code', icon: Video },
    { href: '/my-payslip', label: 'Lihat Payslip', description: 'Slip gaji bulan ini', icon: FileText },
    { href: '/my-leave', label: 'Ajukan Izin', description: 'Izin / Cuti / Sakit', icon: CalendarCheck },
];
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Stats -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="stat in statCards"
                        :key="stat.key"
                        class="rounded-xl border border-stone-200 bg-white p-5"
                    >
                        <div class="flex items-center gap-4">
                            <div :class="['flex h-11 w-11 items-center justify-center rounded-full', stat.color]">
                                <component :is="stat.icon" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-sm text-inkmuted">{{ stat.label }}</p>
                                <p class="font-display text-2xl font-bold">{{ stats[stat.key] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <a
                        v-for="action in quickActions"
                        :key="action.href"
                        :href="action.href"
                        class="flex items-center gap-4 rounded-xl border border-stone-200 bg-white p-5 transition-colors hover:bg-stone-50"
                    >
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-100">
                            <component :is="action.icon" class="h-5 w-5 text-ink" />
                        </div>
                        <div>
                            <p class="font-semibold text-ink">{{ action.label }}</p>
                            <p class="text-sm text-inkmuted">{{ action.description }}</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
