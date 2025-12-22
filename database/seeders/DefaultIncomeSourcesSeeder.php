<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncomeSource;
use App\Models\User;

class DefaultIncomeSourcesSeeder extends Seeder
{
    public function run()
    {
        $user = User::first(); // Assuming we are seeding for the first user (Super Admin)

        if (!$user) {
            return;
        }

        $sources = [
            'Active Income' => [
                'Salary',
                'Overtime pay',
                'Freelancing',
                'Commissions & bonuses',
                'Professional services',
            ],
            'Passive Income' => [
                'Rental income',
                'Dividends from shares',
                'Interest from savings, SACCOs, fixed deposits',
                'Royalties (books, music, software, academic content)',
                'REITs (Real Estate Investment Trusts)',
            ],
            'Investment Income' => [
                'Stocks & ETFs',
                'Bonds & treasury bills',
                'Mutual funds',
                'Cryptocurrency staking',
                'Private equity or angel investing',
            ],
            'Business Income' => [
                'Small businesses',
                'Online stores',
                'Startups',
                'Franchises',
                'Consulting firms',
            ],
            'Informal & Side Hustles' => [
                'Farming & livestock',
                'Ride-hailing (Uber/Bolt/Boda)',
                'Content creation',
                'Event hosting & MC gigs',
                'Photography & videography',
            ],
            'Other Income Sources' => [
                'Grants & scholarships',
                'Pensions',
                'Allowances & stipends',
                'Gifts & inheritances',
            ],
        ];

        foreach ($sources as $type => $names) {
            foreach ($names as $name) {
                IncomeSource::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $name
                    ],
                    [
                        'type' => $type,
                        'linked_account' => null
                    ]
                );
            }
        }
    }
}
