import { execFileSync } from 'node:child_process';
import { expect, Page, test } from '@playwright/test';

const email = 'playwright-cockpit-campaign-activity@example.test';
const mobile = '639170000044';
const password = 'password';
const code = 'PC-PLAYWRIGHT-44';

test.beforeAll(() => {
    execFileSync('php', [
        'artisan',
        'tinker',
        '--execute',
        [
            '$user = App\\Models\\User::query()->updateOrCreate(',
            `['email' => '${email}'],`,
            '[',
            "'name' => 'Playwright Cockpit Campaign Activity Operator',",
            `'mobile' => '${mobile}',`,
            "'password' => Illuminate\\Support\\Facades\\Hash::make('password'),",
            ']',
            ');',
            'LBHurtado\\XChange\\Models\\CockpitOperatorIssuanceActivity::query()->updateOrCreate(',
            "['activity_id' => 'playwright-wave-44-campaign-activity'],",
            '[',
            "'actor_id' => (string) $user->getKey(),",
            "'source' => 'cockpit.quick-generate',",
            "'subject_type' => 'pay_code',",
            `'subject_reference' => '${code}',`,
            "'status' => 'issued',",
            "'severity' => 'info',",
            "'occurred_at' => now(),",
            "'correlation_id' => 'corr-playwright-wave-44',",
            "'summary' => 'Playwright Wave 44 campaign activity',",
            "'safe_context' => [",
            `'code' => '${code}',`,
            "'amount' => '500.00',",
            "'currency' => 'PHP',",
            "'route' => 'x-change.cockpit.quick-generate.store',",
            `'detail_href' => '/x/cockpit/pay-codes/${code}',`,
            '],',
            "'journal_handoff_status' => 'not_wired',",
            "'action_handoff_status' => 'not_wired',",
            "'feedback_handoff_status' => 'not_wired',",
            "'metadata' => [",
            "'campaign_attribution' => [",
            "'schema' => 'x-change.cockpit.quick-generate-campaign-attribution.v1',",
            "'status' => 'available',",
            "'read_only' => true,",
            "'mutates_campaign' => false,",
            "'planning_key' => 'plan-playwright-44',",
            "'execution_id' => 'exec-playwright-44',",
            "'campaign_id' => 'campaign-playwright-44',",
            "'audience_id' => 'audience-playwright-44',",
            "'recipient_id' => 'recipient-playwright-44',",
            "'source' => 'x_campaign_adapter',",
            `'generated_code' => '${code}',`,
            "'template_key' => 'ofw-remittance',",
            "'amount' => '500.00',",
            "'currency' => 'PHP',",
            "'recipient_reference' => '09173011987',",
            "'purpose' => 'Campaign payout',",
            '],',
            '],',
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

test('dashboard campaign activity card preserves recipient context in read-only navigation links', async ({ page }) => {
    await login(page);

    await page.goto(`/x/cockpit?activity_search=${code}`);

    const card = page.getByTestId('cockpit-operator-issuance-activity-card').filter({ hasText: code }).first();
    const detailLink = card.getByTestId('cockpit-operator-issuance-activity-link');
    const distributionLink = card.getByTestId('cockpit-operator-issuance-activity-distribution-link');
    const explorerLink = card.getByTestId('cockpit-operator-issuance-activity-explorer-link');
    const campaignDashboardLink = card.getByTestId('cockpit-operator-issuance-activity-campaign-dashboard-link');

    await expect(card).toContainText('Campaign attribution');
    await expect(card).toContainText('Recipient: recipient-playwright-44');
    await expect(card).toContainText('Recipient reference: 09173011987');
    await expect(detailLink).toContainText('campaign context');
    await expect(detailLink).toContainText('read-only');
    await expect(detailLink).toHaveAttribute('href', /\/x\/cockpit\/pay-codes\/PC-PLAYWRIGHT-44\?/);
    await expect(detailLink).toHaveAttribute('href', /campaign_recipient_id=recipient-playwright-44/);
    await expect(distributionLink).toContainText('campaign context');
    await expect(distributionLink).toContainText('read-only');
    await expect(distributionLink).toHaveAttribute('href', /\/x\/cockpit\/pay-codes\/PC-PLAYWRIGHT-44\/distribution\?/);
    await expect(distributionLink).toHaveAttribute('href', /campaign_recipient_id=recipient-playwright-44/);
    await expect(explorerLink).toContainText('campaign context');
    await expect(explorerLink).toHaveAttribute('href', /activity_code=PC-PLAYWRIGHT-44/);
    await expect(explorerLink).toHaveAttribute('href', /campaign_recipient_id=recipient-playwright-44/);
    await expect(campaignDashboardLink).toContainText('read-only');
    await expect(campaignDashboardLink).toHaveAttribute('href', /campaign_planning_key=plan-playwright-44/);
    await expect(campaignDashboardLink).toHaveAttribute('href', /campaign_recipient_id=recipient-playwright-44/);
    await expect(card).not.toContainText('provider_payload');
    await expect(card).not.toContainText('raw_payload');
    await expect(card).not.toContainText('wallet');
});
