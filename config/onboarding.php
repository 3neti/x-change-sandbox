<?php

use App\Models\User;
use LBHurtado\Onboarding\Enums\AuthAssuranceLevel;
use LBHurtado\Onboarding\Enums\AuthFactor;
use LBHurtado\Onboarding\Enums\IdentityLevel;
use LBHurtado\Onboarding\Enums\OnboardingPurpose;
use LBHurtado\Onboarding\Models\OnboardingSession;

return [
    'routes' => [
        'enabled' => true,
        'prefix' => 'api/onboarding',
        'middleware' => ['api'],
    ],

    'models' => [
        'session' => OnboardingSession::class,
        'user' => class_exists('App\\Models\\User') ? User::class : null,
    ],

    'user' => [
        'primary_identifier' => 'mobile',
        'email_required' => false,
        'name_required' => false,
        'package_user_model_enabled' => false,
    ],

    'identity' => [
        'default_level' => IdentityLevel::Provisional->value,
        'levels' => [
            IdentityLevel::Provisional->value => [
                'requires_name' => false,
                'requires_email' => false,
                'requires_mobile' => true,
                'requires_kyc' => false,
            ],
            IdentityLevel::Declared->value => [
                'requires_name' => true,
                'requires_email' => false,
                'requires_mobile' => true,
                'requires_kyc' => false,
            ],
            IdentityLevel::Verified->value => [
                'requires_name' => true,
                'requires_email' => false,
                'requires_mobile' => true,
                'requires_kyc' => true,
            ],
        ],
    ],

    'auth' => [
        'default_login_factor' => AuthFactor::MobileOtp->value,
        'assurance' => [
            OnboardingPurpose::RedeemPayCode->value => AuthAssuranceLevel::MobileVerified->value,
            OnboardingPurpose::BankOnboardingRequired->value => AuthAssuranceLevel::WalletAuthorized->value,
            OnboardingPurpose::IssuePayCode->value => AuthAssuranceLevel::Strong->value,
            OnboardingPurpose::LinkBankAccount->value => AuthAssuranceLevel::WalletAuthorized->value,
            OnboardingPurpose::UpgradeKyc->value => AuthAssuranceLevel::Strong->value,
            OnboardingPurpose::MerchantOnboarding->value => AuthAssuranceLevel::Strong->value,
        ],
        'pin' => [
            'enabled' => true,
            'length' => 6,
        ],
        'totp' => [
            'enabled' => true,
            'required_for_admins' => true,
        ],
    ],

    'requirements' => [
        OnboardingPurpose::RedeemPayCode->value => [
            'identity_level' => IdentityLevel::Provisional->value,
            'requires_kyc' => false,
            'requires_wallet' => false,
            'requires_bank_account' => false,
            'requires_mobile_verification' => true,
        ],
        OnboardingPurpose::BankOnboardingRequired->value => [
            'identity_level' => IdentityLevel::Verified->value,
            'requires_kyc' => true,
            'requires_wallet' => true,
            'requires_bank_account' => true,
            'requires_mobile_verification' => true,
        ],
        OnboardingPurpose::IssuePayCode->value => [
            'identity_level' => IdentityLevel::Verified->value,
            'requires_kyc' => true,
            'requires_wallet' => true,
            'requires_bank_account' => false,
            'requires_mobile_verification' => true,
        ],
        OnboardingPurpose::LinkBankAccount->value => [
            'identity_level' => IdentityLevel::Verified->value,
            'requires_kyc' => true,
            'requires_wallet' => false,
            'requires_bank_account' => true,
            'requires_mobile_verification' => true,
        ],
        OnboardingPurpose::UpgradeKyc->value => [
            'identity_level' => IdentityLevel::Verified->value,
            'requires_kyc' => true,
            'requires_wallet' => false,
            'requires_bank_account' => false,
            'requires_mobile_verification' => true,
        ],
        OnboardingPurpose::MerchantOnboarding->value => [
            'identity_level' => IdentityLevel::Verified->value,
            'requires_kyc' => true,
            'requires_wallet' => true,
            'requires_bank_account' => false,
            'requires_mobile_verification' => true,
        ],
    ],

    'phone' => [
        'default_country' => 'PH',
        'countries' => ['PH'],
        'allow_international' => true,
    ],
];
