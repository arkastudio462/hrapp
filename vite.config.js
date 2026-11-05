import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    // Pre-bundle every runtime dependency up front. Discovering a new
    // dependency mid-session bumps the optimizeDeps hash, so app.js and freshly
    // transformed page modules end up importing two copies of @inertiajs/vue3
    // ("headManager.createProvider is not a function" on navigation).
    optimizeDeps: {
        include: [
            'vue',
            '@inertiajs/core',
            '@inertiajs/vue3',
            'face-api.js',
            'lucide-vue-next',
            'chart.js',
            'vue-chartjs',
            'leaflet',
            '@vue-leaflet/vue-leaflet',
            'radix-vue',
            'ziggy-js',
            'clsx',
            'tailwind-merge',
            'class-variance-authority',
            'laravel-vite-plugin/inertia-helpers',
        ],
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
