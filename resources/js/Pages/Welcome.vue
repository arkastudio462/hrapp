<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    Apple,
    ArrowRight,
    BarChart3,
    BatteryFull,
    Bell,
    CalendarCheck,
    ChevronDown,
    Code,
    Compass,
    House,
    Instagram,
    Linkedin,
    LogIn,
    MapPin,
    Play,
    Plus,
    Rocket,
    Signal,
    Smartphone,
    Smile,
    Star,
    TrendingUp,
    Twitter,
    User,
    UserCheck,
    Video,
    Wifi,
} from 'lucide-vue-next';

defineProps({
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
});

const features = [
    {
        icon: Compass,
        title: 'Desain Intuitif',
        description: 'Antarmuka simpel dirancang khusus agar mudah digunakan oleh seluruh karyawan tanpa perlu pelatihan rumit.',
        bg: 'bg-[#f5f0fa] border-lavender/40',
        iconBg: 'text-purple-600',
        link: 'text-purple-700 hover:text-purple-900',
    },
    {
        icon: Code,
        title: 'Kode Andal & Aman',
        description: 'Arsitektur cloud berstandar ISO dengan enkripsi data sensitif karyawan dan payroll berkinerja tinggi tanpa lag.',
        bg: 'bg-[#fff7ed] border-coral/10',
        iconBg: 'text-coral',
        link: 'text-coral-deep hover:text-coral-deep',
    },
    {
        icon: Rocket,
        title: 'Fitur Tanpa Batas',
        description: 'Dari presensi GPS anti-fake location, reimbursement instan, hingga kalkulasi PPh 21 dan slip gaji digital.',
        bg: 'bg-[#f0faf6] border-sage/40',
        iconBg: 'text-emerald-600',
        link: 'text-emerald-700 hover:text-emerald-900',
    },
];

const steps = [
    { icon: LogIn, title: 'Login Cepat', desc: 'SSO Google atau email perusahaan dalam satu ketukan.', bg: 'bg-coral/10 text-coral' },
    { icon: Smile, title: 'Verifikasi Wajah', desc: 'Absensi akurat bebas titip presensi dengan teknologi liveness AI.', bg: 'bg-lavender/30 text-purple-600' },
    { icon: TrendingUp, title: 'Analisis Data', desc: 'Laporan otomatis absensi, lembur, dan izin secara live.', bg: 'bg-amber/20 text-amber-600' },
    { icon: BarChart3, title: 'Hasil Real-time', desc: 'Export payroll dan evaluasi performa hanya butuh 1 klik.', bg: 'bg-sage/40 text-emerald-600' },
];

const testimonials = [
    {
        quote: 'Proses rekap absensi 200+ staf yang dulunya butuh 3 hari sekarang selesai dalam 15 menit. Aplikasi sangat responsif dan UI bersih.',
        name: 'Bagus Wicaksono',
        role: 'Head of HR • Logistik Pintar',
        initials: 'BW',
        avatar: 'bg-coral/15 text-coral-deep',
        highlighted: false,
    },
    {
        quote: 'Sistem presensi GPS dan foto selfie HRHub sangat akurat untuk tim lapangan kami. Tidak ada lagi kecurangan lembur atau titip absen.',
        name: 'Anindya Savitri',
        role: 'Operations Director • Retail Prima',
        initials: 'AS',
        avatar: 'bg-lavender/40 text-purple-700',
        highlighted: true,
    },
    {
        quote: 'Integrasi langsung ke payroll dan kalkulasi PPh 21 sangat membantu bisnis UMKM saya yang sedang bertumbuh cepat. Sangat direkomendasikan!',
        name: 'Dimas Anggoro',
        role: 'Founder & CEO • Kreasi Studio',
        initials: 'DA',
        avatar: 'bg-sage/50 text-emerald-700',
        highlighted: false,
    },
];

