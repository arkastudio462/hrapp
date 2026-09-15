<script setup>
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CalendarCheck, Clock, LogIn } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    attendances: Object,
});

const statusLabels = {
    present: 'Hadir',
    late: 'Terlambat',
    absent: 'Alpa',
};

const statusColors = {
    present: 'bg-sage/60 text-green-700',
    late: 'bg-amber/40 text-yellow-700',
    absent: 'bg-blush/60 text-coral-deep',
};

const totalDays = computed(() => props.attendances.total ?? props.attendances.data?.length ?? 0);
const lateCount = computed(() => props.attendances.data?.filter((a) => a.status === 'late').length ?? 0);
</script>

<template>
    <Head title="Riwayat Absensi" />

    <EmployeeLayout title="Absensi">
        <h1 class="text-xl font-extrabold tracking-tight text-ink-app">Riwayat Absensi</h1>
        <p class="mt-0.5 text-sm text-muted-app">Catatan kehadiran dan jam kerja kamu</p>

        <!-- Summary -->
        <section class="mt-4 grid grid-cols-2 gap-3 animate-fade-up">
            <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-soft">
                <div class="flex size-11 items-center justify-center rounded-xl bg-lavender/60 text-purple-700">
                    <CalendarCheck class="size-5" />
                </div>
                <div>
                    <p class="text-[11px] font-medium text-muted-app">Total Kehadiran</p>
                    <p class="text-lg font-extrabold text-ink-app">{{ totalDays }} Hari</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-soft">
                <div class="flex size-11 items-center justify-center rounded-xl bg-amber/40 text-yellow-700">
                    <Clock class="size-5" />
                </div>
                <div>
                    <p class="text-[11px] font-medium text-muted-app">Terlambat</p>
                    <p class="text-lg font-extrabold text-ink-app">{{ lateCount }} Kali</p>
                </div>
            </div>
        </section>

        <!-- Attendance list -->
        <section class="mt-5 animate-fade-up" style="animation-delay: 80ms">
            <div class="space-y-3">
                <div v-for="att in attendances.data" :key="att.id" class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-soft">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-cream text-coral">
                        <LogIn class="size-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-ink-app">{{ att.date }}</p>
                        <p class="mt-0.5 text-xs text-muted-app">
                            Masuk {{ att.check_in_time || '-' }} · Pulang {{ att.check_out_time || '-' }}
                        </p>
                    </div>
                    <span :class="['shrink-0 rounded-full px-2.5 py-1 text-[10px] font-extrabold', statusColors[att.status]]">
                        {{ statusLabels[att.status] }}
                    </span>
                </div>
                <div v-if="!attendances.data.length" class="rounded-2xl bg-white p-8 text-center shadow-soft">
                    <p class="text-sm font-bold text-ink-app">Belum ada riwayat absensi</p>
                </div>
            </div>
        </section>

        <!-- Pagination -->
        <div v-if="attendances.last_page > 1" class="mt-5 flex justify-center gap-1">
            <Link
                v-for="page in attendances.last_page"
                :key="page"
                :href="attendances.path + '?page=' + page"
                :class="[
                    'px-3 py-1.5 text-sm font-bold rounded-lg',
                    page === attendances.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-white',
                ]"
            >
                {{ page }}
            </Link>
        </div>
    </EmployeeLayout>
</template>
