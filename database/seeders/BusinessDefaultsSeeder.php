<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Retail Shop', 'slug' => 'retail', 'modules' => json_encode(['pos', 'products', 'customers', 'reports'])],
            ['name' => 'Pharmacy', 'slug' => 'pharmacy', 'modules' => json_encode(['pos', 'products', 'reports'])],
            ['name' => 'Restaurant', 'slug' => 'restaurant', 'modules' => json_encode(['pos', 'products', 'reports'])],
            ['name' => 'Wholesale', 'slug' => 'wholesale', 'modules' => json_encode(['pos', 'products', 'reports'])],
        ];

        foreach ($types as $t) {
            DB::table('business_types')->updateOrInsert(
                ['slug' => $t['slug']],
                $t + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $retailId = DB::table('business_types')->where('slug', 'retail')->value('id');

        DB::table('businesses')->updateOrInsert(
            ['slug' => 'default-business'],
            [
                'business_type_id' => $retailId,
                'name' => 'Default Business',
                'phone' => null,
                'email' => null,
                'address' => null,
                'currency' => 'TZS',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $businessId = DB::table('businesses')->where('slug', 'default-business')->value('id');

        DB::table('branches')->updateOrInsert(
            ['business_id' => $businessId, 'slug' => 'main'],
            [
                'name' => 'Main Branch',
                'address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $branchId = DB::table('branches')->where('business_id', $businessId)->where('slug', 'main')->value('id');

        DB::table('users')->whereNull('business_id')->update([
            'business_id' => $businessId,
            'branch_id' => $branchId,
            'updated_at' => now(),
        ]);
    }
}
