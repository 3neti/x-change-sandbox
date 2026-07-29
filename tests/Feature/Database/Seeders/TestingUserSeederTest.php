<?php

use App\Models\User;
use Bavix\Wallet\Models\Wallet;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TestingUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('seeds the two verified testing users idempotently without Account balances', function () {
    $this->seed(TestingUserSeeder::class);

    $lester = User::query()->where('mobile', '639173011987')->sole();
    $anais = User::query()->where('mobile', '639467438575')->sole();

    expect(User::query()->count())->toBe(2)
        ->and($lester->getKey())->toBe(1)
        ->and($lester->name)->toBe('Lester Hurtado')
        ->and($lester->email)->toBe('lbhurtado@gmail.com')
        ->and($lester->mobile_verified_at)->not->toBeNull()
        ->and($lester->email_verified_at)->not->toBeNull()
        ->and(Hash::check('537537', $lester->password))->toBeTrue()
        ->and($lester->password)->not->toBe('537537')
        ->and($anais->getKey())->toBe(2)
        ->and($anais->name)->toBe('Anaïs Santos')
        ->and($anais->email)->toBe('geckaanais17@gmail.com')
        ->and($anais->mobile_verified_at)->not->toBeNull()
        ->and($anais->email_verified_at)->not->toBeNull()
        ->and(Hash::check('1234', $anais->password))->toBeTrue()
        ->and($anais->password)->not->toBe('1234')
        ->and(Wallet::query()->count())->toBe(0);

    $originalUserIds = User::query()->orderBy('id')->pluck('id')->all();

    $this->seed(TestingUserSeeder::class);

    expect(User::query()->count())->toBe(2)
        ->and(User::query()->orderBy('id')->pluck('id')->all())
        ->toBe($originalUserIds)
        ->and(Hash::check('537537', $lester->refresh()->password))->toBeTrue()
        ->and(Hash::check('1234', $anais->refresh()->password))->toBeTrue()
        ->and(Wallet::query()->count())->toBe(0);
});

it('uses the testing users as the default database bootstrap', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->orderBy('id')->pluck('email')->all())->toBe([
        'lbhurtado@gmail.com',
        'geckaanais17@gmail.com',
    ]);
});

it('allows each seeded user to authenticate with a local mobile number and PIN', function (
    string $mobile,
    string $email,
    string $pin,
) {
    $this->seed(TestingUserSeeder::class);
    $user = User::query()->where('email', $email)->sole();

    $response = $this->post(route('login.store'), [
        'mobile' => $mobile,
        'password' => $pin,
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect('/x/dashboard');
})->with([
    'Lester Hurtado' => ['09173011987', 'lbhurtado@gmail.com', '537537'],
    'Anaïs Santos' => ['09467438575', 'geckaanais17@gmail.com', '1234'],
]);

it('refuses to seed known testing credentials outside local and testing', function (string $environment) {
    config()->set('app.env', $environment);

    expect(fn () => $this->seed(TestingUserSeeder::class))
        ->toThrow(
            LogicException::class,
            'Testing users may only be seeded locally or while testing.',
        )
        ->and(User::query()->count())->toBe(0);
})->with(['production', 'staging']);
