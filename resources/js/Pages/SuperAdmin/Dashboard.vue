<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    Building2,
    Users,
    DollarSign,
    Clock,
    ArrowRight,
    TrendingUp,
    Wallet,
    ReceiptText,
    AlertTriangle,
    UserPlus,
} from 'lucide-vue-next';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';
import { Line, Bar, Doughnut } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
);

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
    revenueTrend: {
        type: Object,
        default: () => ({ labels: [], data: [] }),
    },
    tenantGrowth: {
        type: Object,
        default: () => ({ labels: [], data: [] }),
    },
    packageDistribution: {
        type: Array,
        default: () => [],
    },
    recentTenants: {
        type: Array,
        default: () => [],
    },
    recentInvoices: {
        type: Array,
        default: () => [],
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(value) || 0);
};

const formatShortCurrency = (value) => {
    const num = Number(value) || 0;
    if (num >= 1000000000) return `${(num / 1000000000).toFixed(1)}M`;
    if (num >= 1000000) return `${(num / 1000000).toFixed(1)}jt`;
    if (num >= 1000) return `${(num / 1000).toFixed(0)}rb`;
    return num.toString();
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('id-ID').format(Number(value) || 0);
};

const statusColors = {
    active: 'bg-sage/50 text-green-700',
    trial: 'bg-lavender text-purple-700',
    suspended: 'bg-amber/40 text-yellow-700',
    terminated: 'bg-coral-deep/10 text-coral-deep',
};

const statusLabels = {
    active: 'Aktif',
    trial: 'Trial',
    suspended: 'Suspend',
    terminated: 'Terminasi',
};

const invoiceStatusColors = {
    pending: 'bg-amber/40 text-yellow-700',
    paid: 'bg-sage/50 text-green-700',
    overdue: 'bg-coral-deep/10 text-coral-deep',
};

const invoiceStatusLabels = {
    pending: 'Menunggu',
    paid: 'Dibayar',
    overdue: 'Jatuh Tempo',
};

const doughnutPalette = ['#F0703C', '#F5F3F0', '#a3be8c', '#d08770', '#bf616a', '#b48ead'];

const revenueData = computed(() => ({
    labels: props.revenueTrend.labels || [],
    datasets: [
        {
            label: 'Revenue',
            data: props.revenueTrend.data || [],
            borderColor: '#F0703C',
            backgroundColor: (ctx) => {
                const { chart } = ctx;
                const { ctx: canvas, chartArea } = chart;
                if (!chartArea) return 'rgba(240, 112, 60, 0.1)';
                const gradient = canvas.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                gradient.addColorStop(0, 'rgba(240, 112, 60, 0.25)');
                gradient.addColorStop(1, 'rgba(240, 112, 60, 0)');
                return gradient;
            },
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointBackgroundColor: '#F0703C',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            borderWidth: 2.5,
        },
    ],
}));

const revenueOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (ctx) => ` ${formatCurrency(ctx.parsed.y)}`,
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value) => formatShortCurrency(value),
                font: { size: 10 },
            },
            grid: { color: 'rgba(148, 163, 184, 0.15)' },
        },
        x: {
            ticks: { font: { size: 10 } },
            grid: { display: false },
        },
    },
};

const tenantData = computed(() => ({
    labels: props.tenantGrowth.labels || [],
    datasets: [
        {
            label: 'Tenant Baru',
            data: props.tenantGrowth.data || [],
            backgroundColor: 'rgba(240, 112, 60, 0.85)',
            borderRadius: 6,
            maxBarThickness: 28,
        },
    ],
}));

const tenantOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (ctx) => ` ${formatNumber(ctx.parsed.y)} tenant`,
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: { precision: 0, font: { size: 10 } },
            grid: { color: 'rgba(148, 163, 184, 0.15)' },
        },
        x: {
            ticks: { font: { size: 10 } },
            grid: { display: false },
        },
    },
};

const packageData = computed(() => ({
    labels: props.packageDistribution.map((p) => p.name) || [],
    datasets: [
        {
            data: props.packageDistribution.map((p) => p.count) || [],
            backgroundColor: doughnutPalette,
            borderColor: '#fff',
            borderWidth: 2,
        },
    ],
}));

const packageOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '68%',
    plugins: {
        legend: {
            position: 'bottom',
            labels: { usePointStyle: true, padding: 14, font: { size: 11 } },
        },
        tooltip: {
            callbacks: {
                label: (ctx) => ` ${formatNumber(ctx.parsed)} tenant`,
            },
        },
    },
};
</script>

