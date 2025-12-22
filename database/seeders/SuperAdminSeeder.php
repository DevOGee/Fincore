<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'gee.mwerevu@gmail.com'],
            [
                'name' => 'Gee Mwerevu',
                'password' => Hash::make('StrongPassword123!'),
                'email_verified_at' => now(),
            ]
        );
    }
}
