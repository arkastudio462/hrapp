<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import {
    Menu,
    X,
    LogOut,
    Search,
    Bell,
    Plus,
    Calendar,
    ChevronsUpDown,
    ChevronDown,
    HelpCircle,
    User,
    LayoutDashboard,
    Users,
    Building2,
    Clock,
    CalendarCheck,
    Wallet,
    BarChart3,
    ReceiptText,
    Settings,
    BadgeCheck,
    UserCircle,
    Package,
    FileText,
    Activity,
    CheckCheck,
} from 'lucide-vue-next';

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const tenant = computed(() => page.props.tenant);
const flash = computed(() => page.props.flash);
const sidebarOpen = ref(false);

const isFaceLocation = computed(() => (tenant.value?.attendance_method || 'face_location') === 'face_location');

const tenantPlan = computed(() => tenant.value?.package?.name || 'Free');
const maxEmployees = computed(() => tenant.value?.package?.max_employees || tenant.value?.limits?.max_employees || 0);
const employeeCount = computed(() => tenant.value?.employee_count || 0);
const quotaPercent = computed(() => maxEmployees.value > 0 ? Math.round((employeeCount.value / maxEmployees.value) * 100) : 0);

const notifOpen = ref(false);
const profileOpen = ref(false);
const notifRef = ref(null);
const profileRef = ref(null);

const notifications = ref([]);

const unreadCount = computed(() => notifications.value.filter((n) => n.unread).length);

const toggleNotif = () => {
    notifOpen.value = !notifOpen.value;
    profileOpen.value = false;
};

const toggleProfile = () => {
    profileOpen.value = !profileOpen.value;
    notifOpen.value = false;
};

const closeDropdowns = () => {
    notifOpen.value = false;
    profileOpen.value = false;
};

const handleClickOutside = (event) => {
    if (notifRef.value && !notifRef.value.contains(event.target)) {
        notifOpen.value = false;
    }
    if (profileRef.value && !profileRef.value.contains(event.target)) {
        profileOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));

const roleLabels = {
    super_admin: 'Super Admin',
    admin: 'Administrator',
    hr: 'HR Manager',
    employee: 'Karyawan',
};

const navByRole = {
    super_admin: [
        { href: '/super-admin/dashboard', label: 'Dashboard', icon: LayoutDashboard },
        { href: '/super-admin/tenants', label: 'Tenants', icon: Building2 },
        { href: '/super-admin/packages', label: 'Paket', icon: Package },
        { href: '/super-admin/invoices', label: 'Invoice', icon: FileText },
        { href: '/super-admin/users', label: 'Users', icon: Users },
        { href: '/super-admin/analytics', label: 'Analytics', icon: BarChart3 },
        { href: '/super-admin/activity-logs', label: 'Activity Logs', icon: Activity },
    ],
    admin: [
        { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
        { href: '/employees', label: 'Karyawan', icon: Users },
        { href: '/departments', label: 'Departemen & Jabatan', icon: Building2 },
        { href: '/attendances', label: 'Absensi', icon: Clock },
        { href: '/leaves', label: 'Izin & Cuti', icon: CalendarCheck },
        { href: '/payroll', label: 'Penggajian', icon: Wallet },
        { href: '/subscription', label: 'Billing & Langganan', icon: ReceiptText },
        { href: '/settings', label: 'Pengaturan', icon: Settings },
    ],
    hr: [
        { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
        { href: '/employees', label: 'Karyawan', icon: Users },
        { href: '/departments', label: 'Departemen & Jabatan', icon: Building2 },
        { href: '/attendances', label: 'Absensi', icon: Clock },
        { href: '/leaves', label: 'Izin & Cuti', icon: CalendarCheck },
        { href: '/payroll', label: 'Penggajian', icon: Wallet },
    ],
    employee: [
        { href: '/my-dashboard', label: 'Dashboard', icon: LayoutDashboard },
        { href: '/my-attendance', label: 'Absensi', icon: Clock },
        { href: '/my-leave', label: 'Izin & Cuti', icon: CalendarCheck },
        { href: '/my-payslip', label: 'Payslip', icon: Wallet },
        { href: '/my-profile', label: 'Profil', icon: UserCircle },
        { href: '/face-attendance', label: 'Absen Wajah', icon: BadgeCheck },
    ],
};

const homeHrefs = {
    super_admin: '/super-admin/dashboard',
    admin: '/dashboard',
    hr: '/dashboard',
    employee: '/my-dashboard',
};

const logoutHrefs = {
    super_admin: '/super-admin/logout',
    admin: '/logout',
    hr: '/logout',
    employee: '/logout',
};

const userRole = computed(() => user.value?.role || 'employee');
const navItems = computed(() => {
    const items = navByRole[userRole.value] || navByRole.employee;
    if (userRole.value === 'employee' && !isFaceLocation.value) {
        return items.filter((item) => item.href !== '/face-attendance');
    }
    return items;
});
const homeHref = computed(() => homeHrefs[userRole.value] || '/dashboard');
const logoutHref = computed(() => logoutHrefs[userRole.value] || '/logout');
const showTenant = computed(() => userRole.value !== 'super_admin');
const isAdminOrHr = computed(() => ['admin', 'hr'].includes(userRole.value));

const profileLinks = computed(() => {
    if (userRole.value === 'employee') {
        return [{ href: '/my-profile', label: 'Profil Saya', icon: UserCircle }];
    }
    if (isAdminOrHr.value) {
        return [{ href: '/settings', label: 'Pengaturan', icon: Settings }];
    }
    return [];
});

const searchPlaceholders = {
    super_admin: 'Cari tenant, analytics...',
    admin: 'Cari karyawan, departemen, slip gaji...',
    hr: 'Cari karyawan, departemen, slip gaji...',
    employee: 'Cari menu...',
};

const breadcrumbLabels = {
    super_admin: 'Super Admin',
    admin: 'HR & People Ops',
    hr: 'HR & People Ops',
    employee: 'Portal Karyawan',
};

const searchPlaceholder = computed(() => searchPlaceholders[userRole.value] || 'Cari...');
const breadcrumbLabel = computed(() => breadcrumbLabels[userRole.value] || 'HR & People Ops');

const currentMonthLabel = new Date().toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });

