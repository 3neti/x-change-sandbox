<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LBHurtado\XChange\Support\Auth\MobileNumber;
use LogicException;

class TestingUserSeeder extends Seeder
{
    private const array USERS = [
        [
            'name' => 'Lester Hurtado',
            'mobile' => '09173011987',
            'email' => 'lbhurtado@gmail.com',
            'pin' => '537537',
        ],
        [
            'name' => 'Anaïs Santos',
            'mobile' => '09467438575',
            'email' => 'geckaanais17@gmail.com',
            'pin' => '1234',
        ],
        [
            'name' => 'Amelia Hurtado',
            'mobile' => '09285243656',
            'email' => 'amelia.hurtado@example.test',
            'pin' => '317537',
        ],
        [
            'name' => 'Michael Kenneth Mauleon',
            'mobile' => '09170008172',
            'email' => 'michael.mauleon@example.test',
            'pin' => '1972',
        ],
    ];

    public function run(): void
    {
        if (
            ! app()->environment(['local', 'testing'])
            || ! in_array(
                mb_strtolower((string) config('app.env')),
                ['local', 'testing'],
                true,
            )
        ) {
            throw new LogicException('Testing users may only be seeded locally or while testing.');
        }

        foreach (self::USERS as $testingUser) {
            $mobile = MobileNumber::normalize($testingUser['mobile']);

            if (! is_string($mobile)) {
                throw new LogicException(
                    "Testing user [{$testingUser['email']}] has an invalid mobile number.",
                );
            }

            $user = User::query()->firstOrNew(['mobile' => $mobile]);

            $user->fill([
                'name' => $testingUser['name'],
                'email' => $testingUser['email'],
                'password' => Hash::make($testingUser['pin']),
            ]);
            $user->mobile_verified_at ??= now();
            $user->email_verified_at ??= now();
            $user->save();
        }
    }
}
