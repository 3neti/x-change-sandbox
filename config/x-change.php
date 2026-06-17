<?php

declare(strict_types=1);

use App\Models\User;
use LBHurtado\Instruction\Models\InstructionItem;
use LBHurtado\PaymentGateway\Adapters\NetbankPayoutProvider;
use LBHurtado\XChange\Contracts\AuditLoggerContract;
use LBHurtado\XChange\Contracts\IdempotencyStoreContract;
use LBHurtado\XChange\Contracts\IssuerOnboardingContract;
use LBHurtado\XChange\Contracts\IssuerResolverContract;
use LBHurtado\XChange\Contracts\PayCodeIssuanceContract;
use LBHurtado\XChange\Contracts\PricingServiceContract;
use LBHurtado\XChange\Contracts\ProviderAccountLinkRepositoryContract;
use LBHurtado\XChange\Contracts\ProviderProvisioningGatewayContract;
use LBHurtado\XChange\Contracts\ProviderProvisioningManagerContract;
use LBHurtado\XChange\Contracts\ProviderReadinessGuardContract;
use LBHurtado\XChange\Contracts\ProviderRuntimeSettingsResolverContract;
use LBHurtado\XChange\Contracts\SystemWalletResolverContract;
use LBHurtado\XChange\Contracts\TerminologyServiceContract;
use LBHurtado\XChange\Contracts\UserResolverContract;
use LBHurtado\XChange\Contracts\VoucherAccessContract;
use LBHurtado\XChange\Contracts\VoucherEntryRouteResolverContract;
use LBHurtado\XChange\Contracts\WalletAccessContract;
use LBHurtado\XChange\Contracts\WalletProvisioningContract;
use LBHurtado\XChange\Contracts\XChangeOnboardingGatewayContract;
use LBHurtado\XChange\Contracts\XChangeProviderTopologyResolverContract;
use LBHurtado\XChange\Repositories\EloquentProviderAccountLinkRepository;
use LBHurtado\XChange\Services\ApiResponseFactory;
use LBHurtado\XChange\Services\ApprovalHandlers\ManualApprovalRequirementHandler;
use LBHurtado\XChange\Services\ApprovalHandlers\OtpApprovalRequirementHandler;
use LBHurtado\XChange\Services\CacheIdempotencyStore;
use LBHurtado\XChange\Services\ConfigProviderRuntimeSettingsResolver;
use LBHurtado\XChange\Services\ConfigProviderTopologyResolver;
use LBHurtado\XChange\Services\ContextUserResolver;
use LBHurtado\XChange\Services\DefaultClaimExecutionFactory;
use LBHurtado\XChange\Services\DefaultDisbursementReconciliationService;
use LBHurtado\XChange\Services\DefaultDisbursementReconciliationStore;
use LBHurtado\XChange\Services\DefaultDisbursementStatusFetcherService;
use LBHurtado\XChange\Services\DefaultDisbursementStatusResolverService;
use LBHurtado\XChange\Services\DefaultIssuerOnboardingService;
use LBHurtado\XChange\Services\DefaultProviderProvisioningManager;
use LBHurtado\XChange\Services\DefaultProviderReadinessGuard;
use LBHurtado\XChange\Services\DefaultRedemptionCompletionContextService;
use LBHurtado\XChange\Services\DefaultRedemptionContextResolverService;
use LBHurtado\XChange\Services\DefaultRedemptionExecutionService;
use LBHurtado\XChange\Services\DefaultRedemptionFlowPreparationService;
use LBHurtado\XChange\Services\DefaultRedemptionProcessorService;
use LBHurtado\XChange\Services\DefaultRedemptionValidationService;
use LBHurtado\XChange\Services\DefaultWalletProvisioningService;
use LBHurtado\XChange\Services\DefaultWithdrawalExecutionService;
use LBHurtado\XChange\Services\DefaultWithdrawalProcessorService;
use LBHurtado\XChange\Services\DefaultWithdrawalValidationService;
use LBHurtado\XChange\Services\DefaultXChangeOnboardingGateway;
use LBHurtado\XChange\Services\DisburseFlowStarterService;
use LBHurtado\XChange\Services\LedgerPooledProviderTopology;
use LBHurtado\XChange\Services\ManualProviderTopology;
use LBHurtado\XChange\Services\NullClaimOtpChallengeService;
use LBHurtado\XChange\Services\NullClaimOtpVerificationService;
use LBHurtado\XChange\Services\NullRedemptionCompletionStore;
use LBHurtado\XChange\Services\PayCodeIssuanceService;
use LBHurtado\XChange\Services\PricingService;
use LBHurtado\XChange\Services\ProviderCustomerWalletTopology;
use LBHurtado\XChange\Services\Provisioning\FakeProviderProvisioningGateway;
use LBHurtado\XChange\Services\SessionCompletionStore;
use LBHurtado\XChange\Services\SystemWalletProxy;
use LBHurtado\XChange\Services\TerminologyService;
use LBHurtado\XChange\Services\VoucherAccessService;
use LBHurtado\XChange\Services\VoucherEntryRouteService;
use LBHurtado\XChange\Services\WalletAccessService;
use LBHurtado\XChange\Services\WithdrawalLifecycleService;
use LBHurtado\XChange\Services\WithdrawalOtpApprovalBackedClaimOtpChallengeService;
use LBHurtado\XChange\Services\WithdrawalOtpApprovalBackedClaimOtpVerificationService;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\AssertWithdrawalEligibilityStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\AuthorizeWithdrawalClaimantStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\AuthorizeWithdrawalOtpStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\AuthorizeWithdrawalPolicyStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\BuildWithdrawalPayoutRequestStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\BuildWithdrawalResultStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\ExecuteWithdrawalDisbursementStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\GuardWithdrawalRailStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\ResolveWithdrawalAmountStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\ResolveWithdrawalBankAccountStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\ResolveWithdrawalClaimantStep;
use LBHurtado\XChange\Services\WithdrawalPipelineSteps\WithdrawalWalletSettlementStep;
use LBHurtado\XChange\Support\Logging\NullAuditLogger;
use LBHurtado\XChange\Support\Resolvers\DefaultIssuerResolver;
use LBHurtado\XChange\Support\Resolvers\NullSystemWalletResolver;

