<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SavingsLegacy;
use App\Models\User;
use Carbon\Carbon;

class SavingsLegacySeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        if (!$user) {
            $this->command->info('No user found to seed data for.');
            return;
        }

        $categories = ['Emergency Fund', 'Retirement', 'Wealth Building', 'Education'];
        $startDate = Carbon::now()->subMonths(12);

        for ($i = 0; $i < 12; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $month = $date->format('Y-m');
            $year = $date->year;
            $quarter = 'Q' . $date->quarter;

            foreach ($categories as $category) {
                // Simulate varying contributions
                $baseAmount = rand(5000, 15000);
                $amount = $baseAmount * (1 + (rand(-10, 20) / 100)); // +/- variation

                SavingsLegacy::create([
                    'user_id' => $user->id,
                    'category' => $category,
                    'amount' => $amount,
                    'percentage_applied' => 10.00, // Dummy percentage
                    'date' => $date->format('Y-m-d'),
                    'month' => $month,
                    'quarter' => $quarter,
                    'year' => $year,
                ]);
            }
        }

        $this->command->info('Savings Legacy data seeded successfully for user: ' . $user->email);
    }
}
