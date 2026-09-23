import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/site/app.ts',
                'resources/js/admin/app.ts',
                'resources/css/site.css',
                'resources/css/admin.css',
            ],
            refresh: [
                'resources/js/**',
                'resources/css/**',
                'resources/views/**',
                'routes/**',
            ],
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
            '@site': resolve(__dirname, 'resources/js/site'),
            '@admin': resolve(__dirname, 'resources/js/admin'),
            '@shared': resolve(__dirname, 'resources/js/shared'),
            '@brand': resolve(__dirname, 'resources/images/brand'),
            // Ziggy ставится композером, npm-пакет дублировал бы список маршрутов
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    server: {
        // Без явного адреса Vite пишет в public/hot ссылку вида http://[::1]:5173,
        // и часть окружений её не резолвит. Фиксируем IPv4.
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
