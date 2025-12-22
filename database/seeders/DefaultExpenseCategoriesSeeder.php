<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class DefaultExpenseCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1; // Main user

        $categories = [
            // Housing & Utilities (Fixed)
            ['name' => 'Rent / Mortgage', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'Electricity', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Water', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Garbage Collection', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'Internet', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'TV Subscription', 'type' => 'fixed', 'is_essential' => false],
            ['name' => 'House Maintenance & Repairs', 'type' => 'variable', 'is_essential' => true],

            // Food & Household (Variable)
            ['name' => 'Groceries', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Cooking Gas / Charcoal', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Eating Out / Takeaways', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'Drinking Water', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Household Supplies', 'type' => 'variable', 'is_essential' => true],

            // Transport (Variable)
            ['name' => 'Public Transport', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Fuel', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Car Maintenance & Service', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Motor Insurance', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'Parking Fees', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'Ride-hailing (Uber, Bolt)', 'type' => 'variable', 'is_essential' => false],

            // Communication & Digital (Fixed)
            ['name' => 'Airtime', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Mobile Data', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Digital Subscriptions', 'type' => 'fixed', 'is_essential' => false],

            // Personal & Lifestyle (Variable)
            ['name' => 'Clothing & Shoes', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'Grooming', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'Gym & Fitness', 'type' => 'fixed', 'is_essential' => false],
            ['name' => 'Entertainment & Leisure', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'Hobbies', 'type' => 'variable', 'is_essential' => false],

            // Health & Wellness (Fixed/Variable)
            ['name' => 'Medical Cover (NHIF/Private)', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'Hospital Visits', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Medication', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Dental & Optical Care', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Mental Health Support', 'type' => 'variable', 'is_essential' => true],

            // Education & Professional Growth (Variable)
            ['name' => 'School Fees', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'Course Fees & Certifications', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'Books & Learning Materials', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'Conferences & Trainings', 'type' => 'variable', 'is_essential' => false],

            // Family & Social Obligations (Variable)
            ['name' => 'Family Support & Remittances', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Childcare & Nanny', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'Gifts, Weddings, Funerals', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'Harambees & Contributions', 'type' => 'variable', 'is_essential' => false],

            // Work-Related Expenses (Variable)
            ['name' => 'Work Tools (Laptop, Phone)', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Printing & Stationery', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'Professional Memberships', 'type' => 'fixed', 'is_essential' => false],

            // Financial Commitments (Fixed)
            ['name' => 'Loan Repayments', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'Credit Card / Mobile Loans', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'SACCO Contributions', 'type' => 'fixed', 'is_essential' => false],
            ['name' => 'Chama Contributions', 'type' => 'fixed', 'is_essential' => false],
            ['name' => 'Insurance Premiums', 'type' => 'fixed', 'is_essential' => true],

            // Savings & Investments (Fixed - non-negotiable)
            ['name' => 'Emergency Fund', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'Fixed Savings', 'type' => 'fixed', 'is_essential' => true],
            ['name' => 'Money Market Funds', 'type' => 'fixed', 'is_essential' => false],
            ['name' => 'Investment Top-ups', 'type' => 'variable', 'is_essential' => false],

            // Miscellaneous & Emergencies (Variable)
            ['name' => 'Unexpected Expenses', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Repairs & Replacements', 'type' => 'variable', 'is_essential' => true],
            ['name' => 'Fines & Penalties', 'type' => 'variable', 'is_essential' => false],
            ['name' => 'One-off Urgent Costs', 'type' => 'variable', 'is_essential' => true],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['user_id' => $userId, 'name' => $category['name']],
                [
                    'type' => $category['type'],
                    'is_essential' => $category['is_essential'],
                ]
            );
        }

        echo "Seeded " . count($categories) . " expense categories.\n";
    }
}
