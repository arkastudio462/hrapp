<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Eye } from 'lucide-vue-next';

const props = defineProps({
    invoices: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const applyFilters = () => {
    router.get('/super-admin/invoices', {
        search: search.value,
        status: status.value,
    }, { preserveState: true });
};

const statusLabels = {
    pending: 'Menunggu',
    paid: 'Dibayar',
    overdue: 'Jatuh Tempo',
};

const statusColors = {
    pending: 'bg-amber/40 text-yellow-700',
    paid: 'bg-sage/50 text-green-700',
    overdue: 'bg-coral-deep/10 text-coral-deep',
};
</script>

<template>
    <Head title="Invoice" />

    <AppLayout title="Invoice Management">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Invoice Management</h1>
        <p class="text-sm text-muted-app">Kelola semua invoice di platform</p>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
        <div class="rounded-2xl bg-white p-5 shadow-soft">
            <p class="text-sm text-muted-app">Total</p>
            <p class="mt-1 text-2xl font-extrabold text-ink-app">{{ stats.total }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-soft">
            <p class="text-sm text-muted-app">Menunggu</p>
            <p class="mt-1 text-2xl font-extrabold text-yellow-700">{{ stats.pending }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-soft">
            <p class="text-sm text-muted-app">Dibayar</p>
            <p class="mt-1 text-2xl font-extrabold text-green-700">{{ stats.paid }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-soft">
            <p class="text-sm text-muted-app">Jatuh Tempo</p>
            <p class="mt-1 text-2xl font-extrabold text-coral-deep">{{ stats.overdue }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-soft">
            <p class="text-sm text-muted-app">Total Revenue</p>
            <p class="mt-1 text-lg font-extrabold text-green-700">{{ formatCurrency(stats.total_revenue) }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        <input
            v-model="search"
            type="text"
            placeholder="Cari nomor invoice atau nama tenant..."
            class="flex-1 min-w-[200px] rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @keyup.enter="applyFilters"
        />
        <select
            v-model="status"
            class="rounded-xl border border-outline-app/50 bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none focus:border-coral"
            @change="applyFilters"
        >
            <option value="">Semua Status</option>
            <option value="pending">Menunggu</option>
            <option value="paid">Dibayar</option>
            <option value="overdue">Jatuh Tempo</option>
        </select>
    </div>

    <div class="max-h-[600px] overflow-auto rounded-2xl bg-white shadow-soft">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline-app/30 bg-cream">
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Invoice #</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Tenant</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Paket</th>
                    <th class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Jumlah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Status</th>
                    <th class="px-4 py-3 text-left text-[11px] font-extrabold text-muted-app">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-right text-[11px] font-extrabold text-muted-app">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b border-outline-app/30 last:border-0 hover:bg-cream/50">
                    <td class="px-4 py-3 text-sm font-mono text-muted-app">{{ invoice.invoice_number }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-ink-app">{{ invoice.tenant_name }}</td>
                    <td class="px-4 py-3 text-sm text-ink-app">{{ invoice.package_name }}</td>
                    <td class="px-4 py-3 text-right text-sm font-semibold text-ink-app">{{ formatCurrency(invoice.amount) }}</td>
                    <td class="px-4 py-3">
                        <span :class="['rounded-full px-2.5 py-0.5 text-[11px] font-extrabold', statusColors[invoice.status]]">
                            {{ statusLabels[invoice.status] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-muted-app">{{ invoice.due_date }}</td>
                    <td class="px-4 py-3 text-right">
                        <Link
                            :href="`/super-admin/invoices/${invoice.id}`"
                            class="rounded-lg p-1.5 text-muted-app hover:bg-cream hover:text-ink-app"
                        >
                            <Eye class="h-4 w-4" />
                        </Link>
                    </td>
                </tr>
                <tr v-if="!invoices.data.length">
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-muted-app">Tidak ada data invoice.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div v-if="invoices.last_page > 1" class="flex justify-center gap-1">
        <Link
            v-for="page in invoices.last_page"
            :key="page"
            :href="invoices.path + '?page=' + page"
            :class="[
                'px-3 py-1.5 text-sm font-semibold rounded-xl',
                page === invoices.current_page ? 'bg-coral text-white' : 'text-muted-app hover:bg-cream',
            ]"
        >
            {{ page }}
        </Link>
    </div>
    </AppLayout>
</template>
