<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'user_limit' => 1,
                'product_limit' => 50,
                'trial_days' => 0,
                'features' => json_encode(['POS', 'Products', 'Basic Reports']),
                'active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price_monthly' => 15000,
                'price_yearly' => 150000,
                'user_limit' => 3,
                'product_limit' => 500,
                'trial_days' => 14,
                'features' => json_encode(['POS', 'Inventory Alerts', 'Advanced Reports', 'WhatsApp Invoice']),
                'active' => true,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'price_monthly' => 35000,
                'price_yearly' => 350000,
                'user_limit' => 10,
                'product_limit' => null,
                'trial_days' => 14,
                'features' => json_encode(['Multi-User', 'Multi-Branch', 'Priority Support']),
                'active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price_monthly' => 100000,
                'price_yearly' => 1000000,
                'user_limit' => null,
                'product_limit' => null,
                'trial_days' => 30,
                'features' => json_encode(['API Access', 'Custom Development', 'Dedicated Support']),
                'active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['slug' => $plan['slug']],
                $plan + ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
