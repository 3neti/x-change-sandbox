import { execFileSync } from 'node:child_process';
import { expect, test } from '@playwright/test';

const email = 'playwright-cockpit@example.test';
const mobile = '639170000004';
const password = 'password';

test.beforeAll(() => {
    execFileSync('php', [
        'artisan',
        'tinker',
        '--execute',
        [
            'App\\Models\\User::query()->updateOrCreate(',
            `['email' => '${email}'],`,
            '[',
            "'name' => 'Playwright Cockpit Operator',",
            `'mobile' => '${mobile}',`,
            "'password' => Illuminate\\Support\\Facades\\Hash::make('password'),",
            ']',
            ');',
        ].join(' '),
    ], {
        stdio: 'inherit',
    });
});

test('cockpit pay code explorer filters render through Playwright without ChromeDriver', async ({ page }) => {
    await page.goto('/login');

    await page.getByLabel(/mobile number/i).fill(mobile);
    await page.getByLabel(/pin/i).fill(password);
    await page.getByRole('button', { name: /log in/i }).click();
    await page.waitForLoadState('networkidle');

    await page.goto('/x/cockpit/pay-codes?search=PC-PLAYWRIGHT&status=redeemed');

    await expect(page).toHaveURL(/\/x\/cockpit\/pay-codes\?search=PC-PLAYWRIGHT&status=redeemed/);
    await expect(page.getByTestId('cockpit-pay-code-explorer-shell')).toBeVisible();
    await expect(page.getByTestId('cockpit-pay-code-stats-summary')).toContainText('Functional parity summary');
    await expect(page.getByTestId('cockpit-pay-code-search-input')).toHaveValue('PC-PLAYWRIGHT');
    await expect(page.getByTestId('cockpit-pay-code-status-filter')).toHaveValue('redeemed');
    await expect(page.getByTestId('cockpit-pay-code-active-filter-summary')).toContainText('Filters: search “PC-PLAYWRIGHT” · status redeemed');
    await expect(page.getByTestId('cockpit-pay-code-clear-filters')).toHaveAttribute('href', '/x/cockpit/pay-codes');
    await expect(page.getByText('Filters use read-only GET navigation.')).toBeVisible();

    await expect(page.getByText('Save configuration')).toHaveCount(0);
    await expect(page.getByText('Enable handoffs')).toHaveCount(0);
    await expect(page.getByText('provider_payload')).toHaveCount(0);
    await expect(page.getByText('raw_payload')).toHaveCount(0);
    await expect(page.getByText('wallet')).toHaveCount(0);
});
