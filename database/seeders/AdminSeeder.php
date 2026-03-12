<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin user
        User::updateOrCreate(
            ['email' => 'admin@phongtro.com'],
            [
                'name'     => 'Super Admin',
                'phone'    => '0901234567',
                'password' => Hash::make('password'),
                'role'     => 'super_admin',
            ]
        );

        // Create a sample landlord
        User::updateOrCreate(
            ['email' => 'landlord@gmail.com'],
            [
                'name'     => 'Chủ Trọ A',
                'phone'    => '0988888888',
                'password' => Hash::make('password'),
                'role'     => 'landlord',
            ]
        );

        // Default settings
        $defaults = [
            'site_name'                 => 'Nhà Trọ Quản Lý',
            'site_address'              => '123 Đường Lê Lợi, TP.HCM',
            'site_phone'                => '0901234567',
            'default_electricity_price' => '3500',
            'default_water_price'       => '15000',
            'vietqr_bank_id'            => 'MB',
            'vietqr_account_no'         => '0123456789',
            'momo_number'               => '0901234567',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
