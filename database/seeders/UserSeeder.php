<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@streamyard.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Test Host',
            'email' => 'host@streamyard.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
