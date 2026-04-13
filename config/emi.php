<?php

return [
    'payout_provider' => env('EMI_PAYOUT_PROVIDER', 'netbank'),

    'default_payout_provider' => env('EMI_PAYOUT_PROVIDER', 'netbank'),

    'payout_providers' => [
        'netbank' => \LBHurtado\PaymentGateway\Adapters\NetbankPayoutProvider::class,
//        'paynamics' => \LBHurtado\EmiPaynamicsConstellation\Adapters\ConstellationPayoutProvider::class
    ],
];
