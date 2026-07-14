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
    ).toContainText('Money, payee, and expiry');
    await page
        .getByTestId('cockpit-quick-generate-submit-template')
        .selectOption('ofw-remittance');
    await expect(
        page.getByTestId('cockpit-quick-generate-submit-amount'),
    ).toHaveValue('500');
    await expect(
        page.getByTestId('cockpit-quick-generate-submit-recipient'),
    ).toHaveValue('09170000000');
    await expect(
        page.getByTestId('cockpit-quick-generate-submit-purpose'),
    ).toHaveValue('OFW remittance payout');
    await expect(
        page.getByTestId('cockpit-quick-generate-expiry-preset'),
    ).toHaveValue('P3D');
    await expect(
        page.getByTestId('cockpit-quick-generate-payee-help'),
    ).toContainText('Restricted to mobile number: 09170000000');
    await page
        .getByTestId('cockpit-quick-generate-submit-recipient')
        .fill('09170000000');
    await expect(
        page.getByTestId('cockpit-quick-generate-payee-help'),
    ).toContainText('Restricted to mobile number: 09170000000');
    await page
        .getByTestId('cockpit-quick-generate-expiry-preset')
        .selectOption('P3D');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-builder'),
    ).toContainText('Claim inputs');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('Reference Code');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).not.toContainText('Recipient mobile/reference');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('Signature');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('KYC');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('Address');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('Birthdate');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('Gross Monthly Income');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('Location');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('OTP');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('Selfie Photo');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('Mobile Number');
    await expect(
        page.getByTestId('cockpit-quick-generate-input-fields'),
    ).toContainText('Email Address');
    const claimInputsText =
        (await page
            .getByTestId('cockpit-quick-generate-input-fields')
            .textContent()) ?? '';
    expect(claimInputsText.indexOf('Signature')).toBeLessThan(
        claimInputsText.indexOf('Selfie Photo'),
    );
    expect(claimInputsText.indexOf('Selfie Photo')).toBeLessThan(
        claimInputsText.indexOf('Location'),
    );
    expect(claimInputsText.indexOf('Location')).toBeLessThan(
        claimInputsText.indexOf('OTP'),
    );
    expect(claimInputsText.indexOf('OTP')).toBeLessThan(
        claimInputsText.indexOf('KYC'),
    );
    expect(claimInputsText.indexOf('KYC')).toBeLessThan(
        claimInputsText.indexOf('Reference Code'),
    );
    expect(claimInputsText.indexOf('Reference Code')).toBeLessThan(
        claimInputsText.indexOf('Full Name'),
    );
    await page.getByLabel(/Reference Code/).check();
    await expect(
        page.getByTestId('cockpit-voucher-instruction-builder'),
    ).toContainText('Validation and verification');
    await expect(
        page.getByTestId('cockpit-quick-generate-payee-interpretation'),
    ).toContainText('cash.validation.mobile');
    await expect(
        page.getByTestId('cockpit-quick-generate-recipient-match-group'),
    ).toContainText('Recipient Match');
    await expect(
        page.getByTestId('cockpit-quick-generate-recipient-match-group'),
    ).toContainText('Match Mobile Number');
    await expect(
        page.getByTestId('cockpit-quick-generate-recipient-match-group'),
    ).toContainText('Require Payable / Vendor Alias');
    await expect(
        page.getByTestId('cockpit-quick-generate-secret-group'),
    ).toContainText('Claim Secret / Branch PIN');
    await expect(
        page.getByTestId('cockpit-quick-generate-evidence-required-group'),
    ).toContainText('Evidence Required');
    await expect(
        page.getByTestId('cockpit-quick-generate-evidence-required-group'),
    ).toContainText('KYC');
    await expect(
        page.getByTestId('cockpit-quick-generate-evidence-required-group'),
    ).toContainText('OTP');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-builder'),
    ).toContainText('Feedback channels');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-coverage'),
    ).toContainText('VoucherInstruction DTO coverage');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-coverage'),
    ).toContainText('feedback.mobile');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-coverage'),
    ).toContainText('rider.splash_meta');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-coverage'),
    ).toContainText('execution.driver');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-coverage'),
    ).toContainText('metadata.collection_wallet_id');
    await expect(
        page.getByTestId('cockpit-voucher-instruction-summary'),
    ).toContainText('Pay Code contract summary');
    await page.getByText('Advanced contract controls').click();
    await expect(
        page.getByTestId('cockpit-quick-generate-effective-expiry'),
    ).toContainText('Expiry preset: P3D');
    await page.getByTestId('cockpit-quick-generate-prefix').fill('BR');
    await page.getByTestId('cockpit-quick-generate-mask').fill('****');
    await page
        .getByTestId('cockpit-quick-generate-settlement-rail')
        .selectOption('INSTAPAY');
    await page
        .getByTestId('cockpit-quick-generate-fee-strategy')
        .selectOption('include');
    await expect(
        page.getByTestId('cockpit-quick-generate-fee-preview'),
    ).toContainText('Include fee inside Pay Code amount');
    await expect(
        page.getByTestId('cockpit-quick-generate-fee-preview'),
    ).toContainText('PHP 10.00');
    await expect(
        page.getByTestId('cockpit-quick-generate-fee-preview'),
    ).toContainText('PHP 490.00');
    await expect(
        page.getByTestId('cockpit-quick-generate-fee-preview'),
    ).toContainText('PHP 500.00');
    await expect(
        page.getByTestId('cockpit-quick-generate-fee-preview'),
    ).toContainText('No pricing or provider quote service is called');
    await page
        .getByTestId('cockpit-quick-generate-cash-type')
        .selectOption('cash');
    await expect(
        page.getByTestId('cockpit-quick-generate-cash-type-helper'),
    ).toContainText('Standard claimable cash Pay Code');
    await expect(
        page.getByTestId('cockpit-quick-generate-mandate-options'),
    ).toContainText('Branch release');
    await expect(
        page.getByTestId('cockpit-quick-generate-mandates-preview-value'),
    ).not.toBeVisible();
    await page.getByText('Mandates payload preview').click();
    await expect(
        page.getByTestId('cockpit-quick-generate-mandates-preview-value'),
    ).toContainText('No mandates selected');
    await page
        .getByTestId('cockpit-quick-generate-mandate-branch-release')
        .check();
    await page
        .getByTestId('cockpit-quick-generate-mandate-counter-check')
        .check();
    await page
        .getByTestId('cockpit-quick-generate-mandates')
        .fill('manual-audit');
    await expect(
        page.getByTestId('cockpit-quick-generate-mandates-preview-value'),
    ).toContainText('branch-release, counter-check, manual-audit');
    await page.getByTestId('cockpit-quick-generate-submit-count').fill('2');
    await page
        .getByTestId('cockpit-quick-generate-validation-secret')
        .fill('branch-pin');
    await page.getByTestId('cockpit-quick-generate-signature-required').check();
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-cash-validation-preview-value',
        ),
    ).not.toBeVisible();
    await page.getByText('Validation Payload Preview').click();
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-cash-validation-preview-value',
        ),
    ).toContainText('secret configured');
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-cash-validation-preview-value',
        ),
    ).toContainText('match mobile number');
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-structured-validation-preview-value',
        ),
    ).toContainText('signature required');
    await expect(
        page.getByTestId('cockpit-quick-generate-validation-preview'),
    ).not.toContainText('branch-pin');
    await page.getByText('Advanced verification rules').click();
    await page
        .getByTestId('cockpit-quick-generate-face-match-required')
        .check();
    await page
        .getByTestId('cockpit-quick-generate-face-match-confidence')
        .fill('0.82');
    await page
        .getByTestId('cockpit-quick-generate-time-validation-enabled')
        .check();
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-structured-validation-preview-value',
        ),
    ).toContainText('face match required');
    await expect(
        page.getByTestId(
            'cockpit-quick-generate-structured-validation-preview-value',
        ),
    ).toContainText('claim time window');
    await page.getByText('Advanced rider metadata').click();
    await page
        .getByTestId('cockpit-quick-generate-rider-redirect-timeout')
        .fill('12');
    await page
        .getByTestId('cockpit-quick-generate-rider-splash-profile')
        .fill('operator-safe');
    await page
        .getByTestId('cockpit-quick-generate-rider-og-source')
        .selectOption('splash');
    await page.getByText('Settlement fields').click();
    await page
        .getByTestId('cockpit-quick-generate-voucher-type')
        .selectOption('settlement');
    await page.getByTestId('cockpit-quick-generate-target-amount').fill('100');
    await page
        .getByTestId('cockpit-quick-generate-rules-min-payment')
        .fill('10');
    await page.getByText('Execution instruction', { exact: true }).click();
    await page.getByTestId('cockpit-quick-generate-include-execution').check();
    await page
        .getByTestId('cockpit-quick-generate-execution-driver')
        .fill('default');
    await page
        .getByTestId('cockpit-quick-generate-execution-pipeline')
        .fill('validate, execute');
    await page.getByText('Metadata fields').click();
    await page
        .getByTestId('cockpit-quick-generate-metadata-flow-type')
        .fill('cockpit.quick-generate');
    await page
        .getByTestId('cockpit-quick-generate-feedback-mobile')
        .fill('09175550000');
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
            settlement_rail: 'INSTAPAY',
            fee_strategy: 'include',
            type: 'cash',
            mandates: ['branch-release', 'counter-check', 'manual-audit'],
            validation: {
                secret: 'branch-pin',
                mobile: '09170000000',
            },
        },
        inputs: {
            fields: ['mobile', 'reference_code', 'name'],
        },
        feedback: {
            mobile: '09175550000',
        },
        rider: {
            redirect_timeout: 12,
            splash_meta: {
                sanitized: true,
                html_profile: 'operator-safe',
            },
            og_source: 'splash',
        },
        validation: {
            signature: {
                required: true,
                on_failure: 'block',
            },
            face_match: {
                required: true,
                on_failure: 'block',
                min_confidence: 0.82,
            },
            time: {
                window: {
                    start_time: '09:00',
                    end_time: '17:00',
                    timezone: 'Asia/Manila',
                },
                limit_minutes: 10,
                track_duration: true,
            },
        },
        count: 2,
        prefix: 'BR',
        mask: '****',
        ttl: 'P3D',
        voucher_type: 'settlement',
        target_amount: 100,
        rules: {
            min_payment: 10,
            auto_close_on_full_payment: true,
        },
        execution: {
            schema: 'voucher.execution.v1',
            driver: 'default',
            pipeline: ['validate', 'execute'],
        },
        metadata: {
            flow_type: 'cockpit.quick-generate',
            custom: {
                cockpit: {
                    builder: 'guided-voucher-instruction-builder',
                },
            },
        },
    });
});

