<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CreditCard, Building2, Wallet } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    invoice: Object,
    tenant: Object,
});

const form = useForm({
    payment_method: 'bank_transfer',
});

const selectedMethod = ref('bank_transfer');

const selectMethod = (method) => {
    selectedMethod.value = method;
    form.payment_method = method;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const submit = () => form.post(`/subscription/payment/${props.invoice.id}`);

const paymentMethods = [
    { id: 'bank_transfer', name: 'Bank Transfer', icon: Building2, description: 'Transfer ke rekening bank kami' },
    { id: 'credit_card', name: 'Kartu Kredit', icon: CreditCard, description: 'Visa, Mastercard, JCB' },
    { id: 'ewallet', name: 'E-Wallet', icon: Wallet, description: 'GoPay, OVO, DANA, ShopeePay' },
];
</script>

<template>
    <Head title="Pembayaran" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <Link href="/subscription" class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-lg px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Pembayaran</h1>

                <!-- Invoice Summary -->
                <div class="mt-6 rounded-xl border border-stone-200 bg-white p-6">
                    <h2 class="font-display text-lg font-bold text-ink">Ringkasan Invoice</h2>
                    <div class="mt-4 space-y-3">
                        <div class="flex justify-between border-b border-stone-100 pb-2">
                            <span class="text-sm text-inkmuted">Paket</span>
                            <span class="text-sm font-semibold text-ink">{{ invoice.package?.name }}</span>
                        </div>
                        <div class="flex justify-between border-b border-stone-100 pb-2">
                            <span class="text-sm text-inkmuted">Deskripsi</span>
                            <span class="text-sm font-semibold text-ink">{{ invoice.description }}</span>
                        </div>
                        <div class="flex justify-between pt-2">
                            <span class="text-lg font-bold text-ink">Total</span>
                            <span class="text-lg font-bold text-ink">{{ formatCurrency(invoice.amount) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mt-6 rounded-xl border border-stone-200 bg-white p-6">
                    <h2 class="font-display text-lg font-bold text-ink">Metode Pembayaran</h2>
                    <div class="mt-4 space-y-3">
                        <button
                            v-for="method in paymentMethods"
                            :key="method.id"
                            :class="[
                                'w-full rounded-xl border p-4 text-left transition-colors',
                                selectedMethod === method.id
                                    ? 'border-ink bg-stone-50'
                                    : 'border-stone-200 hover:border-stone-300',
                            ]"
                            @click="selectMethod(method.id)"
                        >
                            <div class="flex items-center gap-3">
                                <component :is="method.icon" class="h-5 w-5 text-ink" />
                                <div>
                                    <p class="text-sm font-semibold text-ink">{{ method.name }}</p>
                                    <p class="text-xs text-inkmuted">{{ method.description }}</p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Bank Transfer Details -->
                <div v-if="selectedMethod === 'bank_transfer'" class="mt-6 rounded-xl border border-stone-200 bg-white p-6">
                    <h3 class="font-display text-lg font-bold text-ink">Detail Transfer</h3>
                    <div class="mt-4 space-y-2 text-sm">
                        <p class="text-inkmuted">Bank BCA</p>
                        <p class="font-mono text-lg font-bold text-ink">1234 5678 9012</p>
                        <p class="text-inkmuted">a/n PT HRapp Indonesia</p>
                    </div>
                    <div class="mt-4 rounded-lg bg-yellow-50 p-3 text-sm text-yellow-700">
                        Transfer dengan jumlah tepat. Pembayaran akan diverifikasi dalam 1x24 jam.
                    </div>
                </div>

                <!-- Submit -->
                <form class="mt-6" @submit.prevent="submit">
                    <button
                        type="submit"
                        class="w-full rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Memproses...' : 'Bayar Sekarang' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
