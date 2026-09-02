<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronDown, ArrowRight, Check, Users, Clock, CircleCheck, FileText } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';

defineProps({
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
});

const mobileOpen = ref(false);
const faqOpen = ref(null);

const toggleFaq = (index) => {
    faqOpen.value = faqOpen.value === index ? null : index;
};

const features = [
    {
        icon: Clock,
        title: 'Absensi Real-time',
        description: 'Karyawan absen lewat HP dengan verifikasi lokasi atau foto, tercatat langsung tanpa direkap manual.',
    },
    {
        icon: CircleCheck,
        title: 'Slip Gaji Otomatis',
        description: 'Perhitungan gaji, lembur, dan potongan berjalan otomatis dari data absensi setiap bulan.',
    },
    {
        icon: Users,
        title: 'Data Karyawan Terpusat',
        description: 'Kontrak, riwayat cuti, dan dokumen tersimpan di satu profil yang bisa dicari dalam hitungan detik.',
    },
    {
        icon: FileText,
        title: 'Cuti dan Izin',
        description: 'Karyawan mengajukan cuti lewat aplikasi, atasan menyetujui dari HP, sisa jatah terpotong otomatis.',
    },
];

const steps = [
    {
        number: '01',
        title: 'Impor Data Karyawan',
        description: 'Unggah data dari file Excel yang sudah ada, atau input manual satu per satu.',
    },
    {
        number: '02',
        title: 'Atur Jadwal dan Gaji',
        description: 'Tentukan jam kerja, komponen gaji, dan aturan lembur sesuai kebijakan Anda.',
    },
    {
        number: '03',
        title: 'Karyawan Mulai Absen',
        description: 'Bagikan tautan ke karyawan — absensi dan slip gaji berjalan otomatis hari itu juga.',
    },
];

const plans = [
    {
        name: 'Rintisan',
        price: 'Rp99rb',
        desc: 'Sampai 15 karyawan',
        cta: 'Mulai Coba Gratis',
        primary: false,
        features: ['Absensi & slip gaji', 'Data karyawan tak terbatas', 'Dukungan lewat email'],
    },
    {
        name: 'Berkembang',
        price: 'Rp299rb',
        desc: 'Sampai 75 karyawan',
        cta: 'Mulai Coba Gratis',
        primary: true,
        popular: true,
        popularText: 'Paling Banyak Dipakai',
        features: ['Semua di paket Rintisan', 'Pengajuan cuti & izin', 'Laporan & ekspor payroll', 'Dukungan prioritas'],
    },
    {
        name: 'Perusahaan',
        price: 'Khusus',
        desc: '75+ karyawan, multi-cabang',
        cta: 'Hubungi Kami',
        primary: false,
        features: ['Semua di paket Berkembang', 'Multi-cabang & hak akses berjenjang', 'Integrasi sistem internal'],
    },
];

const faqs = [
    {
        question: 'Apakah data absensi dan gaji karyawan saya aman?',
        answer: 'Data disimpan terenkripsi dan hanya bisa diakses oleh akun yang diberi izin di perusahaan Anda. Kami tidak membagikan data ke pihak ketiga mana pun.',
    },
    {
        question: 'Bisakah HRapp menghitung lembur dan BPJS otomatis?',
        answer: 'Bisa. Anda mengatur aturan lembur dan komponen potongan sekali di awal, lalu sistem menghitungnya otomatis setiap periode gaji berjalan.',
    },
    {
        question: 'Apakah karyawan perlu instal aplikasi terpisah?',
        answer: 'Tidak. Karyawan cukup membuka tautan absensi lewat browser HP masing-masing, tanpa perlu mengunduh aplikasi tambahan.',
    },
    {
        question: 'Bagaimana kalau saya ingin berhenti berlangganan?',
        answer: 'Anda bisa berhenti kapan saja dari halaman pengaturan akun. Data Anda tetap bisa diunduh selama 30 hari setelah langganan berakhir.',
    },
];

const employeeStats = [
    { label: 'Hadir', value: 182 },
    { label: 'Izin/Sakit', value: 9 },
    { label: 'Terlambat', value: 3 },
];

const employeeRows = [
    { initials: 'RW', name: 'Rina Wijaya', role: 'HR Manager', location: 'Jakarta', time: 'hari ini' },
    { initials: 'BS', name: 'Budi Santoso', role: 'Staf Payroll', location: 'Surabaya', time: '3 hari lalu' },
    { initials: 'DA', name: 'Dewi Anggraini', role: 'Supervisor Toko', location: 'Bandung', time: 'kemarin' },
];

