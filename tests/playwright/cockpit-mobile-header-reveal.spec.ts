import { execFileSync } from 'node:child_process';
import { expect, Page, test } from '@playwright/test';

const email = 'playwright-cockpit-mobile-shell@example.test';
const mobile = '639170000084';
const password = 'password';

test.beforeAll(() => {
    execFileSync(
        'php',
        [
            'artisan',
            'tinker',
            '--execute',
            [
                '$user = App\\Models\\User::query()->updateOrCreate(',
                `['email' => '${email}'],`,
                '[',
                "'name' => 'Playwright Cockpit Mobile Operator',",
                `'mobile' => '${mobile}',`,
                "'password' => Illuminate\\Support\\Facades\\Hash::make('password'),",
                ']',
                ');',
                '$agreements = app(LBHurtado\\XChange\\Services\\Legal\\CurrentAgreementService::class);',
                'if ($agreements->enabled()) {',
                '$request = Illuminate\\Http\\Request::create("/x/legal/eula/accept", "POST");',
                '$request->setLaravelSession(app("session")->driver());',
                '$agreements->accept(',
                '$user,',
                '$request,',
                '$agreements->document(),',
                ');',
                '}',
            ].join(' '),
        ],
        {
            stdio: 'inherit',
        },
    );
});

async function login(page: Page): Promise<void> {
    await page.goto('/login');
    await page.getByLabel(/mobile number/i).fill(mobile);
    await page.getByLabel(/pin/i).fill(password);
    await page.getByRole('button', { name: /log in/i }).click();
    await page.waitForLoadState('networkidle');

    await expect(page).not.toHaveURL(/\/login/);
}

async function dispatchTouchGesture(
    page: Page,
    startY: number,
    endY: number,
): Promise<void> {
    await page.evaluate(
        ({ startY, endY }) => {
            const touch = (clientY: number): Touch =>
                new Touch({
                    identifier: 1,
                    target: document.body,
                    clientX: 20,
                    clientY,
                });

            window.dispatchEvent(
                new TouchEvent('touchstart', {
                    bubbles: true,
                    touches: [touch(startY)],
                }),
            );
            window.dispatchEvent(
                new TouchEvent('touchmove', {
                    bubbles: true,
                    touches: [touch(endY)],
                }),
            );
        },
        { startY, endY },
    );
}

test('mobile cockpit header stays hidden until a pull from the top', async ({
    page,
}) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await login(page);
    await page.goto('/x/cockpit/quick-generate');

    const revealedHeader = page.getByTestId('cockpit-mobile-revealed-header');

    await expect(revealedHeader).toHaveCount(0);
    await expect(
        page.getByRole('button', { name: 'Toggle sidebar' }),
    ).toHaveCount(0);

    await dispatchTouchGesture(page, 20, 100);

    await expect(revealedHeader).toBeVisible();
    await expect(
        revealedHeader.getByRole('button', { name: 'Toggle sidebar' }),
    ).toBeVisible();

    await dispatchTouchGesture(page, 100, 20);
    await expect(revealedHeader).toHaveCount(0);
});

test('desktop cockpit header remains visible', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 900 });
    await login(page);
    await page.goto('/x/cockpit/quick-generate');

    await expect(
        page.getByRole('button', { name: 'Toggle sidebar' }),
    ).toBeVisible();
    await expect(
        page.getByTestId('cockpit-mobile-revealed-header'),
    ).toHaveCount(0);
});
