<?php

declare(strict_types=1);

use LBHurtado\PaymentGateway\Funding\NetbankFundingProviderAdapter;
use LBHurtado\XChange\Services\Funding\FundingProviderAdapterRegistry;

it('discovers the NetBank funding adapter through the provider-neutral registry', function () {
    expect(app(FundingProviderAdapterRegistry::class)->for('netbank'))
        ->toBeInstanceOf(NetbankFundingProviderAdapter::class);
});
