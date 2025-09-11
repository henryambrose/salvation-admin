import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
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
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    build: {
        // Optimize build performance
        rollupOptions: {
            output: {
                // Manual chunk splitting for better caching
                manualChunks: {
                    'vendor-vue': ['vue', '@inertiajs/vue3'],
                    'vendor-ui': ['lucide-vue-next', 'reka-ui'],
                    'vendor-utils': ['axios', 'clsx', 'class-variance-authority'],
                },
            },
        },
        // Enable source maps for debugging
        sourcemap: process.env.NODE_ENV !== 'production',
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
        // Enable minification in production
        minify: process.env.NODE_ENV === 'production',
    },
    // Development server optimization
    server: {
        hmr: {
            overlay: true,
        },
        // Improve HMR performance
        watch: {
            usePolling: false,
            interval: 100,
        },
    },
    // CSS optimization
    css: {
        devSourcemap: process.env.NODE_ENV !== 'production',
    },
    // Enable esbuild optimization
    esbuild: {
        drop: process.env.NODE_ENV === 'production' ? ['console', 'debugger'] : [],
    },
});