const heroEmployees = [
    { initials: 'RW', name: 'Rina Wijaya', role: 'HR Manager' },
    { initials: 'BS', name: 'Budi Santoso', role: 'Staf Payroll' },
    { initials: 'DA', name: 'Dewi Anggraini', role: 'Supervisor Toko' },
];

const displayedStats = ref(employeeStats.map(() => 0));

onMounted(async () => {
    await nextTick();

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    employeeStats.forEach((stat, i) => {
                        const duration = 900;
                        const start = performance.now();
                        const step = (now) => {
                            const progress = Math.min((now - start) / duration, 1);
                            displayedStats.value[i] = Math.floor(progress * stat.value);
                            if (progress < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                    });
                    observer.disconnect();
                }
            });
        },
        { threshold: 0.4 },
    );

    const target = document.querySelector('[data-counter]');
    if (target) observer.observe(target);
});
</script>

<template>
    <Head title="HRapp — Kelola karyawan, absensi, dan payslip dalam satu tempat" />

    <div class="min-h-screen bg-white text-ink antialiased">
        <!-- NAV -->
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-[72px] max-w-6xl items-center justify-between px-6 lg:px-10">
                <a href="/" class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                        <span class="h-2 w-2 rounded-[1px] bg-white" />
                    </span>
                    <span class="font-display text-lg font-bold tracking-tight">HRapp</span>
                </a>

                <nav class="hidden items-center gap-9 text-[14.5px] text-ink/75 lg:flex">
                    <a href="#fitur" class="hover:text-ink">Fitur</a>
                    <a href="#alur" class="hover:text-ink">Layanan</a>
                    <a href="#harga" class="hover:text-ink">Harga</a>
                    <a href="#faq" class="hover:text-ink">Tentang</a>
                </nav>

                <div class="flex items-center gap-2">
                    <Link v-if="canLogin" href="/login" class="hidden sm:inline-flex">
                        <Button class="gap-2 rounded-full bg-ink pl-5 pr-2 py-2 hover:bg-moss-700">
                            Masuk
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/15">
                                <ArrowRight class="h-3.5 w-3.5" />
                            </span>
                        </Button>
                    </Link>
                    <button
                        class="flex items-center gap-1.5 p-2.5 text-[14.5px] font-semibold lg:hidden"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <span>{{ mobileOpen ? 'Tutup' : 'Menu' }}</span>
                        <svg v-if="!mobileOpen" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                        <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div v-if="mobileOpen" class="border-t border-stone-200 bg-white lg:hidden">
                <div class="flex flex-col gap-1 px-6 py-4 text-[15px]">
                    <a href="#fitur" class="py-2.5" @click="mobileOpen = false">Fitur</a>
                    <a href="#alur" class="py-2.5" @click="mobileOpen = false">Layanan</a>
                    <a href="#harga" class="py-2.5" @click="mobileOpen = false">Harga</a>
                    <a href="#faq" class="py-2.5" @click="mobileOpen = false">Tentang</a>
                    <Link v-if="canLogin" href="/login" class="mt-2 text-center font-semibold" @click="mobileOpen = false">
                        <span class="block rounded-full bg-ink px-4 py-3 text-white">Masuk</span>
                    </Link>
                </div>
            </div>
        </header>

        <!-- HERO -->
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute top-24 left-8 hidden h-16 w-16 rounded-[6px] bg-stone-200 md:block" />
            <div class="pointer-events-none absolute right-16 top-16 hidden h-24 w-24 rounded-[6px] border border-stone-200 bg-stone-100 md:block" />
            <div class="pointer-events-none absolute right-6 top-44 hidden h-10 w-10 rounded-[6px] bg-stone-200 md:block" />

            <div class="relative mx-auto max-w-4xl px-6 pb-4 pt-16 text-center lg:pt-20">
                <h1 class="font-display text-[2.5rem] font-bold leading-[1.08] tracking-tight sm:text-6xl sm:leading-[1.05]">
                    Kelola Karyawan, Absensi, dan Gaji Jadi Satu.
                </h1>
                <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-inkmuted">
                    Kami merapikan proses HR dengan menggabungkan absensi, cuti, dan payroll dalam satu sistem yang mudah dipakai tim Anda.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <Link v-if="canRegister" href="/register">
                        <Button class="gap-2 rounded-full bg-ink pl-6 pr-2.5 py-3 hover:bg-moss-700">
                            Coba Gratis
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/15">
                                <ArrowRight class="h-4 w-4" />
                            </span>
                        </Button>
                    </Link>
                    <Link href="#harga">
                        <Button variant="outline" class="gap-2 rounded-full border-stone-300 pl-6 pr-2.5 py-3 hover:bg-stone-50">
                            Hubungi Kami
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-stone-100">
                                <ArrowRight class="h-4 w-4" />
                            </span>
                        </Button>
                    </Link>
                </div>
            </div>

            <p class="vertical-text absolute right-8 top-1/2 hidden -translate-y-1/2 text-sm font-semibold tracking-wide text-inkmuted xl:block">
                Cepat Kelola Tim Anda
            </p>

            <div class="mx-auto mt-12 max-w-6xl px-6 lg:mt-16">
                <div class="relative overflow-hidden rounded-2xl border border-stone-200 bg-stone-100">
                    <div class="grid-dots absolute inset-0 opacity-60" />
                    <div class="relative p-6 sm:p-10">
                        <div class="mb-8 flex items-center justify-between">
                            <span class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 4h10M7 4a2 2 0 00-2 2v13a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2" /></svg>
                                Absensi hari ini
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-moss-50 px-3 py-1.5 text-xs font-semibold text-moss-700">
                                Sesuai jadwal
                                <Check class="h-3.5 w-3.5" />
                            </span>
                        </div>

                        <div data-counter class="mb-8 grid grid-cols-3 gap-4">
                            <div v-for="(stat, i) in employeeStats" :key="stat.label" class="rounded-xl border border-stone-200 bg-white p-5 text-center">
                                <p class="font-display text-3xl font-bold num-tick">{{ displayedStats[i] }}</p>
                                <p class="mt-1 text-xs text-inkmuted">{{ stat.label }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 rounded-xl border border-stone-200 bg-white p-5">
                            <div
                                v-for="emp in heroEmployees"
                                :key="emp.initials"
                                class="flex min-w-[220px] flex-1 items-center gap-3"
                            >
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-stone-200 font-display text-sm font-bold">
                                    {{ emp.initials }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">{{ emp.name }}</p>
                                    <p class="text-xs text-inkmuted">{{ emp.role }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- LOGOS -->
        <section class="border-b border-stone-200">
            <div class="mx-auto max-w-6xl px-6 py-10">
                <p class="mb-6 text-center text-sm text-inkmuted">Dipercaya oleh tim HR di berbagai jenis usaha</p>
                <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-4 font-display text-lg font-bold text-ink/40">
                    <span>Warung Berkah</span>
                    <span>Klinik Sentosa</span>
                    <span>Koperasi Maju Jaya</span>
                    <span>Retail Nusantara</span>
                    <span>Bengkel Prima</span>
                </div>
            </div>
        </section>

        <!-- FITUR - RAPIKAN DATA -->
        <section id="fitur" class="mx-auto max-w-6xl px-6 py-20 lg:py-28">
            <div class="grid items-start gap-14 lg:grid-cols-2">
                <div>
                    <h2 class="font-display text-4xl font-bold leading-tight tracking-tight">Rapikan Data Tim Anda dengan Cepat.</h2>
                    <p class="mt-4 text-lg leading-relaxed text-inkmuted">Sambungkan absensi, cuti, dan gaji dalam satu alur, kapan saja Anda perlu — mulai dari beberapa menit setelah setup.</p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="group overflow-hidden rounded-xl border border-stone-200">
                            <div class="flex h-32 items-center justify-center bg-stone-100">
                                <Users class="h-10 w-10 text-inkmuted" />
                            </div>
                            <div class="flex items-center justify-between p-4">
                                <span class="text-sm font-semibold">Karyawan Tetap</span>
                                <span class="flex h-8 w-8 items-center justify-center rounded-full border border-stone-300 transition-colors group-hover:bg-ink group-hover:text-white">
                                    <ArrowRight class="h-3.5 w-3.5" />
                                </span>
                            </div>
                        </div>
                        <div class="group overflow-hidden rounded-xl border border-stone-200">
                            <div class="flex h-32 items-center justify-center bg-stone-100">
                                <Clock class="h-10 w-10 text-inkmuted" />
                            </div>
                            <div class="flex items-center justify-between p-4">
                                <span class="text-sm font-semibold">Pekerja Harian</span>
                                <span class="flex h-8 w-8 items-center justify-center rounded-full border border-stone-300 transition-colors group-hover:bg-ink group-hover:text-white">
                                    <ArrowRight class="h-3.5 w-3.5" />
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="emp in employeeRows"
                        :key="emp.initials"
                        class="flex items-center gap-4 rounded-xl border border-stone-200 p-5"
                    >
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-stone-200 font-display font-bold">
                            {{ emp.initials }}
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-inkmuted">Diperbarui {{ emp.time }}</p>
                            <p class="font-semibold">{{ emp.name }} <span class="font-normal text-inkmuted">— {{ emp.role }}, {{ emp.location }}</span></p>
                        </div>
                        <a href="#" class="whitespace-nowrap text-sm font-semibold hover:underline">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- SEMUA FITUR -->
        <section class="border-y border-stone-200 bg-stone-50">
            <div class="mx-auto max-w-6xl px-6 py-20 lg:py-28">
                <div class="max-w-xl">
                    <h2 class="font-display text-4xl font-bold tracking-tight">Semua yang Dibutuhkan Tim HR.</h2>
                    <p class="mt-4 text-lg leading-relaxed text-inkmuted">Bukan sekadar pengganti spreadsheet — setiap data saling terhubung secara otomatis.</p>
                </div>

                <div class="mt-14 grid gap-5 md:grid-cols-2">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="rounded-xl border border-stone-200 bg-white p-7"
                    >
                        <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-full bg-stone-100">
                            <component :is="feature.icon" class="h-5 w-5" />
                        </div>
                        <h3 class="font-display text-xl font-bold">{{ feature.title }}</h3>
                        <p class="mt-2 leading-relaxed text-inkmuted">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ALUR -->
        <section id="alur" class="mx-auto max-w-6xl px-6 py-20 lg:py-28">
            <div class="max-w-xl">
                <h2 class="font-display text-4xl font-bold tracking-tight">Mulai dalam Tiga Langkah.</h2>
                <p class="mt-4 text-lg leading-relaxed text-inkmuted">Tidak butuh tim IT. Kebanyakan usaha selesai setup dalam satu hari kerja.</p>
            </div>

            <div class="mt-14 grid gap-8 md:grid-cols-3">
                <div
                    v-for="step in steps"
                    :key="step.number"
                    class="border-t-2 border-ink pt-6"
                >
                    <p class="font-display text-2xl font-bold text-inkmuted/40">{{ step.number }}</p>
                    <h3 class="mt-3 font-display font-bold text-lg">{{ step.title }}</h3>
                    <p class="mt-2 leading-relaxed text-inkmuted">{{ step.description }}</p>
                </div>
            </div>
        </section>

        <!-- TESTIMONI -->
        <section class="border-y border-stone-200 bg-stone-50">
            <div class="mx-auto max-w-6xl px-6 py-20 lg:py-28">
                <div class="grid items-start gap-12 lg:grid-cols-[1fr_1.4fr]">
                    <p class="text-sm font-semibold text-inkmuted">Cerita Pengguna</p>
                    <div>
                        <blockquote class="font-display text-2xl font-medium leading-snug lg:text-3xl">
                            "Dulu rekap absen dan hitung gaji 30 karyawan bisa makan waktu dua hari penuh setiap bulan. Sekarang slip gaji terbit sendiri begitu absensi bulan berjalan ditutup."
                        </blockquote>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-200 font-display font-bold">RS</div>
                            <div>
                                <p class="text-sm font-semibold">Ratna Sulistiawati</p>
                                <p class="text-sm text-inkmuted">Staf HR, Klinik Sehat Sentosa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- HARGA -->
        <section id="harga" class="mx-auto max-w-6xl px-6 py-20 lg:py-28">
            <div class="max-w-xl">
                <h2 class="font-display text-4xl font-bold tracking-tight">Harga Mengikuti Jumlah Karyawan.</h2>
                <p class="mt-4 text-lg leading-relaxed text-inkmuted">Bayar sesuai jumlah karyawan aktif. Tidak ada biaya setup, berhenti kapan saja.</p>
            </div>

            <div class="mt-14 grid gap-6 md:grid-cols-3">
                <div
                    v-for="plan in plans"
                    :key="plan.name"
                    :class="[
                        'relative rounded-2xl p-8',
                        plan.primary ? 'border-2 border-ink bg-stone-50' : 'border border-stone-200',
                    ]"
                >
                    <span
                        v-if="plan.popular"
                        class="absolute -top-3 left-8 rounded-full bg-ink px-3 py-1 text-xs font-semibold text-white"
                    >
                        {{ plan.popularText }}
                    </span>
                    <h3 class="font-display text-xl font-bold">{{ plan.name }}</h3>
                    <p class="mt-1 text-sm text-inkmuted">{{ plan.desc }}</p>
                    <p class="mt-6 font-display text-4xl font-bold">{{ plan.price }}<span v-if="plan.price !== 'Khusus'" class="font-body text-base font-normal text-inkmuted">/bulan</span></p>
                    <ul class="mt-6 space-y-3 text-sm text-inkmuted">
                        <li v-for="feature in plan.features" :key="feature">{{ feature }}</li>
                    </ul>
                    <Link href="#demo" class="mt-8 block">
                        <Button
                            :variant="plan.primary ? 'default' : 'outline'"
                            :class="[
                                'w-full rounded-full',
                                plan.primary ? 'bg-ink hover:bg-moss-700 text-white' : 'border-stone-300 hover:bg-stone-50',
                            ]"
                        >
                            {{ plan.cta }}
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="border-y border-stone-200 bg-stone-50">
            <div class="mx-auto max-w-3xl px-6 py-20 lg:py-28">
                <h2 class="font-display text-4xl font-bold tracking-tight">Pertanyaan yang Sering Ditanyakan</h2>
                <div class="mt-10 border-t border-stone-200">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="border-b border-stone-200 py-5"
                    >
                        <button
                            class="flex w-full items-center justify-between gap-4 text-left"
                            :aria-expanded="faqOpen === index"
                            @click="toggleFaq(index)"
                        >
                            <span class="font-semibold">{{ faq.question }}</span>
                            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border border-stone-300 transition-transform" :class="{ 'rotate-180': faqOpen === index }">
                                <ChevronDown class="h-4 w-4" />
                            </span>
                        </button>
                        <div v-if="faqOpen === index" class="mt-3 leading-relaxed text-inkmuted">
                            {{ faq.answer }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section id="demo" class="mx-auto max-w-6xl px-6 py-20 text-center lg:py-28">
            <h2 class="mx-auto max-w-2xl font-display text-4xl font-bold tracking-tight sm:text-5xl">
                Rapikan Absensi dan Gaji Karyawan Anda Bulan Ini Juga.
            </h2>
            <p class="mt-4 text-lg text-inkmuted">Coba gratis 14 hari, tanpa kartu kredit.</p>
            <form class="mx-auto mt-8 flex max-w-md flex-col gap-3 sm:flex-row" @submit.prevent>
                <input
                    type="email"
                    required
                    placeholder="Alamat email kerja"
                    class="flex-1 rounded-full border border-stone-300 px-5 py-3.5 outline-none placeholder:text-inkmuted/60 focus:border-ink"
                />
                <Button type="submit" class="rounded-full bg-ink px-6 py-3.5 hover:bg-moss-700">Daftar Sekarang</Button>
            </form>
        </section>

        <!-- FOOTER -->
        <footer class="border-t border-stone-200">
            <div class="mx-auto grid max-w-6xl gap-10 px-6 py-14 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                            <span class="h-2 w-2 rounded-[1px] bg-white" />
                        </span>
                        <span class="font-display text-lg font-bold">HRapp</span>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-inkmuted">Perangkat lunak HR untuk usaha kecil dan menengah di Indonesia.</p>
                </div>
                <div>
                    <p class="text-sm font-semibold">Produk</p>
                    <ul class="mt-4 space-y-3 text-sm text-inkmuted">
                        <li><a href="#fitur" class="hover:text-ink">Fitur</a></li>
                        <li><a href="#harga" class="hover:text-ink">Harga</a></li>
                        <li><a href="#alur" class="hover:text-ink">Layanan</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold">Perusahaan</p>
                    <ul class="mt-4 space-y-3 text-sm text-inkmuted">
                        <li><a href="#" class="hover:text-ink">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-ink">Kontak</a></li>
                        <li><a href="#faq" class="hover:text-ink">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold">Legal</p>
                    <ul class="mt-4 space-y-3 text-sm text-inkmuted">
                        <li><a href="#" class="hover:text-ink">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-ink">Syarat Layanan</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-stone-200">
                <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-6 py-8 sm:flex-row">
                    <p class="text-sm text-inkmuted">&copy; 2026 HRapp. Seluruh hak cipta dilindungi.</p>
                    <span class="font-display text-2xl font-extrabold tracking-tight">HRapp</span>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.font-display {
    font-family: 'Archivo', sans-serif;
}
.vertical-text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
}
.grid-dots {
    background-image: radial-gradient(circle, #D8D2C3 1.5px, transparent 1.5px);
    background-size: 20px 20px;
}
.num-tick {
    font-variant-numeric: tabular-nums;
}
</style>
