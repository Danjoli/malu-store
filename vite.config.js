import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/checkout/index.js',
                'resources/js/dashboard/index.js',
                'resources/js/admin/products/form.js',
                'resources/js/products/show.js',
                'resources/js/payments/card/index.js',
                'resources/js/payments/pix/index.js',
                'resources/js/payments/boleto/index.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
