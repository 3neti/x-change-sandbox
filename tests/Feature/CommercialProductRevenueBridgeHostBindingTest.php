<?php

declare(strict_types=1);

use LBHurtado\XChange\Contracts\CommercialProductRevenueMovementContract;
use LBHurtado\XChange\Services\Commercial\BavixCommercialProductRevenueMovement;

it('binds the governed Product revenue movement adapter in the host', function (): void {
    if (! class_exists(BavixCommercialProductRevenueMovement::class)) {
        $this->markTestSkipped('The installed x-change package does not yet contain the governed adapter.');
    }

    $movement = app(CommercialProductRevenueMovementContract::class);

    expect($movement)->toBeInstanceOf(BavixCommercialProductRevenueMovement::class);
});

it('keeps the Product revenue bridge disabled by default', function (): void {
    expect((bool) config('x-change.commercial.product_revenue_bridge.enabled', false))->toBeFalse();
});
