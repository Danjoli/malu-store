import { defineConfig, devices } from '@playwright/test';
import path from 'node:path';

const database = path.resolve('database/e2e.sqlite');

export default defineConfig({
    testDir: './tests/Browser',
    fullyParallel: false,
    workers: 1,
    retries: process.env.CI ? 1 : 0,
    reporter: process.env.CI ? [['github'], ['html', { open: 'never' }]] : 'list',
    use: {
        baseURL: 'http://127.0.0.1:8000',
        screenshot: 'only-on-failure',
        trace: 'retain-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
    webServer: {
        command: 'php -r "file_exists(\'database/e2e.sqlite\') || touch(\'database/e2e.sqlite\');" && php artisan migrate:fresh --seed --force && php artisan serve --host=127.0.0.1 --port=8000',
        url: 'http://127.0.0.1:8000/up',
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
        env: {
            ...process.env,
            APP_ENV: 'testing',
            APP_DEBUG: 'false',
            APP_KEY: 'base64:VGVzdE9ubHlLZXlGb3JCcm93c2VyVGVzdHMxMjM0NTY=',
            APP_URL: 'http://127.0.0.1:8000',
            DB_CONNECTION: 'sqlite',
            DB_DATABASE: database,
            CACHE_STORE: 'file',
            SESSION_DRIVER: 'file',
            QUEUE_CONNECTION: 'sync',
            MAIL_MAILER: 'array',
            ASAAS_ENV: 'sandbox',
            ASAAS_SANDBOX_API_KEY: 'browser-test-key',
            MELHOR_ENVIO_ENV: 'sandbox',
            MELHOR_ENVIO_SANDBOX_TOKEN: 'browser-test-token',
        },
    },
});
