<?php

use LBHurtado\EmiPaynamicsConstellation\Adapters\ConstellationPayoutProvider;
use LBHurtado\PaymentGateway\Adapters\NetbankPayoutProvider;

return [
    'payout_provider' => env('EMI_PAYOUT_PROVIDER', 'netbank'),

    'default_payout_provider' => env('EMI_PAYOUT_PROVIDER', 'netbank'),

    'payout_providers' => [
        'netbank' => NetbankPayoutProvider::class,
        'paynamics' => ConstellationPayoutProvider::class,
    ],
];
