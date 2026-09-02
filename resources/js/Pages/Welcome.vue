<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Check, ScanFace, QrCode, Wallet, CalendarCheck, Users, Building2 } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Badge from '@/components/ui/badge/Badge.vue';

defineProps({
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
});

const features = [
    {
        icon: ScanFace,
        title: 'Face Detection + GPS Geofence',
        description: 'Karyawan selfie, AI verifikasi wajah, cek lokasi GPS dalam radius kantor, absensi tercatat otomatis. Data tersimpan aman: foto, koordinat, timestamp, IP address.',
        accent: true,
    },
    {
        icon: QrCode,
        title: 'QR Code Absensi',
        description: 'QR unik harian tampil di layar kantor. Scan dari HP — selesai. Auto-expire 5 menit, satu QR satu kali pakai.',
    },
    {
        icon: Wallet,
        title: 'Payroll Otomatis',
        description: 'Hitung gaji, THR, lembur, BPJS & PPh21 sekaligus. Generate payslip PDF. Ekspor ke Excel.',
    },
    {
        icon: CalendarCheck,
        title: 'Izin & Cuti',
        description: 'Submit, approve, selesai. Saldo cuti otomatis. Notifikasi email dan in-app real-time.',
    },
    {
        icon: Users,
        title: 'Manajemen Karyawan',
        description: 'Data NIK, foto, departemen, jabatan. Import bulk Excel. Struktur organisasi visual.',
    },
    {
        icon: Building2,
        title: 'Multi-Tenant SaaS',
        description: 'Subdomain perusahaan (*.hrhub.id). Data terisolasi. Wildcard SSL. Zero downtime deploy.',
    },
];

const steps = [
    { number: '1', title: 'Daftar & pilih paket', description: 'Masukkan nama perusahaan, email admin, pilih paket. Gratis 14 hari tanpa kartu kredit.' },
    { number: '2', title: 'Subdomain aktif otomatis', description: 'Perusahaan dapat subdomain sendiri. Tidak perlu setup DNS manual.' },
    { number: '3', title: 'Setup wizard & mulai', description: 'Atur jam kerja, geofence, import karyawan. Selesai — langsung bisa absen dan gajian.' },
];

const plans = [
    {
        name: 'Starter',
        price: '199rb',
        desc: 'Hingga 25 karyawan',
        cta: 'Mulai Sekarang',
        primary: false,
        features: ['Absensi QR Code', 'Payroll dasar', 'Izin & cuti', '5 GB storage'],
    },
    {
        name: 'Professional',
        price: '499rb',
        desc: 'Hingga 100 karyawan',
        cta: 'Mulai Sekarang',
        primary: true,
        popular: true,
        features: ['Semua fitur Starter', 'Face Detection + GPS', 'Payroll lengkap + PPh21', 'Laporan & analitik', '20 GB storage'],
    },
    {
        name: 'Enterprise',
        price: '999rb',
        desc: 'Unlimited karyawan',
        cta: 'Hubungi Sales',
        primary: false,
        features: ['Semua fitur Professional', 'API access', 'Custom domain', 'Priority support', '100 GB storage'],
    },
];

const testimonials = [
    {
        initials: 'DW',
        name: 'Dewi Wulandari',
        role: 'HR Manager · PT Maju Jaya',
        quote: 'Sejak pakai HRHub, absensi karyawan jadi jauh lebih mudah. Tidak perlu lagi mesin fingerprint mahal. Face detection dari HP sudah cukup.',
    },
    {
        initials: 'RS',
        name: 'Rahmat Supriyadi',
        role: 'Finance Lead · CV Sejahtera',
        quote: 'Payroll yang dulu makan waktu 3 hari, sekarang selesai 1 jam. Kalkulasi BPJS dan PPh21 otomatis. Saya tidak mau kembali ke cara lama.',
    },
    {
        initials: 'AN',
        name: 'Ayu Nuraini',
        role: 'Operations · PT Nusantara',
        quote: 'Multi-tenant-nya brilian. 3 cabang, masing-masing subdomain sendiri. Data terisolasi, tapi diakses dari satu admin.',
    },
];
</script>

