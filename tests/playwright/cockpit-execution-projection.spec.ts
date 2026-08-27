import { execFileSync } from 'node:child_process';
import type { Page} from '@playwright/test';
import { expect, test } from '@playwright/test';

const email = 'playwright-cockpit-execution-projection@example.test';
const mobile = '639170000078';
const password = 'password';
const code = 'PC-PROJECTION';
const executionId = 'exec-playwright-projection';
const correlationId = 'corr-playwright-projection';

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
                "'name' => 'Playwright Cockpit Execution Projection Operator',",
                `'mobile' => '${mobile}',`,
                "'password' => Illuminate\\Support\\Facades\\Hash::make('password'),",
                ']',
                ');',
                "$resultOccurredAt = LBHurtado\\XJournal\\Models\\ExecutionJournalEntry::query()->where('idempotency_key', 'playwright:cockpit-projection:result:v2')->value('occurred_at') ?? now()->subSecond()->startOfSecond();",
                'app(LBHurtado\\XJournal\\Services\\ExecutionJournalRecorder::class)->record(',
                'LBHurtado\\XJournal\\Data\\ExecutionJournalEntryData::fromArray([',
                "'reference_number' => 'EJR-PLAYWRIGHT-PROJECTION-RESULT-V2',",
                "'event_type' => 'execution.result.recorded',",
                "'occurred_at' => $resultOccurredAt,",
                "'actor' => ['type' => 'operator', 'id' => (string) $user->getKey()],",
                "'subject' => ['type' => 'voucher', 'id' => 'PC-PROJECTION'],",
                "'money' => ['amount' => '25.00', 'currency' => 'PHP'],",
                `'references' => ['correlation_id' => '${correlationId}', 'execution_id' => '${executionId}', 'voucher_code' => '${code}'],`,
                `'payload' => ['execution_id' => '${executionId}', 'voucher_code' => '${code}', 'status' => 'succeeded', 'driver' => 'settlement_envelope'],`,
                "'metadata' => ['driver' => 'settlement_envelope', 'source' => 'playwright'],",
                "'idempotency_key' => 'playwright:cockpit-projection:result:v2',",
                ']));',
                "$summaryOccurredAt = LBHurtado\\XJournal\\Models\\ExecutionJournalEntry::query()->where('idempotency_key', 'playwright:cockpit-projection:summary:v2')->value('occurred_at') ?? $resultOccurredAt->addSecond();",
                'app(LBHurtado\\XJournal\\Services\\ExecutionJournalRecorder::class)->record(',
                'LBHurtado\\XJournal\\Data\\ExecutionJournalEntryData::fromArray([',
                "'reference_number' => 'EJR-PLAYWRIGHT-PROJECTION-SUMMARY-V2',",
                "'event_type' => 'execution.handoff.summary.recorded',",
                "'occurred_at' => $summaryOccurredAt,",
                "'actor' => ['type' => 'system', 'id' => 'x-change'],",
                "'subject' => ['type' => 'voucher', 'id' => 'PC-PROJECTION'],",
                "'money' => null,",
                `'references' => ['correlation_id' => '${correlationId}', 'execution_id' => '${executionId}', 'voucher_code' => '${code}'],`,
                "'payload' => [",
                `'execution_id' => '${executionId}',`,
                `'voucher_code' => '${code}',`,
                "'profile' => [",
                "'targets' => ['journal' => 'recorded', 'action' => 'composed', 'feedback' => 'planned', 'cockpit_activity' => 'not_wired'],",
                "'active_targets' => ['journal', 'action', 'feedback'],",
                "'performed_side_effect_targets' => ['journal'],",
                "'failed_targets' => [],",
                "'non_blocking' => true,",
                '],',
                '],',
                "'metadata' => ['source' => 'playwright'],",
                "'idempotency_key' => 'playwright:cockpit-projection:summary:v2',",
                ']));',
            ].join(' '),
        ],
        {
            env: {
                ...process.env,
                HOME: '/tmp',
            },
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

test('cockpit dashboard shows durable execution projection evidence from published assets', async ({
    page,
}) => {
    await login(page);

    await page.goto('/x/cockpit');

    const activity = page
        .getByTestId('cockpit-activity-item')
        .filter({ hasText: executionId })
        .first();
    const projection = activity.getByTestId(
        'cockpit-activity-projection-status',
    );

    await expect(activity).toContainText(`Execution recorded for ${code}`);
    await expect(projection).toContainText('Durable summary evidence');
    await expect(projection).toContainText(
        'durable_summary_evidence_available',
    );
    await expect(projection).toContainText(
        'Action and feedback statuses are projected from x-journal execution.handoff.summary.recorded.',
    );
    await expect(projection).toContainText(
        'Targets: journal, action, feedback, handoff_summary_journal',
    );
    await expect(activity).not.toContainText('provider_payload');
    await expect(activity).not.toContainText('raw_payload');
    await expect(activity).not.toContainText('wallet');
});
