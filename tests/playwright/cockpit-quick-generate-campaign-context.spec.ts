import { execFileSync } from 'node:child_process';
import { expect, Page, test } from '@playwright/test';

const email = 'playwright-cockpit-campaign@example.test';
const mobile = '639170000035';
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
            "'name' => 'Playwright Cockpit Campaign Operator',",
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

test('quick generate renders campaign context prefill and submits safe campaign metadata', async ({ page }) => {
    await login(page);

    let submittedPayload: Record<string, unknown> | null = null;

    await page.route('**/x/cockpit/quick-generate**', async (route) => {
        if (route.request().method() !== 'POST') {
            await route.continue();

            return;
        }

        submittedPayload = route.request().postDataJSON() as Record<string, unknown>;

        await route.fulfill({
            status: 201,
            contentType: 'application/json',
            body: JSON.stringify({
                status: 'issued',
                result: {
                    code: 'PC-PLAYWRIGHT-35',
                    links: {
                        cockpit_detail: '/x/cockpit/pay-codes/PC-PLAYWRIGHT-35',
                    },
                },
            }),
        });
    });

    await page.goto('/x/cockpit/quick-generate?campaign_planning_key=plan-playwright-35&campaign_execution_id=exec-playwright-35&campaign_id=campaign-playwright-35&campaign_audience_id=audience-playwright-35&campaign_recipient_id=recipient-playwright-35&campaign_source=campaign_cockpit&campaign_template_key=ofw-remittance&campaign_amount=500.00&campaign_currency=PHP&campaign_recipient_reference=09173011987&campaign_purpose=Campaign%20payout');
    await expect(page.getByTestId('cockpit-quick-generate-shell')).toBeVisible();
    await expect(page.getByTestId('cockpit-quick-generate-campaign-context-panel')).toContainText('Campaign context prefill');
    await expect(page.getByTestId('cockpit-quick-generate-campaign-context-panel')).toContainText('plan-playwright-35');
    await expect(page.getByTestId('cockpit-quick-generate-campaign-context-panel')).toContainText('read-only');
    await expect(page.getByTestId('cockpit-quick-generate-submit-template')).toHaveValue('ofw-remittance');
    await expect(page.getByTestId('cockpit-quick-generate-submit-amount')).toHaveValue(/500/);
    await expect(page.getByTestId('cockpit-quick-generate-submit-recipient')).toHaveValue('09173011987');
    await expect(page.getByTestId('cockpit-quick-generate-submit-purpose')).toHaveValue('Campaign payout');

    await page.getByTestId('cockpit-quick-generate-submit-button').click();

    await expect(page.getByTestId('cockpit-quick-generate-result-panel')).toContainText('Generated Pay Code: PC-PLAYWRIGHT-35');
    expect(submittedPayload).not.toBeNull();
    expect(submittedPayload).toMatchObject({
        metadata: {
            campaign: {
                planning_key: 'plan-playwright-35',
                execution_id: 'exec-playwright-35',
                campaign_id: 'campaign-playwright-35',
                audience_id: 'audience-playwright-35',
                recipient_id: 'recipient-playwright-35',
                source: 'campaign_cockpit',
                read_only: true,
                mutates_campaign: false,
            },
            custom: {
                cockpit: {
                    template_key: 'ofw-remittance',
                    source: 'cockpit.quick-generate',
                    campaign_context: 'read-model-prefill',
                },
            },
        },
    });
    expect(JSON.stringify(submittedPayload)).not.toContain('campaign_payload');
    expect(JSON.stringify(submittedPayload)).not.toContain('provider_payload');
    expect(JSON.stringify(submittedPayload)).not.toContain('wallet');
});
