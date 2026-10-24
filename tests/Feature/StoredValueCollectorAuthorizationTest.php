<?php

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Gate;

it('allows only the commissioned testing collector', function (): void {
    $approved = User::factory()->make(['id' => 101]);
    $other = User::factory()->make(['id' => 102]);
    config()->set('stored_value_collection.operator_user_ids', [$approved->getKey()]);

    $method = new ReflectionMethod(AppServiceProvider::class, 'configureStoredValueCollectors');
    $method->invoke(new AppServiceProvider(app()));

    expect(Gate::forUser($approved)->allows('x-change.stored-value.collect'))->toBeTrue()
        ->and(Gate::forUser($other)->allows('x-change.stored-value.collect'))->toBeFalse();
});

it('leaves testing collection unavailable without an allowlist', function (): void {
    config()->set('stored_value_collection.operator_user_ids', []);

    $method = new ReflectionMethod(AppServiceProvider::class, 'configureStoredValueCollectors');
    $method->invoke(new AppServiceProvider(app()));

    expect(Gate::has('x-change.stored-value.collect'))->toBeFalse();
});