<template>
    <Head title="Super Admin Dashboard" />

    <AppLayout title="Dashboard">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Dashboard</h1>
                <p class="text-sm text-muted-app">Ringkasan performa platform HRHub</p>
            </div>
            <Link
                href="/super-admin/tenants/create"
                class="inline-flex items-center gap-2 rounded-xl bg-coral px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-coral-deep"
            >
                <UserPlus class="h-4 w-4" />
                Tambah Tenant
            </Link>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-coral/10">
                        <Building2 class="h-5 w-5 text-coral" />
                    </div>
                    <span class="text-[11px] font-extrabold text-green-700">{{ stats.activeTenants }} aktif</span>
                </div>
                <p class="mt-4 text-3xl font-extrabold text-ink-app">{{ formatNumber(stats.totalTenants) }}</p>
                <p class="text-sm text-muted-app">Total Tenants</p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sage/30">
                        <Users class="h-5 w-5 text-green-700" />
                    </div>
                </div>
                <p class="mt-4 text-3xl font-extrabold text-ink-app">{{ formatNumber(stats.totalUsers) }}</p>
                <p class="text-sm text-muted-app">Total Users</p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-lavender/50">
                        <Wallet class="h-5 w-5 text-purple-700" />
                    </div>
                    <TrendingUp class="h-4 w-4 text-green-700" />
                </div>
                <p class="mt-4 text-3xl font-extrabold text-ink-app">{{ formatShortCurrency(stats.monthlyRevenue) }}</p>
                <p class="text-sm text-muted-app">Revenue Bulan Ini</p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber/30">
                        <ReceiptText class="h-5 w-5 text-yellow-700" />
                    </div>
                    <span class="text-[11px] font-extrabold text-coral-deep">{{ stats.overdueInvoices }} overdue</span>
                </div>
                <p class="mt-4 text-3xl font-extrabold text-ink-app">{{ formatNumber(stats.pendingInvoices) }}</p>
                <p class="text-sm text-muted-app">Invoice Menunggu</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-soft lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-extrabold text-ink-app">Tren Revenue</h2>
                        <p class="text-sm text-muted-app">12 bulan terakhir</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 rounded-full bg-sage/50 px-2.5 py-1 font-extrabold text-green-700">
                            <DollarSign class="h-3.5 w-3.5" />
                            Total {{ formatCurrency(stats.totalRevenue) }}
                        </span>
                    </div>
                </div>
                <div class="mt-4 h-72">
                    <Line :data="revenueData" :options="revenueOptions" />
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <h2 class="font-extrabold text-ink-app">Distribusi Paket</h2>
                <p class="text-sm text-muted-app">Tenant per paket</p>
                <div class="mt-4 h-72">
                    <Doughnut v-if="packageDistribution.length" :data="packageData" :options="packageOptions" />
                    <div v-else class="flex h-full items-center justify-center text-sm text-muted-app">
                        Belum ada data paket.
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-soft lg:col-span-2">
                <h2 class="font-extrabold text-ink-app">Pertumbuhan Tenant</h2>
                <p class="text-sm text-muted-app">Tenant baru per bulan</p>
                <div class="mt-4 h-64">
                    <Bar :data="tenantData" :options="tenantOptions" />
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold text-ink-app">Invoice Terbaru</h2>
                    <Link
                        href="/super-admin/invoices"
                        class="inline-flex items-center gap-1 text-sm font-bold text-muted-app hover:text-ink-app"
                    >
                        Lihat semua
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
                <div class="mt-4 space-y-3">
                    <div
                        v-for="invoice in recentInvoices"
                        :key="invoice.id"
                        class="flex items-center justify-between rounded-xl border border-outline-app/30 p-3 hover:bg-cream/50"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-ink-app">{{ invoice.tenant_name }}</p>
                            <p class="truncate text-xs text-muted-app">{{ invoice.invoice_number }}</p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            <span class="text-sm font-semibold text-ink-app">{{ formatCurrency(invoice.amount) }}</span>
                            <span :class="['rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', invoiceStatusColors[invoice.status]]">
                                {{ invoiceStatusLabels[invoice.status] }}
                            </span>
                        </div>
                    </div>
                    <div v-if="!recentInvoices.length" class="py-8 text-center text-sm text-muted-app">
                        Belum ada invoice.
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-soft">
            <div class="flex items-center justify-between p-6 pb-0">
                <div>
                    <h2 class="font-extrabold text-ink-app">Tenant Terbaru</h2>
                    <p class="text-sm text-muted-app">Tenant yang baru bergabung</p>
                </div>
                <Link
                    href="/super-admin/tenants"
                    class="inline-flex items-center gap-1 text-sm font-bold text-muted-app hover:text-ink-app"
                >
                    Lihat semua
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-outline-app/30 bg-cream">
                        <tr>
                            <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Nama</th>
                            <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Paket</th>
                            <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Status</th>
                            <th class="px-6 py-3 text-left text-[11px] font-extrabold text-muted-app">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-app/30">
                        <tr v-for="tenant in recentTenants" :key="tenant.id" class="hover:bg-cream/50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-ink-app">{{ tenant.name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-app">{{ tenant.package || '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span :class="['rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', statusColors[tenant.subscription_status]]">
                                    {{ statusLabels[tenant.subscription_status] || tenant.subscription_status }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-app">{{ tenant.created_at }}</td>
                        </tr>
                        <tr v-if="!recentTenants.length">
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-muted-app">Belum ada tenant.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
