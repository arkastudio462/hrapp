<script setup>
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Plus, Calendar, CalendarCheck } from 'lucide-vue-next';
import { leaveTypeLabels, leaveStatusLabels, leaveStatusBadges } from '@/lib/constants';

const props = defineProps({
    leaves: Object,
    balances: Array,
});

const typeLabels = leaveTypeLabels;

const typeColors = {
    leave: 'bg-sage/60 text-green-700',
    sick: 'bg-amber/40 text-yellow-700',
    permission: 'bg-lavender/60 text-purple-700',
};

const statusLabels = leaveStatusLabels;

const statusColors = {
    pending: 'bg-amber/40 text-yellow-700',
    approved: 'bg-sage/60 text-green-700',
    rejected: 'bg-blush/60 text-coral-deep',
};

const getBalance = (type) => {
    const balance = props.balances.find(b => b.type === type);
    return balance ? `${balance.remaining}/${balance.total}` : '-';
};
</script>

<template>
    <Head title="Izin & Cuti Saya" />

    <EmployeeLayout title="Izin & Cuti">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-ink-app">Izin & Cuti Saya</h1>
                <p class="mt-0.5 text-sm text-muted-app">Riwayat pengajuan izin dan cuti</p>
            </div>
            <Link
                href="/my-leave/create"
                class="inline-flex items-center gap-1.5 rounded-full bg-coral px-4 py-2 text-xs font-extrabold text-white shadow-soft transition-all hover:bg-coral-deep active:scale-95"
            >
                <Plus class="size-4" />
                Ajukan
            </Link>
        </div>

        <!-- Balance -->
        <section class="mt-4 grid grid-cols-3 gap-3 animate-fade-up">
            <div class="rounded-2xl bg-white p-4 text-center shadow-soft">
                <span class="mx-auto flex size-9 items-center justify-center rounded-xl bg-sage/60 text-green-700">
                    <Calendar class="size-4" />
                </span>
                <p class="mt-2 text-[11px] font-medium text-muted-app">Cuti</p>
                <p class="text-sm font-extrabold text-ink-app">{{ getBalance('annual') }}</p>
            </div>
            <div class="rounded-2xl bg-white p-4 text-center shadow-soft">
                <span class="mx-auto flex size-9 items-center justify-center rounded-xl bg-amber/40 text-yellow-700">
                    <Calendar class="size-4" />
                </span>
                <p class="mt-2 text-[11px] font-medium text-muted-app">Sakit</p>
                <p class="text-sm font-extrabold text-ink-app">{{ getBalance('sick') }}</p>
            </div>
            <div class="rounded-2xl bg-white p-4 text-center shadow-soft">
                <span class="mx-auto flex size-9 items-center justify-center rounded-xl bg-blush/50 text-coral-deep">
                    <Calendar class="size-4" />
                </span>
                <p class="mt-2 text-[11px] font-medium text-muted-app">Melahirkan</p>
                <p class="text-sm font-extrabold text-ink-app">{{ getBalance('maternity') }}</p>
            </div>
        </section>

        <!-- Leave list -->
        <section class="mt-5 space-y-3 animate-fade-up" style="animation-delay: 80ms">
            <div v-for="leave in leaves.data" :key="leave.id" class="rounded-2xl bg-white p-4 shadow-soft">
                <div class="flex items-center justify-between">
                    <span :class="['rounded-full px-2.5 py-1 text-[10px] font-extrabold', typeColors[leave.type]]">
                        {{ typeLabels[leave.type] }}
                    </span>
                    <span :class="['rounded-full px-2.5 py-1 text-[10px] font-extrabold', statusColors[leave.status]]">
                        {{ statusLabels[leave.status] }}
                    </span>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <CalendarCheck class="size-4 text-coral" />
                    <p class="text-sm font-bold text-ink-app">{{ leave.start_date }} - {{ leave.end_date }}</p>
                </div>
                <p class="mt-1.5 text-sm text-muted-app">{{ leave.reason }}</p>
            </div>
            <div v-if="!leaves.data.length" class="rounded-2xl bg-white p-8 text-center shadow-soft">
                <p class="text-sm font-bold text-ink-app">Belum ada riwayat izin/cuti</p>
            </div>
        </section>

        <!-- Pagination -->
        <div v-if="leaves.last_page > 1" class="mt-5 flex justify-center gap-1">
            <Link
                v-for="page in leaves.last_page"
                :key="page"
                :href="leaves.path + '?page=' + page"
                :class="[
                    'px-3 py-1.5 text-sm font-bold rounded-lg',
                    page === leaves.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-white',
                ]"
            >
                {{ page }}
            </Link>
        </div>
    </EmployeeLayout>
</template>
