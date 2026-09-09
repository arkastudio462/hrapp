<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    Menu,
    X,
    LogOut,
    Search,
    Bell,
    Plus,
    Calendar,
    ChevronsUpDown,
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
const sidebarOpen = ref(false);

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
        { href: '/super-admin/analytics', label: 'Analytics', icon: BarChart3 },
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
const navItems = computed(() => navByRole[userRole.value] || navByRole.employee);
const homeHref = computed(() => homeHrefs[userRole.value] || '/dashboard');
const logoutHref = computed(() => logoutHrefs[userRole.value] || '/logout');
const showTenant = computed(() => userRole.value !== 'super_admin');
const isAdminOrHr = computed(() => ['admin', 'hr'].includes(userRole.value));

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

const isActive = (href) => {
    if (href === '#') return false;
    if (href === '/dashboard') return page.url === '/dashboard';
    return page.url.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen bg-surface">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-[260px] flex-col bg-surface-card shadow-[0_1px_8px_rgba(0,0,0,0.04)] transition-transform duration-200 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Brand -->
            <div class="flex flex-col gap-2 p-4">
                <Link :href="homeHref" class="flex items-center gap-2">
                    <div class="relative flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-base font-bold text-on-primary shadow-sm">
                        H
                        <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-primary-fixed-dim"></span>
                    </div>
                    <div class="flex min-w-0 flex-col">
                        <span class="truncate text-base font-semibold leading-tight text-on-surface">HRHub</span>
                        <span class="text-xs leading-none text-on-surface-variant">People Operations</span>
                    </div>
                </Link>

                <!-- Tenant selector -->
                <div v-if="showTenant" class="mt-1 flex items-center justify-between rounded-lg bg-surface-subtle p-2">
                    <div class="flex min-w-0 flex-col">
                        <div class="flex items-center gap-1">
                            <span class="truncate text-sm font-medium text-on-surface">{{ tenant?.name || 'PT Nusantara Prima' }}</span>
                            <ChevronsUpDown v-if="isAdminOrHr" class="h-4 w-4 shrink-0 text-on-surface-variant" />
                        </div>
                        <div v-if="isAdminOrHr" class="mt-1 flex items-center gap-1">
                            <span class="rounded bg-primary-fixed px-1.5 py-0.5 text-xs font-semibold text-on-primary-fixed">Pro Plan</span>
                            <span class="text-xs text-on-surface-variant">142/200 Kuota</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 space-y-0.5 overflow-y-auto px-2">
                <Link
                    v-for="item in navItems"
                    :key="item.label"
                    :href="item.href"
                    class="flex items-center justify-between rounded-lg px-2 py-2 transition-colors"
                    :class="
                        isActive(item.href)
                            ? 'bg-primary-container text-on-primary-container'
                            : 'text-on-surface-variant hover:bg-surface-subtle hover:text-on-surface'
                    "
                    @click="sidebarOpen = false"
                >
                    <div class="flex items-center gap-2">
                        <component :is="item.icon" class="h-5 w-5" />
                        <span class="text-sm font-medium">{{ item.label }}</span>
                    </div>
                    <span v-if="item.badge" class="rounded-full bg-status-warning px-2 py-0.5 text-xs font-semibold text-on-primary">
                        {{ item.badge }}
                    </span>
                </Link>
            </nav>

            <!-- Footer -->
            <div class="flex flex-col gap-3 border-t border-border-subtle p-4">
                <div v-if="showTenant && isAdminOrHr" class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-on-surface-variant">Sisa Kuota Karyawan</span>
                        <span class="text-xs font-semibold text-on-surface">71%</span>
                    </div>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-surface-subtle">
                        <div class="h-full w-[71%] rounded-full bg-status-info"></div>
                    </div>
                    <span class="text-xs text-on-surface-variant">142 / 200 Karyawan Terpakai</span>
                </div>
                <Link href="#" class="flex items-center gap-2 py-0.5 text-sm font-medium text-on-surface-variant transition-colors hover:text-on-surface">
                    <HelpCircle class="h-5 w-5" />
                    <span>Bantuan & Dukungan</span>
                </Link>
            </div>
        </aside>

        <!-- Backdrop (mobile) -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/30 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Content -->
        <div class="lg:pl-[260px]">
            <!-- Header -->
            <header class="fixed top-0 right-0 left-0 z-30 flex h-16 items-center justify-between bg-surface-card/90 px-6 shadow-[0_1px_8px_rgba(0,0,0,0.04)] backdrop-blur-xl lg:left-[260px]">
                <div class="flex max-w-2xl flex-1 items-center gap-6">
                    <button type="button" class="text-on-surface-variant hover:text-on-surface lg:hidden" @click="sidebarOpen = !sidebarOpen">
                        <Menu v-if="!sidebarOpen" class="h-5 w-5" />
                        <X v-else class="h-5 w-5" />
                    </button>

                    <div class="hidden items-center gap-1 text-xs text-on-surface-variant md:flex">
                        <span class="cursor-pointer hover:text-on-surface">{{ breadcrumbLabel }}</span>
                        <span>/</span>
                        <span class="text-sm font-medium text-on-surface">{{ title }}</span>
                    </div>

                    <div class="relative max-w-md flex-1">
                        <Search class="absolute top-1/2 left-2.5 h-4 w-4 -translate-y-1/2 text-on-surface-variant" />
                        <input
                            type="text"
                            :placeholder="searchPlaceholder"
                            class="w-full rounded-lg bg-surface-subtle py-1.5 pr-4 pl-9 text-xs text-on-surface placeholder:text-on-surface-variant focus:ring-2 focus:ring-primary/20 focus:outline-none"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div v-if="isAdminOrHr" class="hidden items-center gap-1 rounded-lg bg-surface-subtle px-2 py-1 xl:flex">
                        <Calendar class="h-4 w-4 text-tertiary" />
                        <span class="text-xs font-semibold text-on-surface">Periode: Mei 2025</span>
                    </div>

                    <button type="button" class="relative rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-surface-subtle hover:text-on-surface">
                        <Bell class="h-5 w-5" />
                        <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-status-error"></span>
                    </button>

                    <button
                        v-if="isAdminOrHr"
                        type="button"
                        class="hidden items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-on-primary shadow-sm transition-colors hover:bg-primary-container sm:flex"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Entri Cepat</span>
                    </button>

                    <div v-if="isAdminOrHr" class="hidden h-6 w-px bg-border-subtle sm:block"></div>

                    <div v-if="user" class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary">
                            <User class="h-4 w-4 text-on-primary" />
                        </div>
                        <div class="hidden flex-col text-left lg:flex">
                            <span class="text-sm leading-tight font-medium text-on-surface">{{ user.name }}</span>
                            <span class="text-xs leading-tight text-on-surface-variant">{{ roleLabels[user.role] || 'Karyawan' }}</span>
                        </div>
                        <Link :href="logoutHref" method="post" as="button" class="hidden text-on-surface-variant hover:text-on-surface lg:block">
                            <LogOut class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </header>

            <!-- Main -->
            <main class="min-h-screen w-full bg-surface-canvas px-6 pt-16 pb-6">
                <div class="flex w-full flex-col space-y-6">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
