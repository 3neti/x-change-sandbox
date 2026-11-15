<?php

declare(strict_types=1);

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\User;
use Bavix\Wallet\Interfaces\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use LBHurtado\EmiCore\Contracts\PayoutProvider;
use LBHurtado\EmiCore\Contracts\ProviderReadinessProbe;
use LBHurtado\EmiCore\Contracts\SettlementProvider;
use LBHurtado\EmiCore\Contracts\SettlementProviderRegistryContract;
use LBHurtado\EmiCore\Data\PayoutRequestData;
use LBHurtado\EmiCore\Data\PayoutResultData;
use LBHurtado\EmiCore\Data\Providers\ProviderCapabilityManifestData;
use LBHurtado\EmiCore\Data\Providers\ProviderCapabilityReadinessData;
use LBHurtado\EmiCore\Data\Providers\ProviderReadinessRequestData;
use LBHurtado\EmiCore\Enums\PayoutStatus;
use LBHurtado\EmiCore\Enums\ProviderCapability;
use LBHurtado\EmiCore\Enums\SettlementRail;
use LBHurtado\EmiCore\Support\SettlementProviderRegistry;
use LBHurtado\Instruction\Database\Seeders\InstructionItemSeeder;
use LBHurtado\Voucher\Models\Voucher;
use LBHurtado\Wallet\Contracts\SystemUserResolverContract;
use LBHurtado\Wallet\Treasury\Contracts\TreasuryInventoryOperationContract;
use LBHurtado\Wallet\Treasury\Contracts\TreasuryPositionOperationContract;
use LBHurtado\Wallet\Treasury\Data\TreasuryInventoryData;
use LBHurtado\Wallet\Treasury\Data\TreasuryInventoryRecognitionData;
use LBHurtado\Wallet\Treasury\Data\TreasuryPositionAllocationData;
use LBHurtado\Wallet\Treasury\Data\TreasuryPositionRecognitionData;
use LBHurtado\Wallet\Treasury\Enums\TreasuryPositionPurpose;
use LBHurtado\Wallet\Treasury\Models\TreasuryPosition;
use LBHurtado\XChange\Actions\PayCode\GeneratePayCode;
use LBHurtado\XChange\Actions\Payment\CompleteVoucherCollection;
use LBHurtado\XChange\Actions\Payment\CreatePaymentAttempt;
use LBHurtado\XChange\Actions\Redemption\SubmitPayCodeClaim;
use LBHurtado\XChange\Contracts\ClaimWorkflowResolverContract;
use LBHurtado\XChange\Contracts\ProviderFundingPolicyContract;
use LBHurtado\XChange\Contracts\TreasuryAccountPortfolioProvisioningContract;
use LBHurtado\XChange\Contracts\VerifiedTreasuryFundingAllocationContract;
use LBHurtado\XChange\Data\FundingDecisionData;
use LBHurtado\XChange\Data\Payment\ConfirmedVoucherCollectionData;
use LBHurtado\XChange\Models\CommercialSale;
use LBHurtado\XChange\Models\DisbursementReconciliation;
use LBHurtado\XChange\Services\Commercial\ProvisionCommercialBaselines;
use LBHurtado\XChange\Services\Execution\ValueFlowPayoutService;
use LBHurtado\XChange\Services\SettlementCollectionGate;
use LBHurtado\XChange\Services\Treasury\SystemPrincipalProvisioningService;
use LBHurtado\XChange\Services\Treasury\TreasuryPayCodeAccountingService;
use LBHurtado\XChange\Services\Treasury\TreasuryPreflightService;
use LBHurtado\XChange\Services\Treasury\TreasuryProviderConnectionCatalog;
use LBHurtado\XChange\Services\Treasury\TreasuryProvisioningService;
use LBHurtado\XChange\Services\XRay\VoucherXRayProjectionBuilder;
use LBHurtado\XJournal\Models\ExecutionJournalEntry;

