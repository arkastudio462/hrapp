<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, CreditCard, Building2, Wallet } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const props = defineProps({
    invoice: Object,
    tenant: Object,
    snap_token: String,
});

const selectedMethod = ref('bank_transfer');
const isProcessing = ref(false);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const paymentMethods = [
    { id: 'bank_transfer', name: 'Bank Transfer', icon: Building2, description: 'BCA, Mandiri, BNI, BRI' },
    { id: 'credit_card', name: 'Kartu Kredit', icon: CreditCard, description: 'Visa, Mastercard, JCB' },
    { id: 'ewallet', name: 'E-Wallet', icon: Wallet, description: 'GoPay, OVO, DANA, ShopeePay' },
];

onMounted(() => {
    if (window.snap && props.snap_token) {
        window.snap.embed(props.snap_token, {
            embedId: 'snap-container',
            onSuccess: (result) => {
                handlePaymentSuccess(result);
            },
            onPending: (result) => {
                handlePaymentPending(result);
            },
            onError: (result) => {
                handlePaymentError(result);
            },
        });
    }
});

const handlePaymentSuccess = (result) => {
    router.post(`/subscription/payment/${props.invoice.id}`, {
        payment_method: result.payment_type,
    });
};

const handlePaymentPending = (result) => {
    isProcessing.value = false;
};

const handlePaymentError = (result) => {
    isProcessing.value = false;
    alert('Pembayaran gagal. Silakan coba lagi.');
};
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
                            <span class="text-sm text-inkmuted">Invoice</span>
                            <span class="text-sm font-semibold text-ink">{{ invoice.invoice_number }}</span>
                        </div>
                        <div class="flex justify-between border-b border-stone-100 pb-2">
                            <span class="text-sm text-inkmuted">Paket</span>
                            <span class="text-sm font-semibold text-ink">{{ invoice.package?.name }}</span>
                        </div>
                        <div class="flex justify-between pt-2">
                            <span class="text-lg font-bold text-ink">Total</span>
                            <span class="text-lg font-bold text-ink">{{ formatCurrency(invoice.amount) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Midtrans Snap Container -->
                <div class="mt-6 rounded-xl border border-stone-200 bg-white p-6">
                    <h2 class="font-display text-lg font-bold text-ink">Pilih Metode Pembayaran</h2>
                    <p class="mt-1 text-sm text-inkmuted">Pilih metode pembayaran favorit Anda.</p>

                    <div id="snap-container" class="mt-4" />

                    <div v-if="!snap_token" class="mt-4 rounded-lg bg-yellow-50 p-4 text-sm text-yellow-700">
                        Midtrans Snap belum dikonfigurasi. Silakan tambahkan MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di .env
                    </div>
                </div>

                <!-- Info -->
                <div class="mt-6 rounded-xl border border-stone-200 bg-white p-4">
                    <p class="text-sm text-inkmuted">
                        Pembayaran diproses oleh Midtrans. Setelah pembayaran berhasil, subscription akan otomatis diaktifkan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
