import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.PLAYWRIGHT_BASE_URL ?? process.env.APP_URL ?? 'http://x-change-sandbox.test';

export default defineConfig({
    testDir: './tests/playwright',
    timeout: 30_000,
    expect: {
        timeout: 10_000,
    },
    fullyParallel: false,
    reporter: [['list']],
    use: {
        baseURL,
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