test('quick generate maps CreateV2-style payee values to validation semantics', async ({
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
                    code: 'PC-PLAYWRIGHT-VENDOR',
                    links: {},
                },
            }),
        });
    });

    await page.goto('/x/cockpit/quick-generate');
    await expect(
        page.getByTestId('cockpit-quick-generate-shell'),
    ).toBeVisible();

    await page
        .getByTestId('cockpit-quick-generate-submit-recipient')
        .fill('CASH');
    await expect(
        page.getByTestId('cockpit-quick-generate-payee-help'),
    ).toContainText('anyone can claim');

    await page
        .getByTestId('cockpit-quick-generate-submit-recipient')
        .fill('vendor:branch-001');
    await expect(
        page.getByTestId('cockpit-quick-generate-payee-help'),
    ).toContainText('Restricted to vendor alias: vendor:branch-001');
    await page
        .getByTestId('cockpit-quick-generate-expiry-preset')
        .selectOption('P7D');

    await page.getByTestId('cockpit-quick-generate-submit-button').click();

    await expect(
        page.getByTestId('cockpit-quick-generate-result-panel'),
    ).toContainText('Generated Pay Code: PC-PLAYWRIGHT-VENDOR');

    expect(submittedPayload).toMatchObject({
        cash: {
            validation: {
                payable: 'vendor:branch-001',
            },
        },
        ttl: 'P7D',
    });
    expect(JSON.stringify(submittedPayload)).not.toContain('provider_payload');
    expect(JSON.stringify(submittedPayload)).not.toContain('wallet');
});