beforeEach(function (): void {
    if (app()->environment() !== 'testing' || config('database.default') !== 'sqlite'
        || DB::connection()->getDriverName() !== 'sqlite' || DB::connection()->getDatabaseName() !== ':memory:') {
        throw new RuntimeException('Settlement acceptance requires the isolated in-memory testing database.');
    }
    $this->artisan('migrate:fresh', ['--force' => true])->assertSuccessful();
    Http::preventStrayRequests();
    $provider = new class implements PayoutProvider
    {
        public ?PayoutRequestData $lastRequest = null;

        public int $disburseCallCount = 0;

        private PayoutStatus $status = PayoutStatus::COMPLETED;

        private string $provider = 'netbank';

        private ?array $metadata = null;

        public function reset(): self
        {
            $this->lastRequest = null;
            $this->disburseCallCount = 0;
            $this->status = PayoutStatus::COMPLETED;
            $this->metadata = null;

            return $this;
        }

        public function willReturnSuccessfulResult(string $provider = 'netbank'): self
        {
            $this->status = PayoutStatus::COMPLETED;
            $this->provider = $provider;
            $this->metadata = null;

            return $this;
        }

        public function willReturnFailedResult(string $provider = 'netbank', ?array $metadata = null): self
        {
            $this->status = PayoutStatus::FAILED;
            $this->provider = $provider;
            $this->metadata = $metadata;

            return $this;
        }

        public function disburse(PayoutRequestData $request): PayoutResultData
        {
            $this->lastRequest = $request;
            $this->disburseCallCount++;

            return new PayoutResultData(
                transaction_id: $this->status === PayoutStatus::FAILED ? $request->reference : 'SIMULATED-'.Str::uuid(),
                uuid: Str::uuid()->toString(),
                status: $this->status,
                provider: $this->provider,
                metadata: $this->metadata,
            );
        }

        public function checkStatus(string $transactionId): PayoutResultData
        {
            return new PayoutResultData(
                transaction_id: $transactionId,
                uuid: Str::uuid()->toString(),
                status: $this->status,
                provider: $this->provider,
            );
        }

        public function getRailFee(SettlementRail $rail): int
        {
            return match ($rail) {
                SettlementRail::INSTAPAY => 1000,
                SettlementRail::PESONET => 2500,
            };
        }
    };
    $this->app->instance('settlement.acceptance.provider', $provider);
    $this->app->instance(PayoutProvider::class, $provider);
    $this->seed(InstructionItemSeeder::class);
    config()->set('x-change.commercial.legal_trace.legal_entity_reference', 'legal-entity:x-change:test');
    config()->set('x-change.commercial.legal_trace.profile_version', 'test-v1');
    config()->set('x-change.commercial.billing.mode', 'billable');
    app(ProvisionCommercialBaselines::class)->provision('commissioning-manifest:sandbox-settlement-acceptance');
});

function settlementAcceptanceUser(int $amount = 1_000_000): User
{
    $user = User::query()->create([
        'name' => 'Test User',
        'email' => 'tester+'.Str::uuid().'@example.com',
        'password' => Hash::make('password'),
    ]);

    test()->actingAs($user);

    settlementAcceptanceWallet($user, $amount);

    return $user;
}

function settlementAcceptanceWallet(User $user, int $amount = 1_000_000): void
{
    if (! $user instanceof Wallet) {
        throw new RuntimeException('Test user must implement Bavix Wallet interface.');
    }

    $wallet = $user->wallet()->firstOrCreate([
        'slug' => 'platform',
    ], [
        'name' => 'Platform Wallet',
    ]);

    if ((int) $wallet->balance < $amount) {
        $wallet->deposit($amount - (int) $wallet->balance);
    }
}

