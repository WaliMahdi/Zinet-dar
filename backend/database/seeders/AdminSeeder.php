<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'sedielectro@gmail.com'],
            [
                'first_name' => 'SEDI',
                'last_name' => 'Electro Admin',
                'username' => 'sediadmin',
                'password' => Hash::make('SediElectro123@'),
                'email_verified_at' => now(),
            ]
        );
    }
}
