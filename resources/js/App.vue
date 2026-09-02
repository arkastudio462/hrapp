<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const tenant = computed(() => page.props.tenant);
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
                            >
                                Dashboard
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <template v-if="user">
                            <span class="text-sm text-muted-foreground">{{ user.name }}</span>
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                            >
                                Logout
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                href="/login"
                                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                            >
                                Login
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
