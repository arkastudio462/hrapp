<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Menu, X, LogOut, Users, Clock, CalendarCheck, Wallet, LayoutDashboard } from 'lucide-vue-next';

defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const tenant = computed(() => page.props.tenant);
const mobileMenuOpen = ref(false);

const navItems = computed(() => {
    const items = [
        { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard, show: true },
        { href: '/employees', label: 'Karyawan', icon: Users, show: true },
        { href: '/attendance', label: 'Absensi', icon: Clock, show: true },
        { href: '/leave', label: 'Izin & Cuti', icon: CalendarCheck, show: true },
        { href: '/payroll', label: 'Penggajian', icon: Wallet, show: true },
    ];
    return items.filter((i) => i.show);
});

const isActive = (href) => {
    if (href === '/dashboard') return page.url === '/dashboard';
    return page.url.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen bg-white">
        <!-- Navigation -->
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <Link href="/" class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                            <span class="h-2 w-2 rounded-[1px] bg-white" />
                        </span>
                        <span class="font-display text-lg font-bold tracking-tight">HRapp</span>
                    </Link>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                            :class="isActive(item.href) ? 'bg-stone-100 text-ink' : 'text-inkmuted hover:text-ink hover:bg-stone-50'"
                        >
                            {{ item.label }}
                        </Link>
                    </nav>
                </div>
                <div class="flex items-center gap-3">
                    <template v-if="user">
                        <div class="hidden items-center gap-3 md:flex">
                            <span class="text-sm text-inkmuted">{{ user.name }}</span>
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="flex items-center gap-2 rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-ink transition-colors hover:bg-stone-50"
                            >
                                <LogOut class="h-4 w-4" />
                                Logout
                            </Link>
                        </div>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="rounded-full bg-ink px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-moss-700"
                        >
                            Login
                        </Link>
                    </template>

                    <!-- Mobile menu button -->
                    <button
                        v-if="user"
                        type="button"
                        class="inline-flex items-center justify-center p-2 text-inkmuted hover:text-ink md:hidden"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <X v-if="mobileMenuOpen" class="h-5 w-5" />
                        <Menu v-else class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-if="mobileMenuOpen && user" class="border-t border-stone-200 bg-white md:hidden">
                <div class="space-y-1 px-4 pb-3 pt-2">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                        :class="isActive(item.href) ? 'bg-stone-100 text-ink' : 'text-inkmuted hover:text-ink hover:bg-stone-50'"
                        @click="mobileMenuOpen = false"
                    >
                        <component :is="item.icon" class="h-4 w-4" />
                        {{ item.label }}
                    </Link>
                    <div class="mt-2 border-t border-stone-200 pt-2">
                        <div class="px-3 py-2 text-sm text-inkmuted">{{ user.name }}</div>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-inkmuted transition-colors hover:text-ink hover:bg-stone-50"
                            @click="mobileMenuOpen = false"
                        >
                            <LogOut class="h-4 w-4" />
                            Logout
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Header -->
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">{{ title }}</h1>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
