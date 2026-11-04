import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/inertia.js',
                'resources/js/checkout/index.js',
                'resources/js/dashboard/index.js',
                'resources/js/admin/products/form.js',
                'resources/js/products/show.js',
                'resources/js/catalog/index.js',
                'resources/js/home/catalog.js',
                'resources/js/payments/pix/index.js',
                'resources/js/payments/boleto/index.js',
            ],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
