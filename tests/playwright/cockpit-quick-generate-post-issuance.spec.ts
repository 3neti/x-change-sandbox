import { execFileSync } from 'node:child_process';
import { expect, Page, test } from '@playwright/test';

const email = 'playwright-cockpit@example.test';
const mobile = '639170000004';
const password = 'password';

test.beforeAll(() => {
    execFileSync(
        'php',
        [
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
        ],
        {
            stdio: 'inherit',
        },
    );
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

test('quick generate renders post issuance detail and distribution handoff links from operator safe response', async ({
    page,
}) => {
    await login(page);

    let submittedPayload: Record<string, unknown> | null = null;

    await page.route('**/x/cockpit/quick-generate', async (route) => {
        if (route.request().method() !== 'POST') {
            await route.continue();

            return;
        }

        submittedPayload = route.request().postDataJSON() as Record<
            string,
            unknown
        >;

        await route.fulfill({
            status: 201,
            contentType: 'application/json',
            body: JSON.stringify({
                status: 'issued',
                result: {
                    code: 'PC-PLAYWRIGHT-34',
                    links: {
                        cockpit_detail: '/x/cockpit/pay-codes/PC-PLAYWRIGHT-34',
                        cockpit_distribution:
                            '/x/cockpit/pay-codes/PC-PLAYWRIGHT-34/distribution',
                    },
                },
                post_issuance_navigation: {
                    schema: 'x-change.cockpit.quick-generate-post-issuance-navigation.v1',
                    status: 'available',
                    auto_redirect: false,
                    items: [
                        {
                            key: 'detail',
                            label: 'Open Cockpit detail',
                            href: '/x/cockpit/pay-codes/PC-PLAYWRIGHT-34',
                            status: 'available',
                            enabled: true,
                            read_only: true,
                        },
                        {
                            key: 'distribution',
                            label: 'Open Distribution workspace',
                            href: '/x/cockpit/pay-codes/PC-PLAYWRIGHT-34/distribution',
                            status: 'available',
                            enabled: true,
                            read_only: true,
                        },
                    ],
                    redactions: {
                        payloads: 'post-issuance-navigation-only',
                    },
                },
                draft: {
                    status: 'compiled',
                    factory: 'CockpitQuickGenerateDraftFactoryContract',
                    compiler: 'CockpitIssuanceDraftCompilerContract',
                },
                preflight: {
                    pricing: {
                        status: 'estimated',
                        currency: 'PHP',
                        total: 25,
                        base_fee: 0,
                        blocking: false,
                    },
                    funding: {
                        status: 'checked',
                        authority: 'local_ledger',
                        sync_status: 'fresh',
                        authoritative: {
                            currency: 'PHP',
                            balance: 10000,
                        },
                    },
                },
                activity: {
                    schema: 'x-change.cockpit.operator-issuance-activity.v1',
                    status: 'recording-attempted-after-issuance',
                    presentation_only: true,
                },
            }),
        });
    });

    await page.goto('/x/cockpit/quick-generate');
    await expect(
        page.getByTestId('cockpit-quick-generate-shell'),
    ).toBeVisible();
    await expect(
        page.getByTestId('cockpit-voucher-instruction-builder'),
    ).toContainText('Money and quantity');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-builder'),
    ).toContainText('Claim inputs');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-builder'),
    ).toContainText('Validation and verification');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-builder'),
    ).toContainText('Feedback channels');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-summary'),
    ).toContainText('Pay Code contract summary');
    await page.getByTestId('cockpit-quick-generate-submit-count').fill('2');
    await page
        .getByTestId('cockpit-quick-generate-validation-secret')
        .fill('branch-pin');
    await expect(
        page.getByTestId('cockpit-quick-generate-engineering-preview-json'),
    ).not.toBeVisible();
    await page
        .getByText('Engineering Preview — sanitized instruction payload')
        .click();
    await expect(
        page.getByTestId('cockpit-quick-generate-engineering-preview-json'),
    ).toContainText('"cash"');
    await expect(
        page.getByTestId('cockpit-quick-generate-engineering-preview-json'),
    ).toContainText('[redacted secret]');
    await expect(
        page.getByTestId('cockpit-quick-generate-engineering-preview-json'),
    ).not.toContainText('branch-pin');

    await page.getByTestId('cockpit-quick-generate-submit-button').click();

    await expect(
        page.getByTestId('cockpit-quick-generate-result-panel'),
    ).toContainText('Generated Pay Code: PC-PLAYWRIGHT-34');
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-post-issuance-navigation-panel',
        ),
    ).toContainText('Post-issuance handoff');
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-post-issuance-navigation-panel',
        ),
    ).toContainText('Automatic redirect: disabled');
    await expect(
        page.getByTestId('cockpit-quick-generate-post-issuance-link-detail'),
    ).toHaveAttribute('href', '/x/cockpit/pay-codes/PC-PLAYWRIGHT-34');
    await expect(
        page.getByTestId('cockpit-quick-generate-post-issuance-link-detail'),
    ).toContainText('read-only');
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-post-issuance-link-distribution',
        ),
    ).toHaveAttribute(
        'href',
        '/x/cockpit/pay-codes/PC-PLAYWRIGHT-34/distribution',
    );
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-post-issuance-link-distribution',
        ),
    ).toContainText('read-only');
    await expect(
        page.getByTestId('cockpit-quick-generate-result-panel'),
    ).not.toContainText('provider_payload');
    await expect(
        page.getByTestId('cockpit-quick-generate-result-panel'),
    ).not.toContainText('raw_payload');
    await expect(
        page.getByTestId('cockpit-quick-generate-result-panel'),
    ).not.toContainText('wallet');
    expect(submittedPayload).toMatchObject({
        cash: {
            validation: {
                secret: 'branch-pin',
            },
        },
        inputs: {
            fields: ['mobile'],
        },
        count: 2,
        metadata: {
            custom: {
                cockpit: {
                    builder: 'guided-voucher-instruction-builder',
                },
            },
        },
    });
});
