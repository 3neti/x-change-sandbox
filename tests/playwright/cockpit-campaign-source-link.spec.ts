import { execFileSync } from 'node:child_process';
import { expect, Page, test } from '@playwright/test';

const email = 'playwright-cockpit-campaign-source@example.test';
const mobile = '639170000037';
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
            "'name' => 'Playwright Cockpit Campaign Source Operator',",
            `'mobile' => '${mobile}',`,
            "'password' => Illuminate\\Support\\Facades\\Hash::make('password'),",
            ']',
            ');',
        ].join(' '),
    ], {
        stdio: 'inherit',
    });
});

async function login(page: Page): Promise<void> {
    for (let attempt = 0; attempt < 2; attempt += 1) {
        await page.goto('/login');
        await page.getByLabel(/mobile number/i).fill(mobile);
        await page.getByLabel(/pin/i).fill(password);
        await page.getByRole('button', { name: /log in/i }).click();
        await page.waitForLoadState('networkidle');

        if (!page.url().includes('/login')) {
            return;
        }
    }

    await expect(page).not.toHaveURL(/\/login/);
}

test('dashboard campaign context source link opens quick generate with prefilled campaign values', async ({ page }) => {
    await login(page);

    await page.goto('/x/cockpit?campaign_planning_key=plan-playwright-37&campaign_execution_id=exec-playwright-37&campaign_id=campaign-playwright-37&campaign_audience_id=audience-playwright-37&campaign_recipient_id=recipient-playwright-37&campaign_source=campaign_cockpit&campaign_template_key=ofw-remittance&campaign_amount=500.00&campaign_currency=PHP&campaign_recipient_reference=09173011987&campaign_purpose=Campaign%20payout');

    const quickGenerateSourceLink = page.getByTestId('cockpit-campaign-quick-generate-link');

    await expect(page.getByTestId('cockpit-campaign-adoption-panel')).toContainText('Campaign Cockpit Adoption');
    await expect(quickGenerateSourceLink).toBeVisible();
    await expect(quickGenerateSourceLink).toContainText('Open Quick Generate');
    await expect(quickGenerateSourceLink).toContainText('read-only campaign context');
    await expect(quickGenerateSourceLink).toHaveAttribute('href', /\/x\/cockpit\/quick-generate/);
    await expect(quickGenerateSourceLink).toHaveAttribute('href', /campaign_planning_key=plan-playwright-37/);
    await expect(quickGenerateSourceLink).toHaveAttribute('href', /campaign_template_key=ofw-remittance/);
    await expect(quickGenerateSourceLink).toHaveAttribute('href', /campaign_recipient_reference=09173011987/);

    await quickGenerateSourceLink.click();

    await expect(page).toHaveURL(/\/x\/cockpit\/quick-generate/);
    await expect(page.getByTestId('cockpit-quick-generate-campaign-context-panel')).toContainText('Campaign context prefill');
    await expect(page.getByTestId('cockpit-quick-generate-campaign-context-panel')).toContainText('plan-playwright-37');
    await expect(page.getByTestId('cockpit-quick-generate-campaign-context-panel')).toContainText('read-only');
    await expect(page.getByTestId('cockpit-quick-generate-submit-template')).toHaveValue('ofw-remittance');
    await expect(page.getByTestId('cockpit-quick-generate-submit-amount')).toHaveValue(/500/);
    await expect(page.getByTestId('cockpit-quick-generate-submit-recipient')).toHaveValue('09173011987');
    await expect(page.getByTestId('cockpit-quick-generate-submit-purpose')).toHaveValue('Campaign payout');
});
