<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceDefaultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_methods')->updateOrInsert(
            ['slug' => 'mpesa'],
            ['name' => 'M-Pesa', 'enabled' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('payment_methods')->updateOrInsert(
            ['slug' => 'bank'],
            ['name' => 'Bank Transfer', 'enabled' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('payment_methods')->updateOrInsert(
            ['slug' => 'cash'],
            ['name' => 'Cash', 'enabled' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('payment_gateway_settings')->updateOrInsert(
            ['id' => 1],
            ['provider' => null, 'public_key' => null, 'secret_key' => null, 'enabled' => false, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('invoice_settings')->updateOrInsert(
            ['id' => 1],
            ['business_name' => 'Dukafy', 'tin' => null, 'address' => null, 'currency' => 'TZS', 'prefix' => 'INV', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('tax_settings')->updateOrInsert(
            ['id' => 1],
            ['vat_percent' => 18, 'vat_enabled' => true, 'created_at' => now(), 'updated_at' => now()]
        );
    }
}