const isActive = (href) => {
    if (href === '#') return false;
    if (href === '/dashboard') return page.url === '/dashboard';
    return page.url.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen bg-cream">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-[260px] flex-col bg-white shadow-soft transition-transform duration-200 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Brand -->
            <div class="flex flex-col gap-2 p-4">
                <Link :href="homeHref" class="flex items-center gap-2.5">
                    <div class="flex size-10 items-center justify-center rounded-2xl bg-coral text-lg font-extrabold text-white shadow-card shadow-coral/20">
                        H
                    </div>
                    <div class="flex min-w-0 flex-col">
                        <span class="truncate text-base font-extrabold tracking-tight text-ink-app">HRHub</span>
                        <span class="text-[11px] font-medium text-muted-app">People Operations</span>
                    </div>
                </Link>

                <!-- Tenant selector -->
                <div v-if="showTenant" class="mt-1 rounded-2xl bg-cream p-3">
                    <div class="flex min-w-0 flex-col">
                        <div class="flex items-center gap-1.5">
                            <Building2 class="size-3.5 shrink-0 text-muted-app" />
                            <span class="truncate text-sm font-bold text-ink-app">{{ tenant?.name || 'Belum ada tenant' }}</span>
                            <ChevronsUpDown v-if="isAdminOrHr" class="size-3.5 shrink-0 text-muted-app" />
                        </div>
                        <div v-if="isAdminOrHr && maxEmployees > 0" class="mt-1.5 flex items-center gap-2">
                            <span class="rounded-full bg-lavender px-2 py-0.5 text-[10px] font-extrabold text-purple-700">{{ tenantPlan }}</span>
                            <span class="text-[11px] text-muted-app">{{ employeeCount }}/{{ maxEmployees }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 space-y-0.5 overflow-y-auto px-3">
                <Link
                    v-for="item in navItems"
                    :key="item.label"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-all"
                    :class="
                        isActive(item.href)
                            ? 'bg-coral/10 text-coral shadow-sm'
                            : 'text-muted-app hover:bg-cream hover:text-ink-app'
                    "
                    @click="sidebarOpen = false"
                >
                    <component :is="item.icon" class="size-5 shrink-0" />
                    <span>{{ item.label }}</span>
                    <span v-if="item.badge" class="ml-auto rounded-full bg-amber px-2 py-0.5 text-[10px] font-extrabold text-ink-app">
                        {{ item.badge }}
                    </span>
                </Link>
            </nav>

            <!-- Footer -->
            <div class="flex flex-col gap-3 border-t border-outline-app/50 p-4">
                <div v-if="showTenant && isAdminOrHr && maxEmployees > 0" class="flex flex-col gap-1.5">
                    <div class="flex justify-between text-[11px]">
                        <span class="font-medium text-muted-app">Kuota Karyawan</span>
                        <span class="font-bold text-ink-app">{{ quotaPercent }}%</span>
                    </div>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-cream">
                        <div class="h-full rounded-full bg-coral transition-all duration-500" :style="{ width: Math.min(quotaPercent, 100) + '%' }"></div>
                    </div>
                    <span class="text-[11px] text-muted-app">{{ employeeCount }}/{{ maxEmployees }} terpakai</span>
                </div>
                <Link href="#" class="flex items-center gap-2 py-0.5 text-sm font-semibold text-muted-app transition-colors hover:text-ink-app">
                    <HelpCircle class="size-4" />
                    <span>Bantuan & Dukungan</span>
                </Link>
            </div>
        </aside>

        <!-- Backdrop (mobile) -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/30 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Content -->
        <div class="lg:pl-[260px]">
            <!-- Header -->
            <header class="fixed top-0 right-0 left-0 z-30 flex h-16 items-center justify-between border-b border-outline-app/30 bg-white/90 px-6 backdrop-blur-xl lg:left-[260px]">
                <div class="flex max-w-2xl flex-1 items-center gap-6">
                    <button type="button" class="text-muted-app hover:text-ink-app lg:hidden" @click="sidebarOpen = !sidebarOpen">
                        <Menu v-if="!sidebarOpen" class="size-5" />
                        <X v-else class="size-5" />
                    </button>

                    <div class="hidden items-center gap-1.5 text-xs text-muted-app md:flex">
                        <span class="cursor-pointer hover:text-ink-app">{{ breadcrumbLabel }}</span>
                        <span>/</span>
                        <span class="font-bold text-ink-app">{{ title }}</span>
                    </div>

                    <div class="relative max-w-md flex-1">
                        <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-app" />
                        <input
                            type="text"
                            :placeholder="searchPlaceholder"
                            class="w-full rounded-xl border border-outline-app/50 bg-cream py-2 pr-4 pl-9 text-xs font-semibold text-ink-app placeholder:text-muted-app focus:border-coral focus:ring-2 focus:ring-coral/20 focus:outline-none"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div v-if="isAdminOrHr" class="hidden items-center gap-1.5 rounded-xl bg-cream px-3 py-1.5 xl:flex">
                        <Calendar class="size-3.5 text-muted-app" />
                        <span class="text-[11px] font-bold text-ink-app">{{ currentMonthLabel }}</span>
                    </div>

                    <!-- Notification Dropdown -->
                    <div ref="notifRef" class="relative">
                        <button
                            type="button"
                            class="relative rounded-xl p-2 text-muted-app transition-colors hover:bg-cream hover:text-ink-app"
                            :class="notifOpen ? 'bg-cream text-ink-app' : ''"
                            @click="toggleNotif"
                        >
                            <Bell class="size-5" />
                            <span
                                v-if="unreadCount > 0"
                                class="absolute -top-0.5 -right-0.5 flex size-4 items-center justify-center rounded-full bg-coral text-[9px] font-extrabold text-white"
                            >
                                {{ unreadCount }}
                            </span>
                        </button>

                        <transition
                            enter-active-class="transition duration-100 ease-out"
                            enter-from-class="opacity-0 scale-95 -translate-y-1"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition duration-75 ease-in"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-95"
                        >
                            <div
                                v-if="notifOpen"
                                class="absolute right-0 z-50 mt-2 w-80 origin-top-right overflow-hidden rounded-2xl border border-outline-app/30 bg-white shadow-card"
                            >
                                <div class="flex items-center justify-between border-b border-outline-app/30 px-4 py-3">
                                    <div>
                                        <p class="text-sm font-extrabold text-ink-app">Notifikasi</p>
                                        <p class="text-[11px] text-muted-app">{{ unreadCount }} belum dibaca</p>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[11px] font-bold text-coral hover:bg-coral/10"
                                    >
                                        <CheckCheck class="size-3.5" />
                                        Tandai dibaca
                                    </button>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <div
                                        v-for="notif in notifications"
                                        :key="notif.id"
                                        class="flex gap-3 border-b border-outline-app/20 px-4 py-3 transition-colors hover:bg-cream/50"
                                        :class="notif.unread ? 'bg-coral/[0.03]' : ''"
                                    >
                                        <div class="flex size-9 shrink-0 items-center justify-center rounded-xl" :class="notif.iconColor">
                                            <component :is="notif.icon" class="size-4" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5">
                                                <p class="truncate text-sm font-bold text-ink-app">{{ notif.title }}</p>
                                                <span v-if="notif.unread" class="size-1.5 shrink-0 rounded-full bg-coral"></span>
                                            </div>
                                            <p class="mt-0.5 line-clamp-2 text-xs text-muted-app">{{ notif.description }}</p>
                                            <p class="mt-1 text-[10px] text-muted-app/70">{{ notif.time }}</p>
                                        </div>
                                    </div>
                                    <div v-if="!notifications.length" class="px-4 py-8 text-center text-sm text-muted-app">
                                        Tidak ada notifikasi.
                                    </div>
                                </div>
                                <div class="border-t border-outline-app/30 p-2">
                                    <button
                                        type="button"
                                        class="w-full rounded-xl px-3 py-2 text-center text-sm font-bold text-coral transition-colors hover:bg-coral/5"
                                    >
                                        Lihat semua notifikasi
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>

                    <button
                        v-if="isAdminOrHr"
                        type="button"
                        class="hidden items-center gap-2 rounded-xl bg-coral px-4 py-2 text-sm font-bold text-white shadow-soft transition-colors hover:bg-coral-deep sm:flex"
                    >
                        <Plus class="size-4" />
                        <span>Entri Cepat</span>
                    </button>

                    <div v-if="isAdminOrHr" class="hidden h-6 w-px bg-outline-app/50 sm:block"></div>

                    <!-- Profile Dropdown -->
                    <div v-if="user" ref="profileRef" class="relative">
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-xl p-1.5 transition-colors hover:bg-cream"
                            :class="profileOpen ? 'bg-cream' : ''"
                            @click="toggleProfile"
                        >
                            <div class="flex size-9 items-center justify-center rounded-full bg-lavender text-sm font-extrabold text-purple-700">
                                {{ user.name?.charAt(0) || 'U' }}
                            </div>
                            <div class="hidden flex-col text-left lg:flex">
                                <span class="text-sm leading-tight font-bold text-ink-app">{{ user.name }}</span>
                                <span class="text-[11px] leading-tight text-muted-app">{{ roleLabels[user.role] || 'Karyawan' }}</span>
                            </div>
                            <ChevronDown class="hidden size-4 text-muted-app transition-transform lg:block" :class="profileOpen ? 'rotate-180' : ''" />
                        </button>

                        <transition
                            enter-active-class="transition duration-100 ease-out"
                            enter-from-class="opacity-0 scale-95 -translate-y-1"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition duration-75 ease-in"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-95"
                        >
                            <div
                                v-if="profileOpen"
                                class="absolute right-0 z-50 mt-2 w-64 origin-top-right overflow-hidden rounded-2xl border border-outline-app/30 bg-white shadow-card"
                            >
                                <div class="border-b border-outline-app/30 px-4 py-3">
                                    <p class="truncate text-sm font-extrabold text-ink-app">{{ user.name }}</p>
                                    <p class="truncate text-[11px] text-muted-app">{{ user.email }}</p>
                                </div>
                                <div class="p-1.5">
                                    <Link
                                        v-for="link in profileLinks"
                                        :key="link.href"
                                        :href="link.href"
                                        class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold text-ink-app transition-colors hover:bg-cream"
                                        @click="closeDropdowns"
                                    >
                                        <component :is="link.icon" class="size-4 text-muted-app" />
                                        {{ link.label }}
                                    </Link>
                                    <div class="my-1 h-px bg-outline-app/30"></div>
                                    <Link
                                        :href="logoutHref"
                                        method="post"
                                        as="button"
                                        class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold text-coral transition-colors hover:bg-coral/10"
                                        @click="closeDropdowns"
                                    >
                                        <LogOut class="size-4" />
                                        Keluar
                                    </Link>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </header>

            <!-- Main -->
            <main class="min-h-screen w-full bg-cream px-6 pt-24 pb-8">
                <div class="flex w-full flex-col space-y-6">
                    <!-- Flash Messages -->
                    <div v-if="flash?.success" class="rounded-2xl border border-sage bg-sage/30 p-4 text-sm font-semibold text-green-800 animate-fade-up">
                        {{ flash.success }}
                    </div>
                    <div v-if="flash?.error" class="rounded-2xl border border-blush bg-blush/30 p-4 text-sm font-semibold text-coral-deep animate-fade-up">
                        {{ flash.error }}
                    </div>
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