test('quick generate applies expiry precedence from exact expiry to ttl override to preset', async ({
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
                    code: 'PC-PLAYWRIGHT-EXPIRY',
                    links: {},
                },
            }),
        });
    });

    await page.goto('/x/cockpit/quick-generate');
    await expect(
        page.getByTestId('cockpit-quick-generate-shell'),
    ).toBeVisible();

    await page
        .getByTestId('cockpit-quick-generate-expiry-preset')
        .selectOption('P7D');
    await page.getByText('Advanced contract controls').click();
    await expect(
        page.getByTestId('cockpit-quick-generate-effective-expiry'),
    ).toContainText('Expiry preset: P7D');

    await page.getByTestId('cockpit-quick-generate-ttl').fill('PT12H');
    await expect(
        page.getByTestId('cockpit-quick-generate-effective-expiry'),
    ).toContainText('Raw TTL override: PT12H');

    await page
        .getByTestId('cockpit-quick-generate-expires-at')
        .fill('2026-08-01T10:30');
    await expect(
        page.getByTestId('cockpit-quick-generate-effective-expiry'),
    ).toContainText('Absolute expiry: 2026-08-01T10:30');
    await expect(
        page.getByTestId('cockpit-quick-generate-effective-expiry'),
    ).toContainText('absolute_expires_at');

    await page.getByTestId('cockpit-quick-generate-submit-button').click();

    await expect(
        page.getByTestId('cockpit-quick-generate-result-panel'),
    ).toContainText('Generated Pay Code: PC-PLAYWRIGHT-EXPIRY');

    expect(submittedPayload).toMatchObject({
        expires_at: '2026-08-01T10:30',
    });
    expect(submittedPayload).not.toHaveProperty('ttl');
});