<template>
    <Head title="HRHub — HR SaaS Modern" />

    <div class="min-h-screen bg-background text-foreground antialiased selection:bg-primary selection:text-primary-foreground">
        <!-- NAVBAR -->
        <nav class="fixed inset-x-0 top-0 z-50 border-b border-border/60 bg-background/80 backdrop-blur-xl">
            <div class="mx-auto flex h-[68px] max-w-[1200px] items-center justify-between px-6">
                <a href="/" class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-sm font-extrabold text-primary-foreground">H</div>
                    <span class="text-lg font-bold tracking-tight">HRHub</span>
                </a>
                <div class="hidden items-center gap-1 md:flex">
                    <a href="#fitur" class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition hover:text-foreground">Fitur</a>
                    <a href="#cara" class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition hover:text-foreground">Cara Kerja</a>
                    <a href="#harga" class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition hover:text-foreground">Harga</a>
                    <a href="#testimonial" class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition hover:text-foreground">Testimoni</a>
                </div>
                <div class="flex items-center gap-2">
                    <Link v-if="canLogin" href="/login" class="hidden sm:inline-flex">
                        <Button variant="ghost" size="sm">Masuk</Button>
                    </Link>
                    <Link v-if="canRegister" href="/register">
                        <Button size="sm">Daftar Gratis</Button>
                    </Link>
                </div>
            </div>
        </nav>

        <!-- HERO -->
        <section class="relative flex min-h-screen items-center overflow-hidden pt-[68px]">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute top-20 left-1/2 h-[560px] w-[800px] -translate-x-1/2 rounded-full bg-primary/10 blur-[160px]" />
                <div class="absolute bottom-0 left-0 h-[400px] w-[400px] rounded-full bg-primary/5 blur-[120px]" />
            </div>
            <div class="mx-auto w-full max-w-[1200px] px-6">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div class="relative z-10">
                        <Badge variant="outline" class="gap-2 border-primary/30 bg-primary/5 px-3 py-1.5 text-primary">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-75" />
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-primary" />
                            </span>
                            Platform HR SaaS Indonesia
                        </Badge>
                        <h1 class="mt-6 text-[clamp(2.5rem,6vw,4.75rem)] font-bold leading-[0.95] tracking-tight">
                            Kelola
                            <span class="block text-primary">SDM</span>
                            <span class="block">lebih cerdas</span>
                        </h1>
                        <p class="mt-6 max-w-md text-base leading-relaxed text-muted-foreground">
                            Absensi wajah & QR, penggajian, cuti — satu platform untuk semua kebutuhan HR perusahaan Anda. Tanpa server, langsung pakai.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <Link v-if="canRegister" href="/register">
                                <Button size="lg" class="rounded-full px-8">
                                    Mulai Gratis 14 Hari
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Button>
                            </Link>
                            <Link v-if="canLogin" href="/login">
                                <Button variant="outline" size="lg" class="rounded-full px-8">Lihat Demo</Button>
                            </Link>
                        </div>
                        <div class="mt-10 flex items-center gap-6">
                            <div>
                                <p class="text-2xl font-bold">100<span class="text-primary">+</span></p>
                                <p class="text-xs text-muted-foreground">Perusahaan</p>
                            </div>
                            <div class="h-10 w-px bg-border" />
                            <div>
                                <p class="text-2xl font-bold">10k<span class="text-primary">+</span></p>
                                <p class="text-xs text-muted-foreground">Karyawan</p>
                            </div>
                            <div class="h-10 w-px bg-border" />
                            <div>
                                <p class="text-2xl font-bold">99.9<span class="text-primary">%</span></p>
                                <p class="text-xs text-muted-foreground">Uptime</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative hidden lg:block">
                        <Card class="relative z-10 mx-auto w-[460px] border-border bg-card/50 backdrop-blur-sm">
                            <CardContent class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-muted-foreground">Dashboard Overview</p>
                                        <p class="mt-1 text-2xl font-bold">Maju Jaya</p>
                                    </div>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-primary-foreground">
                                        <ScanFace class="h-5 w-5" />
                                    </div>
                                </div>
                                <div class="mt-6 grid grid-cols-3 gap-3">
                                    <div class="rounded-xl bg-muted p-4">
                                        <p class="text-xs text-muted-foreground">Karyawan</p>
                                        <p class="mt-1 text-xl font-bold">128</p>
                                    </div>
                                    <div class="rounded-xl bg-primary p-4">
                                        <p class="text-xs text-primary-foreground/80">Hadir</p>
                                        <p class="mt-1 text-xl font-bold text-primary-foreground">112</p>
                                    </div>
                                    <div class="rounded-xl bg-muted p-4">
                                        <p class="text-xs text-muted-foreground">Izin</p>
                                        <p class="mt-1 text-xl font-bold">8</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                        <Card class="absolute -top-4 -right-4 z-20 w-56 border-border bg-card p-4 shadow-2xl">
                            <CardContent class="flex items-center gap-3 p-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                    <Check class="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Absensi Berhasil</p>
                                    <p class="text-sm font-semibold">Budi S. • 08:02</p>
                                </div>
                            </CardContent>
                        </Card>
                        <Card class="absolute -bottom-4 -left-4 z-20 w-56 border-border bg-card p-4 shadow-2xl">
                            <CardContent class="flex items-center gap-3 p-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                    <Wallet class="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Payroll Selesai</p>
                                    <p class="text-sm font-semibold">Rp 412.000.000</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </section>

        <!-- FITUR -->
        <section id="fitur" class="relative py-24">
            <div class="mx-auto max-w-[1200px] px-6">
                <div class="max-w-2xl">
                    <Badge variant="outline" class="border-primary/30 text-primary">Fitur</Badge>
                    <h2 class="mt-4 text-[clamp(2rem,4vw,3rem)] font-bold leading-tight tracking-tight">Semua yang HR butuhkan<br />sudah ada di sini</h2>
                    <p class="mt-4 text-muted-foreground">Satu platform untuk absensi, penggajian, cuti, dan manajemen karyawan. Tanpa integrasi rumit.</p>
                </div>

                <div class="mt-14 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="feature in features"
                        :key="feature.title"
                        :class="[
                            'group transition-all hover:border-primary/40',
                            feature.accent ? 'md:col-span-2 lg:col-span-2' : '',
                            feature.accent ? 'border-primary/40 bg-primary/5' : '',
                        ]"
                    >
                        <CardHeader>
                            <div
                                :class="[
                                    'flex h-12 w-12 items-center justify-center rounded-xl',
                                    feature.accent ? 'bg-primary text-primary-foreground shadow-lg shadow-primary/20' : 'bg-muted text-foreground',
                                ]"
                            >
                                <component :is="feature.icon" class="h-6 w-6" />
                            </div>
                            <CardTitle class="mt-4">{{ feature.title }}</CardTitle>
                            <CardDescription>{{ feature.description }}</CardDescription>
                        </CardHeader>
                        <CardContent
                            v-if="feature.accent"
                            class="grid grid-cols-3 gap-3 pt-4"
                        >
                            <div class="rounded-xl bg-card p-4 text-center shadow-sm">
                                <p class="text-lg font-bold text-primary">90%+</p>
                                <p class="text-[11px] text-muted-foreground">Akurasi Wajah</p>
                            </div>
                            <div class="rounded-xl bg-card p-4 text-center shadow-sm">
                                <p class="text-lg font-bold text-primary">&lt;2 detik</p>
                                <p class="text-[11px] text-muted-foreground">Verifikasi</p>
                            </div>
                            <div class="rounded-xl bg-card p-4 text-center shadow-sm">
                                <p class="text-lg font-bold text-primary">100-200m</p>
                                <p class="text-[11px] text-muted-foreground">Radius Geofence</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- CARA KERJA -->
        <section id="cara" class="relative bg-muted py-24">
            <div class="mx-auto max-w-[1200px] px-6">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div>
                        <Badge variant="outline" class="border-primary/30 text-primary">Cara Kerja</Badge>
                        <h2 class="mt-4 text-[clamp(2rem,4vw,3rem)] font-bold leading-tight tracking-tight">Siap pakai dalam<br />3 langkah mudah</h2>
                        <p class="mt-4 text-muted-foreground">Tidak perlu setup server atau tim IT khusus. Langsung dari browser.</p>
                        <div class="mt-8 space-y-6">
                            <div v-for="step in steps" :key="step.number" class="flex gap-5">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary text-lg font-bold text-primary-foreground shadow-lg shadow-primary/20">
                                    {{ step.number }}
                                </div>
                                <div>
                                    <h4 class="font-bold">{{ step.title }}</h4>
                                    <p class="mt-1 text-sm text-muted-foreground">{{ step.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <Card class="border-border bg-card/60">
                        <CardContent class="p-6">
                            <div class="flex items-center gap-2 border-b border-border pb-5">
                                <span class="h-3 w-3 rounded-full bg-border" />
                                <span class="h-3 w-3 rounded-full bg-border" />
                                <span class="h-3 w-3 rounded-full bg-border" />
                                <span class="ml-3 text-xs text-muted-foreground">hrhub.id</span>
                            </div>
                            <div class="mt-6 space-y-3">
                                <div class="flex items-center justify-between rounded-xl bg-muted p-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground">1</span>
                                        <span class="text-sm font-medium">Daftar → pilih paket</span>
                                    </div>
                                    <Check class="h-5 w-5 text-primary" />
                                </div>
                                <div class="rounded-xl bg-muted p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground">2</span>
                                            <span class="text-sm font-medium">Subdomain aktif</span>
                                        </div>
                                        <Check class="h-5 w-5 text-primary" />
                                    </div>
                                    <p class="mt-2 ml-12 font-mono text-xs text-primary">pt-maju-jaya.hrhub.id</p>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-muted p-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground">3</span>
                                        <span class="text-sm font-medium">Setup wizard</span>
                                    </div>
                                    <Check class="h-5 w-5 text-primary" />
                                </div>
                                <div class="rounded-xl bg-primary p-4 text-center font-semibold text-primary-foreground shadow-lg shadow-primary/20">Siap pakai!</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- HARGA -->
        <section id="harga" class="relative py-24">
            <div class="mx-auto max-w-[1200px] px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <Badge variant="outline" class="border-primary/30 text-primary">Harga</Badge>
                    <h2 class="mt-4 text-[clamp(2rem,4vw,3rem)] font-bold leading-tight tracking-tight">Transparan. Tanpa biaya tersembunyi.</h2>
                </div>
                <div class="mt-14 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <Card
                        v-for="plan in plans"
                        :key="plan.name"
                        :class="[
                            'relative p-2',
                            plan.primary ? 'border-primary bg-primary/5 shadow-2xl shadow-primary/10' : 'border-border',
                        ]"
                    >
                        <Badge
                            v-if="plan.popular"
                            class="absolute -top-3 left-8 bg-primary text-primary-foreground"
                        >
                            Populer
                        </Badge>
                        <CardHeader>
                            <CardTitle :class="plan.primary ? 'text-primary' : ''">{{ plan.name }}</CardTitle>
                            <div class="mt-3 flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold">{{ plan.price }}</span>
                                <span class="text-sm text-muted-foreground">/bulan</span>
                            </div>
                            <CardDescription>{{ plan.desc }}</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <ul class="space-y-3 text-sm text-muted-foreground">
                                <li v-for="feature in plan.features" :key="feature" class="flex gap-3">
                                    <Check class="h-4 w-4 shrink-0 text-primary" />
                                    {{ feature }}
                                </li>
                            </ul>
                            <Link
                                :href="plan.name === 'Enterprise' ? '#' : '/register'"
                                class="block"
                            >
                                <Button
                                    :variant="plan.primary ? 'default' : 'outline'"
                                    class="w-full rounded-full"
                                >
                                    {{ plan.cta }}
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- TESTIMONI -->
        <section id="testimonial" class="relative bg-muted py-24">
            <div class="mx-auto max-w-[1200px] px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <Badge variant="outline" class="border-primary/30 text-primary">Testimoni</Badge>
                    <h2 class="mt-4 text-[clamp(2rem,4vw,3rem)] font-bold leading-tight tracking-tight">Dipercaya HR profesional</h2>
                </div>
                <div class="mt-14 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <Card
                        v-for="testimonial in testimonials"
                        :key="testimonial.name"
                        class="border-border"
                    >
                        <CardContent class="p-6">
                            <div class="flex gap-0.5 text-primary">
                                <span v-for="n in 5" :key="n">★</span>
                            </div>
                            <p class="mt-4 text-sm leading-relaxed text-muted-foreground">"{{ testimonial.quote }}"</p>
                            <div class="mt-6 flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground">
                                    {{ testimonial.initials }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">{{ testimonial.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ testimonial.role }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="relative py-24">
            <div class="mx-auto max-w-[1200px] px-6">
                <Card class="relative overflow-hidden border-primary/30 bg-card px-8 py-16 text-center sm:px-16 sm:py-20">
                    <div class="pointer-events-none absolute inset-0">
                        <div class="absolute top-1/2 left-1/2 h-[300px] w-[420px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-primary/10 blur-[100px]" />
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-[clamp(1.75rem,4vw,2.75rem)] font-bold leading-tight tracking-tight">Siap transformasi HR<br />perusahaan Anda?</h2>
                        <p class="mx-auto mt-4 max-w-lg text-muted-foreground">Bergabung dengan 100+ perusahaan Indonesia. Mulai sekarang — gratis 14 hari tanpa kartu kredit.</p>
                        <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                            <Link v-if="canRegister" href="/register">
                                <Button size="lg" class="rounded-full px-8">
                                    Mulai Gratis Sekarang
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Button>
                            </Link>
                            <Link href="/login">
                                <Button variant="outline" size="lg" class="rounded-full px-8">Sudah punya akun? Masuk</Button>
                            </Link>
                        </div>
                    </div>
                </Card>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="border-t border-border bg-muted py-10">
            <div class="mx-auto flex max-w-[1200px] flex-col items-center justify-between gap-6 px-6 sm:flex-row">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-xs font-bold text-primary-foreground">H</div>
                    <span class="text-sm font-bold">HRHub</span>
                    <span class="text-xs text-muted-foreground">© 2026</span>
                </div>
                <div class="flex gap-6 text-xs text-muted-foreground">
                    <a href="#" class="transition hover:text-foreground">Privasi</a>
                    <a href="#" class="transition hover:text-foreground">Syarat</a>
                    <a href="#" class="transition hover:text-foreground">Bantuan</a>
                </div>
            </div>
        </footer>
    </div>
</template>
