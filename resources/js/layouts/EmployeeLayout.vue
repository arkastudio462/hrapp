<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import {
    LayoutDashboard,
    Clock,
    ScanFace,
    CalendarCheck,
    Wallet,
    LogOut,
    User,
    ChevronDown,
} from 'lucide-vue-next';

const props = defineProps({
    title: {
        type: String,
        default: 'Portal Karyawan',
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash);
const tenant = computed(() => page.props.tenant);
const isFaceLocation = computed(() => (tenant.value?.attendance_method || 'face_location') === 'face_location');

const profileOpen = ref(false);
const profileRef = ref(null);

const tabsLeft = [
    { href: '/my-dashboard', label: 'Beranda', icon: LayoutDashboard, exact: true },
    { href: '/my-attendance', label: 'Absensi', icon: Clock },
];

const tabsRight = [
    { href: '/my-leave', label: 'Izin', icon: CalendarCheck },
    { href: '/my-payslip', label: 'Payslip', icon: Wallet },
];

const isActive = (tab) => {
    if (tab.exact) {
        return page.url === tab.href;
    }
    return page.url.startsWith(tab.href);
};

const initials = computed(() => {
    const name = user.value?.name || '?';
    return name.split(' ').map((n) => n[0]).slice(0, 2).join('').toUpperCase();
});

const toggleProfile = () => {
    profileOpen.value = !profileOpen.value;
};

const handleClickOutside = (event) => {
    if (profileRef.value && !profileRef.value.contains(event.target)) {
        profileOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
    <div class="min-h-screen bg-cream pb-28">
        <!-- Header -->
        <header class="sticky top-0 z-40 border-b border-outline-app/60 bg-cream/85 backdrop-blur-sm">
            <div class="mx-auto flex h-16 max-w-lg items-center justify-between px-5">
                <div class="flex items-center gap-2.5">
                    <div class="flex size-9 items-center justify-center rounded-xl bg-coral text-sm font-extrabold text-white shadow-soft">
                        H
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="text-base font-extrabold tracking-tight text-ink-app">HRHub</span>
                        <span class="mt-0.5 text-[11px] font-medium text-muted-app">{{ title }}</span>
                    </div>
                </div>

                <!-- Profile -->
                <div ref="profileRef" class="relative">
                    <button
                        type="button"
                        class="flex items-center gap-1 rounded-full p-1 transition-colors hover:bg-ink-app/5 active:scale-95"
                        @click="toggleProfile"
                    >
                        <span class="flex size-8 items-center justify-center rounded-full bg-lavender text-xs font-extrabold text-ink-app">
                            {{ initials }}
                        </span>
                        <ChevronDown class="size-4 text-muted-app" :class="profileOpen ? 'rotate-180 transition-transform' : 'transition-transform'" />
                    </button>

                    <transition
                        enter-active-class="transition duration-100 ease-out"
                        enter-from-class="opacity-0 -translate-y-1 scale-95"
                        enter-to-class="opacity-100 translate-y-0 scale-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="profileOpen"
                            class="absolute right-0 z-50 mt-2 w-60 overflow-hidden rounded-2xl border border-outline-app bg-white shadow-card"
                        >
                            <div class="border-b border-outline-app/60 px-4 py-3">
                                <p class="truncate text-sm font-bold text-ink-app">{{ user?.name }}</p>
                                <p class="truncate text-xs text-muted-app">{{ user?.email }}</p>
                            </div>
                            <div class="p-1.5">
                                <Link
                                    href="/my-profile"
                                    class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold text-ink-app transition-colors hover:bg-cream"
                                    @click="profileOpen = false"
                                >
                                    <User class="size-4 text-muted-app" />
                                    Profil Saya
                                </Link>
                                <div class="my-1 h-px bg-outline-app/60"></div>
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold text-coral transition-colors hover:bg-blush/20"
                                    @click="profileOpen = false"
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

        <!-- Flash -->
        <main class="mx-auto max-w-lg px-5 pt-4 pb-6">
            <div v-if="flash?.success" class="mb-4 rounded-2xl bg-sage/50 p-4 text-sm font-semibold text-green-800 animate-fade-up">
                {{ flash.success }}
            </div>
            <div v-if="flash?.error" class="mb-4 rounded-2xl bg-blush/40 p-4 text-sm font-semibold text-coral-deep animate-fade-up">
                {{ flash.error }}
            </div>

            <slot />
        </main>

        <!-- Bottom Tab Bar -->
        <nav class="fixed inset-x-0 bottom-0 z-50 px-4 pb-[calc(1rem+env(safe-area-inset-bottom))]">
            <div class="mx-auto flex max-w-lg items-center justify-around rounded-2xl border border-outline-app/70 bg-white p-1.5 shadow-card">
                <Link
                    v-for="tab in tabsLeft"
                    :key="tab.href"
                    :href="tab.href"
                    class="flex flex-1 flex-col items-center gap-0.5 rounded-xl py-1.5 transition-all duration-200 active:scale-95"
                    :class="isActive(tab) ? 'text-coral' : 'text-muted-app'"
                >
                    <component :is="tab.icon" class="size-5 transition-transform duration-200" :class="isActive(tab) ? 'scale-110' : ''" />
                    <span class="text-[10px] font-semibold">{{ tab.label }}</span>
                </Link>
                <Link
                    v-if="isFaceLocation"
                    href="/face-attendance"
                    class="flex flex-1 flex-col items-center gap-0.5 rounded-xl py-1 transition-all duration-200 active:scale-95"
                    :class="page.url.startsWith('/face-attendance') ? 'text-coral' : 'text-muted-app'"
                >
                    <div class="-mt-5 flex size-12 items-center justify-center rounded-full bg-coral text-white shadow-card shadow-coral/30">
                        <ScanFace class="size-6" />
                    </div>
                    <span class="text-[10px] font-semibold">Absen</span>
                </Link>
                <Link
                    v-for="tab in tabsRight"
                    :key="tab.href"
                    :href="tab.href"
                    class="flex flex-1 flex-col items-center gap-0.5 rounded-xl py-1.5 transition-all duration-200 active:scale-95"
                    :class="isActive(tab) ? 'text-coral' : 'text-muted-app'"
                >
                    <component :is="tab.icon" class="size-5 transition-transform duration-200" :class="isActive(tab) ? 'scale-110' : ''" />
                    <span class="text-[10px] font-semibold">{{ tab.label }}</span>
                </Link>
            </div>
        </nav>
    </div>
</template>