function settlementAcceptanceTreasury(): User
{
    $system = User::query()->create([
        'name' => 'Test System Treasury',
        'email' => 'test-system-treasury+'.Str::uuid().'@example.com',
        'password' => 'not-a-login-credential',
    ]);
    config()->set('x-change.payout.system_user_column', 'email');
    config()->set('x-change.payout.system_user_id', $system->email);
    config()->set('x-change.payout.system_wallet_slug', 'platform');
    $provider = new class implements SettlementProvider
    {
        public function manifest(): ProviderCapabilityManifestData
        {
            return new ProviderCapabilityManifestData(
                provider: 'netbank',
                label: 'NetBank Test Provider',
                capabilities: [
                    ProviderCapability::ReadinessProbe,
                    ProviderCapability::BalanceRead,
                    ProviderCapability::FundingEvidenceRead,
                    ProviderCapability::FundingInstructionIssue,
                    ProviderCapability::SettlementExecution,
                ],
            );
        }
    };
    $probe = new class implements ProviderReadinessProbe
    {
        public function providerCode(): string
        {
            return 'netbank';
        }

        public function checkReadiness(
            ProviderReadinessRequestData $request,
        ): ProviderCapabilityReadinessData {
            return new ProviderCapabilityReadinessData(
                provider: $request->provider,
                connectionReference: $request->connectionReference,
                checks: collect($request->requiredCapabilities)
                    ->mapWithKeys(static fn ($capability): array => [$capability->value => true])
                    ->all(),
                issues: [],
                checkedAt: new DateTimeImmutable,
            );
        }
    };
    $systemResolver = new class($system) implements SystemUserResolverContract
    {
        public function __construct(private readonly User $system) {}

        public function resolve(): Wallet
        {
            return $this->system;
        }
    };

    config()->set('x-change.treasury.legal_entity_reference', 'legal-entity:x-change:test');
    config()->set('x-change.treasury.legal_profile', 'treasury-settlement-ph-v1');
    config()->set('x-change.treasury.legal_profile_version', '2026-07-24.1');
    config()->set('x-change.treasury.connections', [
        'netbank-primary' => [
            'provider' => 'netbank',
            'mode' => 'required',
            'currency' => 'PHP',
            'decimal_places' => 2,
            'inventory_reference' => 'inventory:netbank:vca-cash',
            'settlement_resource_reference' => 'resource:netbank:corporate-vca',
            'settlement_resource_type' => 'cash_at_bank',
            'custody_mode' => 'provider_projection',
            'required_capabilities' => [
                'readiness_probe',
                'balance_read',
                'funding_evidence_read',
                'funding_instruction_issue',
            ],
        ],
    ]);
    app()->instance(
        SettlementProviderRegistryContract::class,
        new SettlementProviderRegistry([$provider]),
    );
    app()->instance(
        SystemUserResolverContract::class,
        $systemResolver,
    );

    foreach ([
        TreasuryProviderConnectionCatalog::class,
        TreasuryPreflightService::class,
        TreasuryPayCodeAccountingService::class,
        TreasuryProvisioningService::class,
        TreasuryAccountPortfolioProvisioningContract::class,
        VerifiedTreasuryFundingAllocationContract::class,
    ] as $abstract) {
        app()->forgetInstance($abstract);
    }

    app()->instance(TreasuryProviderConnectionCatalog::class, new TreasuryProviderConnectionCatalog(
        config('x-change.treasury.connections'),
    ));
    app()->instance(TreasuryPreflightService::class, new TreasuryPreflightService(
        app(TreasuryProviderConnectionCatalog::class),
        app(SettlementProviderRegistryContract::class),
        [$probe],
    ));

    app(SystemPrincipalProvisioningService::class)->provision(
        'test:netbank-treasury-system-principal',
    );
    $system->refresh();

    return $system;
}

function settlementAcceptanceClientFunds(Model $user, int $amountMinor = 1_000_000): void
{
    $system = User::query()->where('email', config('x-change.payout.system_user_id'))->firstOrFail();
    $connection = 'netbank-primary';
    $preflight = app(TreasuryPreflightService::class)->run([$connection]);
    expect($preflight->passes())->toBeTrue(json_encode($preflight->connections));
    app(TreasuryAccountPortfolioProvisioningContract::class)->provision($user, [$connection]);
    app(TreasuryProvisioningService::class)->provision([$connection]);
    $systemPositions = TreasuryPosition::query()
        ->whereMorphedTo('principal', $system)
        ->where('connection_reference', $connection)
        ->get()
        ->keyBy('purpose');
    $source = $systemPositions->get(TreasuryPositionPurpose::TreasuryClearing->value);
    $destination = TreasuryPosition::query()
        ->whereMorphedTo('principal', $user)
        ->where('connection_reference', $connection)
        ->where('purpose', TreasuryPositionPurpose::ClientFunds)
        ->sole();

    if (! $source instanceof TreasuryPosition) {
        throw new RuntimeException('The test system Treasury Clearing position was not provisioned.');
    }

    $scope = Str::uuid()->toString();
    $operations = app(TreasuryPositionOperationContract::class);
    $recognition = $operations->recognize(new TreasuryPositionRecognitionData(
        operationReference: 'test-user-funding-recognition:'.$scope,
        destinationPositionReference: $source->position_reference,
        amountMinor: $amountMinor,
        currency: 'PHP',
        idempotencyKey: 'test-user-funding-recognition-key:'.$scope,
        externalReference: 'test-user-funding:'.$user->getKey(),
    ));
    $operations->allocate(new TreasuryPositionAllocationData(
        operationReference: 'test-user-funding-allocation:'.$scope,
        sourcePositionReference: $source->position_reference,
        destinationPositionReference: $destination->position_reference,
        amountMinor: $amountMinor,
        currency: 'PHP',
        idempotencyKey: 'test-user-funding-allocation-key:'.$scope,
        externalReference: $recognition->operationReference,
    ));
}

