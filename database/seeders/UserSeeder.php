<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Enums\RoleEnum;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Brian',
            'last_name' => 'Kaleli',
            'phone_local_number' => '795083961',
            'phone_country_code' => '+1',
            'email' => 'briankaleli@gmail.com',
            'email_verified_at' => now(),
            'firm' => null,
            'branch' => null,
            'password' => Hash::make('12345678'),
            'user_type' => RoleEnum::admin->value,
        ]);

        User::create([
            'first_name' => 'Victor',
            'last_name' => 'Muasya',
            'phone_local_number' => '795083960',
            'phone_country_code' => '+1',
            'email' => 'vicmwe184@gmail.com',
            'email_verified_at' => now(),
            'firm' => null,
            'branch' => null,
            'password' => Hash::make('12345678'),
            'user_type' => RoleEnum::admin->value,
        ]);

        User::create([
            'first_name' => 'Joshua',
            'last_name' => 'Mutua',
            'phone_local_number' => '795083962',
            'phone_country_code' => '+1',
            'email' => 'joshuamutua@gmail.com',
            'email_verified_at' => now(),
            'firm' => null,
            'branch' => null,
            'password' => Hash::make('12345678'),
            'user_type' => RoleEnum::admin->value,
        ]);

        User::create([
            'first_name' => 'Firm',
            'last_name' => 'Owner',
            'phone_local_number' => '795083963',
            'phone_country_code' => '+1',
            'email' => 'firm_owner@gmail.com',
            'email_verified_at' => now(),
            'firm' => 1,
            'branch' => null,
            'password' => Hash::make('12345678'),
            'user_type' => RoleEnum::store_owner->value,
        ]);

        User::create([
            'first_name' => 'Firm',
            'last_name' => 'Owner',
            'phone_local_number' => '795083964',
            'phone_country_code' => '+1',
            'email' => 'sales_manager@gmail.com',
            'email_verified_at' => now(),
            'firm' => null,
            'branch' => 1,
            'password' => Hash::make('12345678'),
            'user_type' => RoleEnum::sales->value,
        ]);
    }
}