const faqs = [
    {
        q: 'Apakah ada masa uji coba gratis untuk HRHub?',
        a: 'Ya, kami menyediakan uji coba gratis penuh selama 14 hari tanpa perlu memasukkan informasi kartu kredit. Anda juga akan didampingi tim onboarding untuk migrasi data.',
    },
    {
        q: 'Apakah HRHub dapat disesuaikan dengan aturan lembur & shift khusus?',
        a: 'Tentu saja. HRHub mendukung multi-shift rotasi fleksibel, split-shift, serta konfigurasi perhitungan lembur sesuai standar Depnaker atau kebijakan perusahaan Anda.',
    },
    {
        q: 'Bagaimana keamanan data pribadi karyawan kami?',
        a: 'Data Anda dienkripsi end-to-end menggunakan standar enkripsi AES-256 dan disimpan di pusat data tier-4 dengan backup harian otomatis serta sertifikasi kepatuhan privasi data.',
    },
    {
        q: 'Apakah karyawan bisa mengajukan cuti dan reimbursement lewat aplikasi?',
        a: 'Ya, karyawan dapat mengunggah struk pengeluaran atau mengajukan izin sakit langsung dari aplikasi mobile HRHub di Android atau iOS untuk segera di-approve oleh atasan.',
    },
];

const chartBars = [
    { h: 'h-8', c: 'bg-coral/30' },
    { h: 'h-12', c: 'bg-coral/60' },
    { h: 'h-10', c: 'bg-coral' },
    { h: 'h-14', c: 'bg-coral-deep' },
    { h: 'h-11', c: 'bg-amber' },
];
</script>

