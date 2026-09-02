<script setup>
import AppLayout from '@/layouts/AppLayout.vue';

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
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Employees -->
                    <div class="overflow-hidden rounded-xl border border-border bg-card p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                                <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Total Karyawan</p>
                                <p class="text-2xl font-bold">{{ stats.totalEmployees }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Present Today -->
                    <div class="overflow-hidden rounded-xl border border-border bg-card p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500/10">
                                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Hadir Hari Ini</p>
                                <p class="text-2xl font-bold">{{ stats.presentToday }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- On Leave -->
                    <div class="overflow-hidden rounded-xl border border-border bg-card p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-500/10">
                                <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Sedang Izin</p>
                                <p class="text-2xl font-bold">{{ stats.onLeave }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approvals -->
                    <div class="overflow-hidden rounded-xl border border-border bg-card p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-500/10">
                                <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Menunggu Approval</p>
                                <p class="text-2xl font-bold">{{ stats.pendingApprovals }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <a
                        href="/absen"
                        class="flex items-center gap-4 rounded-xl border border-border bg-card p-6 shadow-sm transition-colors hover:bg-accent"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Absen Sekarang</p>
                            <p class="text-sm text-muted-foreground">Face Detection / QR Code</p>
                        </div>
                    </a>

                    <a
                        href="/my-payslip"
                        class="flex items-center gap-4 rounded-xl border border-border bg-card p-6 shadow-sm transition-colors hover:bg-accent"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Lihat Payslip</p>
                            <p class="text-sm text-muted-foreground">Slip gaji bulan ini</p>
                        </div>
                    </a>

                    <a
                        href="/my-leave"
                        class="flex items-center gap-4 rounded-xl border border-border bg-card p-6 shadow-sm transition-colors hover:bg-accent"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Ajukan Izin</p>
                            <p class="text-sm text-muted-foreground">Izin / Cuti / Sakit</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