// use LBHurtado\XChange\Support\Resolvers\NullUserResolver;

return [

    'product' => [
        'name' => env('XCHANGE_PRODUCT_NAME', 'X-Change'),
        'code' => env('XCHANGE_PRODUCT_CODE', 'x-change'),
        'default_currency' => env('XCHANGE_DEFAULT_CURRENCY', 'PHP'),
        'default_country' => env('XCHANGE_DEFAULT_COUNTRY', 'PH'),
    ],

    'terminology' => [
        'voucher' => env('XCHANGE_TERM_VOUCHER', 'Pay Code'),
        'voucher_code' => env('XCHANGE_TERM_VOUCHER_CODE', 'Pay Code'),
        'redeem' => env('XCHANGE_TERM_REDEEM', 'Claim'),
        'withdraw' => env('XCHANGE_TERM_WITHDRAW', 'Withdraw'),
        'wallet' => env('XCHANGE_TERM_WALLET', 'Wallet'),
        'account' => env('XCHANGE_TERM_ACCOUNT', 'Account'),
    ],

    'routes' => [
        'web' => env('XCHANGE_ROUTES_WEB', true),
        'api' => env('XCHANGE_ROUTES_API', true),

        'experimental' => [
            'enabled' => env('XCHANGE_ROUTES_EXPERIMENTAL_ENABLED', true),
            'prefix' => env('XCHANGE_ROUTES_EXPERIMENTAL_PREFIX', 'x'),
        ],

        'api_prefix' => env('XCHANGE_API_PREFIX', 'api/x'),
        'api_version' => env('XCHANGE_API_VERSION', 'v1'),

        'paths' => [
            'redeem' => env('XCHANGE_PATH_REDEEM', 'disburse'),
            'withdraw' => env('XCHANGE_PATH_WITHDRAW', 'withdraw'),
            'pay' => env('XCHANGE_PATH_PAY', 'pay'),
            'generate' => env('XCHANGE_PATH_GENERATE', 'pay-codes'),
            'estimate' => env('XCHANGE_PATH_ESTIMATE', 'pay-codes/estimate'),
            'wallets' => env('XCHANGE_PATH_WALLETS', 'wallets'),
            'users' => env('XCHANGE_PATH_USERS', 'users'),
        ],
    ],

    'api' => [
        'idempotency' => [
            'enabled' => env('XCHANGE_API_IDEMPOTENCY_ENABLED', true),
            'header' => env('XCHANGE_API_IDEMPOTENCY_HEADER', 'Idempotency-Key'),
            'ttl' => (int) env('XCHANGE_API_IDEMPOTENCY_TTL', 3600),
        ],

        'correlation' => [
            'enabled' => env('XCHANGE_API_CORRELATION_ENABLED', true),
            'header' => env('XCHANGE_API_CORRELATION_HEADER', 'X-Correlation-ID'),
        ],

        'response' => [
            'wrap_success' => env('XCHANGE_API_WRAP_SUCCESS', true),
            'wrap_errors' => env('XCHANGE_API_WRAP_ERRORS', true),
            'success_key' => env('XCHANGE_API_SUCCESS_KEY', 'success'),
            'data_key' => env('XCHANGE_API_DATA_KEY', 'data'),
            'meta_key' => env('XCHANGE_API_META_KEY', 'meta'),
            'message_key' => env('XCHANGE_API_MESSAGE_KEY', 'message'),
            'code_key' => env('XCHANGE_API_CODE_KEY', 'code'),
            'errors_key' => env('XCHANGE_API_ERRORS_KEY', 'errors'),
        ],
    ],

    'services' => [
        'user_resolver' => ContextUserResolver::class,
        'voucher_access' => VoucherAccessService::class,
        'entry_route' => VoucherEntryRouteService::class,
        'disburse_flow' => DisburseFlowStarterService::class,
        'completion_store' => SessionCompletionStore::class,
        'terminology' => TerminologyService::class,
        'api_response' => ApiResponseFactory::class,
        'pricing' => PricingService::class,
        'issuance' => PayCodeIssuanceService::class,
        'wallet_access' => WalletAccessService::class,
        'idempotency_store' => CacheIdempotencyStore::class,
        'issuer_onboarding' => DefaultIssuerOnboardingService::class,
        'onboarding_gateway' => DefaultXChangeOnboardingGateway::class,
        'provider_topology_resolver' => ConfigProviderTopologyResolver::class,
        'provider_runtime_settings' => ConfigProviderRuntimeSettingsResolver::class,
        'provider_account_links' => EloquentProviderAccountLinkRepository::class,
        'provider_provisioning_gateway' => FakeProviderProvisioningGateway::class,
        'provider_provisioning_manager' => DefaultProviderProvisioningManager::class,
        'provider_readiness_guard' => DefaultProviderReadinessGuard::class,
        'wallet_provisioning' => DefaultWalletProvisioningService::class,
        'issuer_resolver' => DefaultIssuerResolver::class,
        'redemption_flow_preparation' => DefaultRedemptionFlowPreparationService::class,
        'redemption_completion_context' => DefaultRedemptionCompletionContextService::class,
        'redemption_completion_store' => NullRedemptionCompletionStore::class,
        'claim_execution_factory' => DefaultClaimExecutionFactory::class,
        'redemption_context_resolver' => DefaultRedemptionContextResolverService::class,
        'redemption_validation' => DefaultRedemptionValidationService::class,
        'redemption_processor' => DefaultRedemptionProcessorService::class,
        'redemption_execution' => DefaultRedemptionExecutionService::class,
        'withdrawal_validation' => DefaultWithdrawalValidationService::class,
        'withdrawal_processor' => DefaultWithdrawalProcessorService::class,
        'withdrawal_execution' => DefaultWithdrawalExecutionService::class,
        'disbursement_reconciliation_store' => DefaultDisbursementReconciliationStore::class,
        'disbursement_status_resolver' => DefaultDisbursementStatusResolverService::class,
        'disbursement_status_fetcher' => DefaultDisbursementStatusFetcherService::class,
        'disbursement_reconciliation' => DefaultDisbursementReconciliationService::class,
    ],

    'service_contracts' => [
        UserResolverContract::class => 'user_resolver',
        VoucherAccessContract::class => 'voucher_access',
        VoucherEntryRouteResolverContract::class => 'entry_route',
        TerminologyServiceContract::class => 'terminology',
        PricingServiceContract::class => 'pricing',
        PayCodeIssuanceContract::class => 'issuance',
        WalletAccessContract::class => 'wallet_access',
        IdempotencyStoreContract::class => 'idempotency_store',
        IssuerOnboardingContract::class => 'issuer_onboarding',
        XChangeOnboardingGatewayContract::class => 'onboarding_gateway',
        XChangeProviderTopologyResolverContract::class => 'provider_topology_resolver',
        ProviderRuntimeSettingsResolverContract::class => 'provider_runtime_settings',
        ProviderAccountLinkRepositoryContract::class => 'provider_account_links',
        ProviderProvisioningGatewayContract::class => 'provider_provisioning_gateway',
        ProviderProvisioningManagerContract::class => 'provider_provisioning_manager',
        ProviderReadinessGuardContract::class => 'provider_readiness_guard',
        WalletProvisioningContract::class => 'wallet_provisioning',
        IssuerResolverContract::class => 'issuer_resolver',
    ],

    'integrations' => [
        'system_wallet_resolver' => NullSystemWalletResolver::class,
        'audit_logger' => NullAuditLogger::class,
    ],

    'integration_contracts' => [
        SystemWalletResolverContract::class => 'system_wallet_resolver',
        AuditLoggerContract::class => 'audit_logger',
    ],

    'pricing' => [
        'currency' => env('XCHANGE_PRICING_CURRENCY', 'PHP'),

        'base_fee' => (float) env('XCHANGE_PRICING_BASE_FEE', 0.00),

        'components' => [
            'cash' => (float) env('XCHANGE_PRICE_CASH', 0.00),
            'kyc' => (float) env('XCHANGE_PRICE_KYC', 25.00),
            'otp' => (float) env('XCHANGE_PRICE_OTP', 2.00),
            'selfie' => (float) env('XCHANGE_PRICE_SELFIE', 5.00),
            'signature' => (float) env('XCHANGE_PRICE_SIGNATURE', 3.00),
            'location' => (float) env('XCHANGE_PRICE_LOCATION', 1.00),
            'webhook' => (float) env('XCHANGE_PRICE_WEBHOOK', 0.00),
            'email_feedback' => (float) env('XCHANGE_PRICE_EMAIL_FEEDBACK', 0.00),
            'sms_feedback' => (float) env('XCHANGE_PRICE_SMS_FEEDBACK', 0.00),
        ],

        'minimum_balance_enforced' => env('XCHANGE_PRICING_MINIMUM_BALANCE_ENFORCED', true),
    ],

    'onboarding' => [
        //        'issuer_model' => env('XCHANGE_ONBOARDING_DEFAULT_ISSUER_MODEL', \LBHurtado\XChange\Tests\Fakes\User::class),
        'issuer_model' => env('XCHANGE_ONBOARDING_DEFAULT_ISSUER_MODEL', User::class),
        'default_wallet_slug' => env('XCHANGE_ONBOARDING_DEFAULT_WALLET_SLUG', 'platform'),
        'default_wallet_name' => env('XCHANGE_ONBOARDING_DEFAULT_WALLET_NAME', 'Platform Wallet'),
        'driver' => env('XCHANGE_ONBOARDING_DRIVER', 'legacy'),
        'mobile_first_auth' => env('XCHANGE_MOBILE_FIRST_AUTH', true),
        'email_required' => env('XCHANGE_AUTH_EMAIL_REQUIRED', false),
        'auth_enforcement' => env('XCHANGE_ONBOARDING_AUTH_ENFORCEMENT', 'scaffold'),
    ],

    'provider_topologies' => [
        'default' => env('XCHANGE_PROVIDER_TOPOLOGY', 'manual'),
        'aliases' => [
            'manual' => 'manual',
            'netbank' => 'ledger_pooled',
            'paynamics' => 'provider_customer_wallet',
        ],
        'topologies' => [
            'manual' => ManualProviderTopology::class,
            'ledger_pooled' => LedgerPooledProviderTopology::class,
            'provider_customer_wallet' => ProviderCustomerWalletTopology::class,
        ],
    ],

    'provider_runtime' => [
        'default_provider' => env('XCHANGE_PROVIDER', env('XCHANGE_PROVIDER_TOPOLOGY', 'manual')),

        'providers' => [
            'manual' => [
                'enabled' => env('XCHANGE_PROVIDER_MANUAL_ENABLED', true),
            ],
            'netbank' => [
                'enabled' => env('XCHANGE_PROVIDER_NETBANK_ENABLED', true),
            ],
            'paynamics' => [
                'enabled' => env('XCHANGE_PROVIDER_PAYNAMICS_ENABLED', true),
            ],
        ],

        'lifecycle' => [
            'allow_live_provider_scenarios' => env('XCHANGE_LIFECYCLE_ALLOW_LIVE_PROVIDER_SCENARIOS', false),
        ],

        'settlement' => [
            'default_rail' => env('XCHANGE_DEFAULT_SETTLEMENT_RAIL', 'INSTAPAY'),
        ],
    ],

    'redemption' => [
        'field_mappings' => [
            'full_name' => 'name',
            'date_of_birth' => 'birth_date',
            'otp_code' => 'otp',
        ],
    ],
    'payout' => [
        'provider' => env('XCHANGE_PAYOUT_PROVIDER', NetbankPayoutProvider::class),
        'wallet_proxy' => SystemWalletProxy::class,
        'system_user_id' => env('XCHANGE_SYSTEM_USER_ID'),
        'system_user_column' => env('XCHANGE_SYSTEM_USER_COLUMN', 'id'),
        'system_wallet_slug' => env(
            'XCHANGE_SYSTEM_WALLET_SLUG',
            env('XCHANGE_ONBOARDING_DEFAULT_WALLET_SLUG', 'platform')
        ),
    ],
    'revenue' => [
        'instruction_item_model' => env(
            'XCHANGE_REVENUE_INSTRUCTION_ITEM_MODEL',
            InstructionItem::class,
            //            \App\Models\InstructionItem::class
        ),

        'destination' => [
            'model' => env(
                'XCHANGE_REVENUE_DESTINATION_MODEL',
                User::class
            ),
            'identifier' => env('XCHANGE_REVENUE_DESTINATION_IDENTIFIER'),
            'identifier_column' => env('XCHANGE_REVENUE_DESTINATION_IDENTIFIER_COLUMN', 'email'),
        ],
    ],
    'lifecycle' => [
        ...require __DIR__.'/lifecycle-scenarios.php',
        'withdrawals' => [
            'service' => WithdrawalLifecycleService::class,
        ],
        'scenario_groups' => [
            'pre-deployment' => [
                'label' => 'Pre-Deployment Checks',
                'description' => 'Runs critical lifecycle scenarios before deployment.',
                'categories' => ['smoke', 'contract', 'settlement', 'reconciliation'],
                'tags' => [],
                'scenarios' => [],
            ],

            'post-deployment' => [
                'label' => 'Post-Deployment Checks',
                'description' => 'Runs lightweight checks after deployment.',
                'categories' => ['smoke'],
                'tags' => [],
                'scenarios' => [],
            ],

            'partner-certification' => [
                'label' => 'Partner Certification',
                'description' => 'Runs lifecycle scenarios needed to certify partner readiness.',
                'categories' => ['smoke', 'contract', 'provider', 'settlement', 'reconciliation'],
                'tags' => [],
                'scenarios' => [],
            ],

            'demo' => [
                'label' => 'Demo Automation',
                'description' => 'Runs scenarios useful for predictable demos.',
                'categories' => [],
                'tags' => ['demo'],
                'scenarios' => [
                    // optionally explicit scenario keys
                ],
            ],

            'turnkey-onboarding' => [
                'label' => 'Turnkey Onboarding',
                'description' => 'Runs safe mobile-first install and onboarding readiness scenarios.',
                'categories' => [],
                'tags' => [],
                'scenarios' => [
                    'turnkey_mobile_boot',
                    'turnkey_bank_onboarding_required',
                    'turnkey_provider_link_ready',
                    'turnkey_provider_link_pending_blocks',
                    'turnkey_netbank_bank_account_ready',
                    'turnkey_paynamics_wallet_fake_provisioned',
                    'turnkey_issuer_blocks_missing_provider_wallet',
                    'turnkey_issuer_allows_ready_provider_wallet',
                    'turnkey_claim_blocks_missing_bank_account',
                    'turnkey_claim_resumes_after_provider_account_ready',
                    'turnkey_basic_cash_mobile',
                ],
            ],
        ],
    ],

    'withdrawal' => [
        'open_slice_min_interval_seconds' => 10,

        'pipeline' => [
            'steps' => [
                ResolveWithdrawalClaimantStep::class,
                AssertWithdrawalEligibilityStep::class,
                AuthorizeWithdrawalClaimantStep::class,
                ResolveWithdrawalAmountStep::class,

                AuthorizeWithdrawalOtpStep::class,
                AuthorizeWithdrawalPolicyStep::class,

                ResolveWithdrawalBankAccountStep::class,
                BuildWithdrawalPayoutRequestStep::class,
                GuardWithdrawalRailStep::class,
                ExecuteWithdrawalDisbursementStep::class,
                WithdrawalWalletSettlementStep::class,
                BuildWithdrawalResultStep::class,
            ],
        ],

        'otp' => [
            'driver' => env('XCHANGE_WITHDRAWAL_OTP_DRIVER', 'null'),
            'required' => env('XCHANGE_WITHDRAWAL_OTP_REQUIRED', false),
            'label' => env('OTP_LABEL', config('app.name', 'x-change')),

            'txtcmdr' => [
                'base_url' => env('TXTCMDR_API_URL', 'http://txtcmdr.test'),
                'api_token' => env('TXTCMDR_API_TOKEN'),
                'sender_id' => env('TXTCMDR_DEFAULT_SENDER_ID', 'cashless'),
                'timeout' => env('TXTCMDR_TIMEOUT', 30),
                'verify_ssl' => env('TXTCMDR_VERIFY_SSL', true),
                'test_mobile' => env('TXTCMDR_TEST_MOBILE'),
            ],
        ],
    ],

    'vendors' => [
        'registry' => env('XCHANGE_VENDOR_REGISTRY', 'config'),
        'aliases' => [],
    ],

    'approval_workflow' => [
        'handlers' => [
            'approval' => ManualApprovalRequirementHandler::class,
            'otp' => OtpApprovalRequirementHandler::class,
        ],
    ],

    'claim_approval' => [
        'ttl_minutes' => env('X_CHANGE_CLAIM_APPROVAL_TTL_MINUTES', 15),
        'otp' => [
            'driver' => env('X_CHANGE_CLAIM_APPROVAL_OTP_DRIVER', 'null'),
            'drivers' => [
                'null' => [
                    'challenge' => NullClaimOtpChallengeService::class,
                    'verify' => NullClaimOtpVerificationService::class,
                ],
                'withdrawal_otp' => [
                    'challenge' => WithdrawalOtpApprovalBackedClaimOtpChallengeService::class,
                    'verify' => WithdrawalOtpApprovalBackedClaimOtpVerificationService::class,
                ],
            ],
        ],
    ],
];
