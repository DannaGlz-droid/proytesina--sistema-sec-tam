import { defineConfig, devices } from '@playwright/test';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const projectRoot = path.dirname(fileURLToPath(import.meta.url));
const databasePath = path.join(projectRoot, 'storage', 'app', 'e2e.sqlite');
const baseURL = 'http://127.0.0.1:8011';
const applicationEnvironment = {
    ...process.env,
    APP_ENV: 'testing',
    APP_URL: baseURL,
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: databasePath,
    SESSION_DRIVER: 'file',
    CACHE_STORE: 'file',
    QUEUE_CONNECTION: 'sync',
    MAIL_MAILER: 'array',
};

export default defineConfig({
    testDir: './tests/Browser',
    fullyParallel: false,
    workers: 1,
    timeout: 45_000,
    expect: {
        timeout: 8_000,
        toHaveScreenshot: {
            animations: 'disabled',
            caret: 'hide',
            maxDiffPixelRatio: 0.01,
        },
    },
    forbidOnly: Boolean(process.env.CI),
    retries: process.env.CI ? 1 : 0,
    reporter: [['list'], ['html', { open: 'never' }]],
    globalSetup: './tests/Browser/global-setup.js',
    globalTeardown: './tests/Browser/global-teardown.js',
    use: {
        baseURL,
        locale: 'es-MX',
        timezoneId: 'America/Monterrey',
        colorScheme: 'light',
        reducedMotion: 'reduce',
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        {
            name: 'setup',
            testMatch: /auth\.setup\.js/,
        },
        {
            name: 'chromium',
            dependencies: ['setup'],
            testIgnore: /statistics\.mobile\.spec\.js/,
            use: {
                ...devices['Desktop Chrome'],
                viewport: { width: 1920, height: 1080 },
                storageState: 'test-results/.auth/statistics-user.json',
            },
        },
        {
            name: 'mobile-chromium',
            dependencies: ['setup'],
            testMatch: /statistics\.mobile\.spec\.js/,
            use: {
                ...devices['Pixel 7'],
                storageState: 'test-results/.auth/statistics-user.json',
            },
        },
    ],
});

export { applicationEnvironment, baseURL, databasePath };