<template>
    <Head title="HRHub - Kelola Semua Karyawan dengan HRHub" />

    <div class="bg-cream font-sans text-ink-app antialiased">
        <!-- NAVBAR -->
        <header class="sticky top-0 z-50 w-full border-b border-outline-app/70 bg-white/80 backdrop-blur-md">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 sm:px-12">
                <a href="/" class="flex items-center gap-3">
                    <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-coral text-xl font-bold text-white shadow-md shadow-coral/20">
                        H
                        <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-blush"></span>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight text-ink-app">HRHub<span class="text-coral">.</span></span>
                </a>

                <nav class="hidden items-center space-x-8 text-sm font-medium text-muted-app md:flex">
                    <a href="#beranda" class="transition-colors hover:text-coral">Beranda</a>
                    <a href="#fitur" class="transition-colors hover:text-coral">Fitur</a>
                    <a href="#cara-kerja" class="transition-colors hover:text-coral">Cara Kerja</a>
                    <a href="#testimoni" class="transition-colors hover:text-coral">Ulasan</a>
                    <a href="#faq" class="transition-colors hover:text-coral">FAQ</a>
                </nav>

                <div class="flex items-center space-x-3">
                    <Link v-if="canLogin" href="/login" class="hidden rounded-full border border-outline-app bg-white px-5 py-2.5 text-sm font-semibold text-ink-app transition-all hover:-translate-y-0.5 hover:border-coral hover:text-coral sm:inline-block">
                        Masuk
                    </Link>
                    <Link v-if="canRegister" href="/register" class="rounded-full bg-coral px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-coral/25 transition-all hover:-translate-y-0.5 hover:bg-coral-deep">
                        Daftar
                    </Link>
                </div>
            </div>
        </header>

        <!-- HERO -->
        <section id="beranda" class="hero-bg-gradient overflow-hidden px-6 pt-8 pb-20 sm:px-12">
            <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 lg:grid-cols-12">
                <!-- Left -->
                <div class="z-10 space-y-8 text-left lg:col-span-6">
                    <h1 class="text-4xl font-extrabold leading-[1.15] tracking-tight text-ink-app sm:text-5xl lg:text-6xl">
                        Kelola <span class="text-coral">semua</span> karyawan dengan HRHub
                    </h1>
                    <p class="max-w-lg text-base leading-relaxed text-muted-app sm:text-lg">
                        Masih mencari aplikasi terbaik untuk mengelola absensi, kinerja, dan payroll tim Anda? HRHub hadir dengan sistem otomatis terintegrasi.
                    </p>

                    <!-- Store badges -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="#" class="inline-flex items-center gap-3 rounded-xl bg-black px-4 py-2.5 text-white shadow-sm transition-transform hover:-translate-y-0.5 hover:bg-ink-app">
                            <Apple class="h-6 w-6" />
                            <span class="text-left leading-tight">
                                <span class="block text-[10px] uppercase tracking-wider text-slate-300">Download on the</span>
                                <span class="text-sm font-semibold">App Store</span>
                            </span>
                        </a>
                        <a href="#" class="inline-flex items-center gap-3 rounded-xl bg-black px-4 py-2.5 text-white shadow-sm transition-transform hover:-translate-y-0.5 hover:bg-ink-app">
                            <Play class="h-5 w-5 fill-current" />
                            <span class="text-left leading-tight">
                                <span class="block text-[10px] uppercase tracking-wider text-slate-300">GET IT ON</span>
                                <span class="text-sm font-semibold">Google Play</span>
                            </span>
                        </a>
                    </div>

                    <!-- Social proof -->
                    <div class="flex items-center gap-4 pt-4">
                        <div class="flex -space-x-2.5">
                            <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border-2 border-white bg-slate-200 text-xs font-bold text-slate-600 shadow-sm"><User class="mt-2 h-5 w-5 text-slate-400" /></span>
                            <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border-2 border-white bg-coral/15 text-xs font-bold text-coral-deep shadow-sm"><User class="mt-2 h-5 w-5 text-coral" /></span>
                            <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border-2 border-white bg-amber/30 text-xs font-bold text-amber-600 shadow-sm"><User class="mt-2 h-5 w-5 text-amber" /></span>
                            <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border-2 border-white bg-lavender/40 text-xs font-bold text-purple-700 shadow-sm"><UserCheck class="mt-2 h-5 w-5 text-lavender" /></span>
                        </div>
                        <div>
                            <div class="text-lg font-extrabold text-coral">10M+</div>
                            <div class="text-xs font-medium text-muted-app">Pengguna aktif terdaftar</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Phone mockup -->
                <div class="relative flex items-center justify-center lg:col-span-6">
                    <div class="absolute -inset-4 -z-10 rounded-full bg-gradient-to-tr from-coral/20 to-blush/30 opacity-60 blur-3xl"></div>

                    <div class="phone-shadow relative z-10 w-[300px] select-none rounded-[42px] border-[8px] border-white bg-slate-50 p-3.5 sm:w-[330px]">
                        <!-- Status bar -->
                        <div class="flex items-center justify-between px-4 py-1 text-xs font-semibold text-slate-500">
                            <span>09:41</span>
                            <div class="h-4 w-16 rounded-full bg-slate-200"></div>
                            <div class="flex items-center space-x-1">
                                <Signal class="h-3 w-3" />
                                <Wifi class="h-3 w-3" />
                                <BatteryFull class="h-3.5 w-3.5" />
                            </div>
                        </div>

                        <!-- App content -->
                        <div class="mt-3 space-y-4 rounded-3xl border border-slate-100 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-muted-app">Selamat Pagi,</span>
                                    <span class="text-sm font-bold text-ink-app">Bima Pratama 👋</span>
                                </div>
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cream text-muted-app">
                                    <Bell class="h-3.5 w-3.5" />
                                </div>
                            </div>

                            <!-- Presensi card -->
                            <div class="rounded-2xl bg-gradient-to-r from-coral to-coral-deep p-3.5 text-white shadow-md shadow-coral/20">
                                <div class="mb-1 flex items-center justify-between text-xs opacity-90">
                                    <span>Presensi Hari Ini</span>
                                    <span class="rounded-full bg-white/20 px-2 py-0.5 text-[10px]">Tepat Waktu</span>
                                </div>
                                <div class="text-xl font-bold">08:29 WIB</div>
                                <div class="mt-2 flex items-center gap-1 text-[11px] text-coral/80">
                                    <MapPin class="h-3 w-3" /> Kantor Pusat Jakarta
                                </div>
                            </div>

                            <!-- Task list -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold text-ink-app">
                                    <span>Proyek Tim</span>
                                    <span class="cursor-pointer text-[11px] text-coral">Lihat Semua</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-cream p-2.5">
                                    <div class="flex items-center space-x-2">
                                        <div class="h-2.5 w-2.5 rounded-full bg-emerald-500"></div>
                                        <span class="text-xs font-medium text-ink-app">Evaluasi KPI Q3</span>
                                    </div>
                                    <span class="rounded bg-sage/50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Selesai</span>
                                </div>
                            </div>

                            <!-- Chart -->
                            <div class="rounded-2xl border border-slate-100 bg-cream p-3">
                                <div class="mb-2 text-[11px] font-bold text-ink-app">Statistik Kehadiran Mingguan</div>
                                <div class="flex h-14 items-end justify-between px-1 pt-2">
                                    <div v-for="(bar, i) in chartBars" :key="i" class="w-4 rounded-t" :class="[bar.h, bar.c]"></div>
                                </div>
                                <div class="mt-1 flex justify-between text-[9px] font-medium text-muted-app">
                                    <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span>
                                </div>
                            </div>

                            <!-- Bottom nav -->
                            <div class="flex items-center justify-around pt-1 text-xs text-muted-app">
                                <House class="h-4 w-4 text-coral" />
                                <CalendarCheck class="h-4 w-4" />
                                <div class="-mt-3 flex h-7 w-7 items-center justify-center rounded-full bg-coral text-white shadow-md shadow-coral/30">
                                    <Plus class="h-3 w-3" />
                                </div>
                                <BarChart3 class="h-4 w-4" />
                                <User class="h-4 w-4" />
                            </div>
                        </div>
                    </div>

                    <!-- Floating card 1 -->
                    <div class="floating-card-shadow absolute top-28 -left-6 z-20 flex animate-bounce items-center gap-3 rounded-2xl border border-white/60 bg-white/95 p-3.5 backdrop-blur-md sm:-left-12">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-lavender/30 text-base font-bold text-purple-600">
                            <Video class="h-4 w-4" />
                        </div>
                        <div>
                            <div class="text-xs font-bold text-ink-app">Weekly Townhall</div>
                            <div class="text-[10px] text-muted-app">10:00 - 11:00 • Ruang Zoom</div>
                        </div>
                        <button class="ml-2 rounded-full bg-coral px-2.5 py-1 text-[10px] font-medium text-white">Join</button>
                    </div>

                    <!-- Floating card 2 -->
                    <div class="floating-card-shadow absolute -right-4 bottom-32 z-20 max-w-[210px] rounded-2xl border border-white/60 bg-white/95 p-3.5 backdrop-blur-md sm:-right-8">
                        <div class="mb-1.5 flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase text-muted-app">Izin & Cuti</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        </div>
                        <p class="text-xs font-bold text-ink-app">Permohonan Disetujui</p>
                        <p class="text-[11px] text-muted-app">Cuti tahunan 2 hari (Sari Dewi) telah diverifikasi HR.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CLIENT LOGOS -->
        <section class="border-y border-outline-app/50 bg-white py-12">
            <div class="mx-auto max-w-7xl px-6 text-center">
                <p class="mb-8 text-xs font-bold uppercase tracking-widest text-muted-app">
                    Dipercaya oleh lebih dari 10.000+ Perusahaan & Startup
                </p>
                <div class="flex flex-wrap items-center justify-center gap-8 font-bold text-slate-400 transition-opacity grayscale md:gap-14">
                    <span class="flex items-center gap-1.5 text-xl font-bold tracking-tight text-slate-600">G Google</span>
                    <span class="text-2xl font-black italic tracking-wider">VISA</span>
                    <span class="flex items-center gap-1 text-xl font-bold"><Linkedin class="h-5 w-5" /> LinkedIn</span>
                    <span class="text-lg font-extrabold uppercase tracking-widest">SAMSUNG</span>
                    <span class="flex items-center gap-1.5 text-xl font-bold"><Video class="h-5 w-5" /> zoom</span>
                    <span class="flex items-center gap-1.5 text-xl font-bold">Spotify</span>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section id="fitur" class="mx-auto max-w-7xl px-6 py-20 sm:px-12">
            <div class="mx-auto mb-16 max-w-2xl text-center">
                <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-ink-app sm:text-4xl">Fitur Unggulan HRHub</h2>
                <p class="text-base text-muted-app">Semua yang Anda butuhkan untuk otomasi administrasi karyawan dalam satu platform yang mudah digunakan.</p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div v-for="feature in features" :key="feature.title" class="rounded-3xl border p-8 transition-transform duration-300 hover:-translate-y-1" :class="feature.bg">
                    <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm" :class="feature.iconBg">
                        <component :is="feature.icon" class="h-6 w-6" />
                    </div>
                    <h3 class="mb-3 text-xl font-bold text-ink-app">{{ feature.title }}</h3>
                    <p class="mb-4 text-sm leading-relaxed text-muted-app">{{ feature.description }}</p>
                    <a href="#" class="inline-flex items-center gap-1 text-xs font-bold" :class="feature.link">
                        Pelajari lebih lanjut <ArrowRight class="h-2.5 w-2.5" />
                    </a>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section id="cara-kerja" class="border-t border-outline-app/50 bg-cream/70 px-6 py-20 sm:px-12">
            <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 lg:grid-cols-12">
                <div class="grid grid-cols-2 gap-4 lg:col-span-6">
                    <div v-for="step in steps" :key="step.title" class="flex flex-col items-center rounded-2xl border border-outline-app/50 bg-white p-6 text-center shadow-sm transition hover:shadow-md">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl text-xl" :class="step.bg">
                            <component :is="step.icon" class="h-5 w-5" />
                        </div>
                        <h4 class="mb-1 text-sm font-bold text-ink-app">{{ step.title }}</h4>
                        <p class="text-xs text-muted-app">{{ step.desc }}</p>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-6">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-coral">Workflow Efisien</span>
                    <h2 class="text-3xl font-extrabold leading-tight text-ink-app sm:text-4xl">
                        Bagaimana HRHub Bekerja untuk Perusahaan Anda
                    </h2>
                    <p class="text-base leading-relaxed text-muted-app">
                        HRHub menghilangkan proses formulir manual yang memakan waktu berjam-jam. Mulai dari onboarding karyawan baru hingga penggajian akhir bulan, semuanya tersinkronisasi langsung ke smartphone Anda dan tim.
                    </p>
                    <div class="pt-2">
                        <Link v-if="canRegister" href="/register" class="inline-flex items-center gap-2 rounded-full bg-coral px-6 py-3 text-sm font-semibold text-white shadow-md shadow-coral/20 transition-transform hover:-translate-y-0.5 hover:bg-coral-deep">
                            <span>Mulai Uji Coba Gratis</span>
                            <ArrowRight class="h-3 w-3" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section id="testimoni" class="mx-auto max-w-7xl px-6 py-20 sm:px-12">
            <div class="mx-auto mb-16 max-w-xl text-center">
                <h2 class="mb-3 text-3xl font-extrabold tracking-tight text-ink-app sm:text-4xl">Apa Kata Mereka Tentang HRHub</h2>
                <p class="text-sm text-muted-app sm:text-base">Pengalaman nyata dari para HR Manager dan Pemilik Bisnis di Indonesia.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div
                    v-for="t in testimonials"
                    :key="t.name"
                    class="relative flex flex-col justify-between rounded-2xl border bg-white p-6 shadow-sm"
                    :class="t.highlighted ? 'border-2 border-coral/20 shadow-md' : 'border-outline-app/50'"
                >
                    <span v-if="t.highlighted" class="absolute -top-3 right-6 rounded-full bg-coral px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">Favorit</span>
                    <div>
                        <div class="mb-3 flex text-sm text-amber">
                            <Star v-for="n in 5" :key="n" class="h-3.5 w-3.5 fill-current" />
                        </div>
                        <p class="mb-6 text-sm font-normal leading-relaxed text-slate-700">"{{ t.quote }}"</p>
                    </div>
                    <div class="flex items-center gap-3 border-t border-outline-app/50 pt-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold" :class="t.avatar">{{ t.initials }}</div>
                        <div>
                            <div class="text-sm font-bold text-ink-app">{{ t.name }}</div>
                            <div class="text-xs text-muted-app">{{ t.role }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="border-t border-outline-app/50 bg-cream px-6 py-16 sm:px-12">
            <div class="mx-auto max-w-4xl">
                <div class="mb-12 text-center">
                    <h2 class="text-3xl font-extrabold text-ink-app">Pertanyaan yang Sering Diajukan</h2>
                    <p class="mt-2 text-sm text-muted-app">Punya pertanyaan seputar migrasi dan penggunaan HRHub? Temukan jawabannya di sini.</p>
                </div>
                <div class="space-y-4">
                    <details v-for="(faq, i) in faqs" :key="i" class="group rounded-2xl border border-outline-app/70 bg-white p-5" :open="i === 0">
                        <summary class="flex list-none cursor-pointer items-center justify-between text-base font-bold text-ink-app">
                            <span>{{ faq.q }}</span>
                            <span class="text-muted-app transition group-open:rotate-180"><ChevronDown class="h-3 w-3" /></span>
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-muted-app">{{ faq.a }}</p>
                    </details>
                </div>
            </div>
        </section>

        <!-- BOTTOM CTA -->
        <section class="mx-auto max-w-7xl px-6 py-16 sm:px-12">
            <div class="relative flex flex-col items-center justify-between gap-8 overflow-hidden rounded-3xl bg-gradient-to-r from-ink-app via-slate-800 to-coral-deep p-8 text-white shadow-xl sm:p-14 md:flex-row">
                <div class="z-10 max-w-lg space-y-4 text-left">
                    <h3 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Tingkatkan Performa Tim Anda Bersama HRHub</h3>
                    <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                        Download aplikasi HRHub sekarang di smartphone Anda dan nikmati kemudahan kelola presensi, jadwal, dan payroll dari genggaman tangan.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="#" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-xs font-bold text-ink-app transition hover:bg-slate-100">
                            <Apple class="h-4 w-4" /> App Store
                        </a>
                        <a href="#" class="inline-flex items-center gap-2 rounded-xl border border-slate-600 bg-slate-800 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-700">
                            <Play class="h-4 w-4 fill-current" /> Google Play
                        </a>
                    </div>
                </div>
                <div class="z-10 flex-shrink-0">
                    <div class="w-48 rounded-2xl border border-white/20 bg-white/10 p-4 text-center backdrop-blur-md sm:w-56">
                        <Smartphone class="mx-auto mb-2 h-10 w-10 text-coral" />
                        <div class="text-xs font-semibold text-white">HRHub Mobile v3.2</div>
                        <div class="mt-1 text-[10px] text-slate-400">Siap pakai untuk iOS & Android</div>
                    </div>
                </div>
                <div class="pointer-events-none absolute -right-20 -bottom-20 h-80 w-80 rounded-full bg-coral/20 blur-3xl"></div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="border-t border-outline-app/50 bg-white px-6 py-12 sm:px-12">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 md:flex-row">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-8 w-8 items-center justify-center rounded-lg bg-coral text-sm font-bold text-white">
                        H
                        <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-blush"></span>
                    </div>
                    <span class="text-xl font-bold text-ink-app">HRHub<span class="text-coral">.</span></span>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-6 text-xs font-medium text-muted-app">
                    <a href="#" class="transition hover:text-coral">Kebijakan Privasi</a>
                    <a href="#" class="transition hover:text-coral">Syarat & Ketentuan</a>
                    <a href="#" class="transition hover:text-coral">Bantuan & Dukungan</a>
                    <a href="#" class="transition hover:text-coral">Hubungi Kami</a>
                </div>

                <div class="flex items-center gap-4 text-sm text-muted-app">
                    <a href="#" class="transition hover:text-ink-app"><Instagram class="h-4 w-4" /></a>
                    <a href="#" class="transition hover:text-ink-app"><Linkedin class="h-4 w-4" /></a>
                    <a href="#" class="transition hover:text-ink-app"><Twitter class="h-4 w-4" /></a>
                </div>
            </div>
            <div class="mt-8 text-center text-[11px] text-muted-app">
                © 2024 HRHub Indonesia. Hak Cipta Dilindungi Undang-Undang.
            </div>
        </footer>
    </div>
</template>

<style scoped>
.hero-bg-gradient {
    background:
        radial-gradient(circle at 80% 30%, rgba(240, 112, 60, 0.12) 0%, rgba(244, 166, 183, 0.08) 40%, rgba(245, 243, 240, 0.9) 70%),
        radial-gradient(circle at 10% 20%, rgba(245, 243, 240, 0.8) 0%, rgba(255, 255, 255, 0) 50%);
}
.floating-card-shadow {
    box-shadow: 0 20px 35px -5px rgba(26, 26, 26, 0.08), 0 10px 15px -6px rgba(26, 26, 26, 0.04);
}
.phone-shadow {
    box-shadow: 0 35px 60px -15px rgba(240, 112, 60, 0.18), 0 15px 30px -10px rgba(26, 26, 26, 0.12);
}
</style>
