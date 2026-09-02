<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
</script>

<template>
    <div class="min-h-screen bg-background">
        <!-- Navigation -->
        <nav class="border-b border-border bg-card">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-8">
                        <Link href="/" class="text-xl font-bold text-primary">
                            {{ tenant?.name || 'HRHub' }}
                        </Link>
                        <div class="hidden md:flex md:gap-6">
                            <Link
                                v-if="user"
                                href="/dashboard"
                                class="text-sm font-medium text-muted-foreground hover:text-foreground"
                                :class="{ 'text-foreground': $page.url === '/dashboard' }"
                            >
                                Dashboard
                            </Link>
                            <Link
                                v-if="user"
                                href="/employees"
                                class="text-sm font-medium text-muted-foreground hover:text-foreground"
                                :class="{ 'text-foreground': $page.url.startsWith('/employees') }"
                            >
                                Karyawan
                            </Link>
                            <Link
                                v-if="user"
                                href="/attendance"
                                class="text-sm font-medium text-muted-foreground hover:text-foreground"
                                :class="{ 'text-foreground': $page.url.startsWith('/attendance') }"
                            >
                                Absensi
                            </Link>
                            <Link
                                v-if="user"
                                href="/leave"
                                class="text-sm font-medium text-muted-foreground hover:text-foreground"
                                :class="{ 'text-foreground': $page.url.startsWith('/leave') }"
                            >
                                Izin & Cuti
                            </Link>
                            <Link
                                v-if="user"
                                href="/payroll"
                                class="text-sm font-medium text-muted-foreground hover:text-foreground"
                                :class="{ 'text-foreground': $page.url.startsWith('/payroll') }"
                            >
                                Penggajian
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <template v-if="user">
                            <div class="hidden md:flex md:items-center md:gap-4">
                                <span class="text-sm text-muted-foreground">{{ user.name }}</span>
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                                >
                                    Logout
                                </Link>
                            </div>
                        </template>
                        <template v-else>
                            <Link
                                href="/login"
                                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                            >
                                Login
                            </Link>
                        </template>

                        <!-- Mobile menu button -->
                        <button
                            v-if="user"
                            type="button"
                            class="inline-flex items-center justify-center rounded-md p-2 text-muted-foreground hover:text-foreground md:hidden"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-if="mobileMenuOpen && user" class="border-t border-border md:hidden">
                <div class="space-y-1 px-4 pb-3 pt-2">
                    <Link
                        href="/dashboard"
                        class="block rounded-md px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                    >
                        Dashboard
                    </Link>
                    <Link
                        href="/employees"
                        class="block rounded-md px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                    >
                        Karyawan
                    </Link>
                    <Link
                        href="/attendance"
                        class="block rounded-md px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                    >
                        Absensi
                    </Link>
                    <Link
                        href="/leave"
                        class="block rounded-md px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                    >
                        Izin & Cuti
                    </Link>
                    <Link
                        href="/payroll"
                        class="block rounded-md px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                    >
                        Penggajian
                    </Link>
                    <div class="border-t border-border mt-2 pt-2">
                        <div class="px-3 py-2 text-sm text-muted-foreground">{{ user.name }}</div>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                        >
                            Logout
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Header -->
        <header class="border-b border-border bg-card">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-foreground">{{ title }}</h1>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