function settlementAcceptanceClientLedger(User $owner, string $provider = 'netbank'): Bavix\Wallet\Models\Wallet
{
    $position = TreasuryPosition::query()
        ->whereMorphedTo('principal', $owner)
        ->where('provider', $provider)
        ->where('purpose', TreasuryPositionPurpose::ClientFunds)
        ->sole();

    return Bavix\Wallet\Models\Wallet::query()->findOrFail($position->internal_ledger_id);
}

function settlementAcceptancePayload(
    float $amount = 100.00,
    ?string $settlementRail = 'INSTAPAY',
    array $overrides = []
): array {
    return array_replace_recursive([
        'cash' => [
            'amount' => $amount,
            'currency' => 'PHP',
            'settlement_rail' => $settlementRail,
            'validation' => [
                'secret' => null,
                'mobile' => null,
                'payable' => null,
                'country' => 'PH',
                'location' => null,
                'radius' => null,
            ],
        ],
        'inputs' => [
            'fields' => [
                'selfie',
                'signature',
            ],
        ],
        'feedback' => [
            'email' => 'example@example.com',
            'mobile' => '09171234567',
            'webhook' => 'https://example.com/webhook',
        ],
        'rider' => [
            'message' => null,
            'url' => null,
            'redirect_timeout' => null,
            'splash' => null,
            'splash_timeout' => null,
            'og_source' => null,
        ],
        'count' => 1,
        'prefix' => 'TEST',
        'mask' => '****',
        'ttl' => null,
        'metadata' => [],
    ], $overrides);
}

function settlementAcceptanceInventory(
    int $amountMinor,
    string $scope,
): void {
    $inventory = app(TreasuryInventoryOperationContract::class);
    $inventory->registerInventory(new TreasuryInventoryData(
        inventoryReference: 'inventory:netbank:vca-cash',
        resourceType: 'cash_at_bank',
        currency: 'PHP',
        capacityMinor: 0,
        status: 'requested',
        idempotencyKey: 'accounting-inventory-registration:'.$scope,
        externalReference: 'resource:netbank:corporate-vca',
    ));
    $inventory->recognize(new TreasuryInventoryRecognitionData(
        operationReference: 'accounting-inventory-recognition:'.$scope,
        inventoryReference: 'inventory:netbank:vca-cash',
        settlementResourceReference: 'resource:netbank:corporate-vca',
        amountMinor: $amountMinor,
        currency: 'PHP',
        status: 'requested',
        idempotencyKey: 'accounting-inventory-recognition-key:'.$scope,
        effectiveAt: now()->toRfc3339String(),
        externalReference: 'provider-observation:'.$scope,
    ));
}

it('funds a settlement principal and routes fixed repayments with one accepted service charge', function (string $bearer, bool $failedRepayment, bool $v2, int $principal, int $target, int $installment): void {
    $issuer = settlementAcceptanceUser(0);
    settlementAcceptanceTreasury();
    config()->set('x-change.commercial.enabled', true);
    config()->set('x-change.provider_runtime.default_provider', 'netbank');
    config()->set('x-change.funding.providers.netbank.enabled', true);
    config()->set('omnipay.gateways.netbank.options.rails.INSTAPAY', ['enabled' => true, 'min_amount' => 100, 'max_amount' => 5_000_000]);
    Queue::fake();
    settlementAcceptanceClientFunds($issuer, 50_000);
    settlementAcceptanceInventory(50_000, 'settlement-repayment');
    $funding = Mockery::mock(ProviderFundingPolicyContract::class);
    $funding->shouldReceive('assertCanIssue')->once()->andReturnUsing(function ($owner, $wallet, float $amount) use ($bearer, $principal): FundingDecisionData {
        expect($amount)->{$bearer === 'payer' ? 'toBe' : 'toBeGreaterThan'}((float) $principal);

        return FundingDecisionData::allowed(authority: 'local_ledger', availableMinor: 50_000,
            requiredMinor: (int) round($amount * 100), currency: 'PHP',
            meta: ['provider' => 'netbank', 'topology' => 'ledger_pooled']);
    });
    app()->instance(ProviderFundingPolicyContract::class, $funding);
    $payload = settlementAcceptancePayload($principal, 'INSTAPAY', [
        'provider' => 'netbank', 'issuer_id' => $issuer->getKey(),
        'voucher_type' => 'settlement', 'target_amount' => $target, 'fee_bearer' => $bearer,
        'rules' => ['settlement_mode' => 'disburse_then_repay', 'min_payment' => $installment, 'max_payment' => $installment],
        'value_flow' => ['destination' => 'instapay.gcash.mobile:09179999999'],
        'inputs' => ['fields' => []], 'feedback' => [],
    ]);
    $payload['inputs']['fields'] = [];
    if ($v2) {
        $payload['execution'] = ['schema' => 'voucher.execution.v2', 'driver' => 'default', 'value_flow' => $payload['value_flow']];
        unset($payload['value_flow']);
        data_set($payload, 'metadata.custom.cockpit.template_key', 'loan');
        data_set($payload, 'metadata.custom.cockpit.template_version', 1);
        data_set($payload, 'metadata.custom.claim_presentation', ['title' => 'Receive your loan', 'primary_action_label' => 'Receive loan']);
        data_set($payload, 'metadata.custom.loan_presentation', ['destination_title' => 'Where should we send your loan?', 'confirmation_label' => 'Confirm Loan Details']);
    }
    $result = app(GeneratePayCode::class)->handle($payload);
    $voucher = Voucher::findOrFail($result->voucher_id);
    $sale = CommercialSale::query()->sole();
    expect(data_get($voucher->metadata, 'treasury.pay_code_reservation.amount_minor'))->toBe($principal * 100)
        ->and($voucher->instructions->cash->amount)->toBe((float) $principal)
        ->and((float) $voucher->target_amount)->toBe((float) $target)
        ->and(settlementAcceptanceClientLedger($issuer)->balanceInt)->toBe(50_000 - $principal * 100 - ($bearer === 'issuer' ? $sale->total_price_minor : 0));
    expect(data_get($voucher->metadata, 'instructions.rules.settlement_mode'))->toBe('disburse_then_repay');
    expect(data_get($voucher->metadata, 'instructions.voucher_type'))->toBe('settlement');
    if ($v2) {
        expect(data_get($voucher->metadata, 'instructions.metadata.custom.cockpit.template_version'))->toBe(1)
            ->and(data_get($voucher->metadata, 'instructions.execution.value_flow.destination'))->toBe('instapay.gcash.mobile:09179999999');
        $projection = app(VoucherXRayProjectionBuilder::class)->build($voucher);
        expect(data_get($projection, 'presentation.title'))->toBe('Receive your loan')
            ->and(data_get($projection, 'presentation.primary_action_label'))->toBe('Receive loan');
        $workflow = app(ClaimWorkflowResolverContract::class)->resolve($voucher);
        expect($workflow->title)->toBe('Where should we send your loan?')
            ->and($workflow->confirmation_label)->toBe('Confirm Loan Details');
    }
    expect(fn () => app(CreatePaymentAttempt::class)->handle($voucher, 'netbank', 'repayment-browser', 'early'))
        ->toThrow(RuntimeException::class, 'after the initial disbursement');
    app(SettlementCollectionGate::class)->assertSettlementDoesNotDisburse($voucher);
    $provider = app('settlement.acceptance.provider')->reset()->willReturnSuccessfulResult(provider: 'netbank');
    $claim = app(SubmitPayCodeClaim::class)->handle($voucher, [
        'mobile' => '09170000001', 'recipient_country' => 'PH',
        'bank_account' => ['bank_code' => 'GXCHPHM2XXX', 'account_number' => '09170000001'],
    ]);
    expect($claim->claimed)->toBeTrue(json_encode($claim->toArray()))
        ->and($claim->status)->toBe('succeeded', json_encode($claim->toArray()))
        ->and($provider->disburseCallCount)->toBe(1)
        ->and($provider->lastRequest->account_number)->toBe('09170000001')
        ->and($voucher->fresh()->canAcceptPayment())->toBeTrue();
    expect(fn () => app(SubmitPayCodeClaim::class)->handle($voucher->fresh(), [
        'mobile' => '09170000001', 'recipient_country' => 'PH',
        'bank_account' => ['bank_code' => 'GXCHPHM2XXX', 'account_number' => '09170000001'],
    ]))->toThrow(RuntimeException::class);
    expect($provider->disburseCallCount)->toBe(1);
    $this->get(route('x-change.claim.show', ['code' => $voucher->code]))
        ->assertRedirect(route('x-change.pay.show', ['code' => $voucher->code]));
    $this->post(route('x-change.claim.flows.store', ['code' => $voucher->code]))
        ->assertRedirect(route('x-change.pay.show', ['code' => $voucher->code]));
    $version = app(HandleInertiaRequests::class)->version(request());
    $this->withHeaders(['X-Inertia' => 'true', 'X-Inertia-Version' => $version])
        ->get(route('x-change.pay.show', ['code' => $voucher->code]))
        ->assertOk()->assertJsonPath('props.payment.repayment_mode', true)
        ->assertJsonPath('props.payment.repayment_ready', true)
        ->assertJsonPath('props.payment.principal_due_minor', $installment * 100)
        ->assertJsonPath('props.payment.remaining_to_collect_minor', $target * 100);
    $paymentCount = intdiv($target, $installment);
    $failedPayout = null;
    for ($number = 1; $number <= $paymentCount; $number++) {
        $voucher->refresh();
        $fee = $number === 1 && $bearer === 'payer' ? $sale->total_price_minor : 0;
        $attempt = app(CreatePaymentAttempt::class)->handle($voucher, 'netbank', 'repayment-browser', 'repayment-'.$number);
        expect($attempt->expected_amount_minor)->toBe($installment * 100 + $fee);
        $confirmed = new ConfirmedVoucherCollectionData(
            amountMinor: $installment * 100 + $fee, currency: 'PHP', executionDriver: 'provider_wallet',
            authority: 'authoritative_provider', authorityReference: 'repayment-'.$number,
            idempotencyKey: 'repayment-'.$number, provider: 'netbank', providerTransactionId: 'repayment-'.$number,
        );
        $collection = app(CompleteVoucherCollection::class)->handle($voucher, $confirmed);
        expect(app(CompleteVoucherCollection::class)->handle($voucher, $confirmed)->id)->toBe($collection->id);
        $payout = DisbursementReconciliation::query()->where('claim_type', 'value_flow')->latest('id')->firstOrFail();
        if ($failedRepayment && $number === 2) {
            $provider->willReturnFailedResult(provider: 'netbank', metadata: ['provider_submission_accepted' => false]);
        } else {
            $provider->willReturnSuccessfulResult(provider: 'netbank');
        }
        app(ValueFlowPayoutService::class)->submit($payout->id);
        app(ValueFlowPayoutService::class)->submit($payout->id);
        expect($payout->fresh()->internal_status)->toBe($failedRepayment && $number === 2 ? 'recovery_opened' : 'finalized')
            ->and((float) $payout->amount)->toBe((float) $installment)
            ->and($provider->lastRequest->account_number)->toBe('09179999999');
        if ($failedRepayment && $number === 2) {
            $failedPayout = $payout;
        }
    }
    expect($provider->disburseCallCount)->toBe($paymentCount + 1)
        ->and($voucher->fresh()->isClosed())->toBeTrue()
        ->and($sale->fresh()->status)->toBe('posted')
        ->and(CommercialSale::query()->count())->toBe(1)
        ->and(ExecutionJournalEntry::query()->where('event_type', 'voucher.collection.completed')->count())->toBe($paymentCount);
    if ($failedPayout !== null) {
        $provider->willReturnSuccessfulResult(provider: 'netbank');
        $this->artisan('xchange:value-flow:recovery', [
            'voucher' => $voucher->code,
            '--id' => $failedPayout->id,
            '--retry-unaccepted' => true,
            '--confirm-no-transfer' => true,
            '--evidence' => 'simulated-provider-rejection',
        ])->assertSuccessful();
        expect($failedPayout->fresh()->internal_status)->toBe('finalized')
            ->and($provider->disburseCallCount)->toBe($paymentCount + 2);
    }
    expect(fn () => app(CreatePaymentAttempt::class)->handle($voucher->fresh(), 'netbank', 'repayment-browser', 'fifth'))
        ->toThrow(LogicException::class, 'fully paid');
})->with([
    'v1 issuer' => ['issuer', false, false, 100, 120, 30],
    'v2 failed payout and recovery' => ['issuer', true, true, 100, 120, 30],
]);
