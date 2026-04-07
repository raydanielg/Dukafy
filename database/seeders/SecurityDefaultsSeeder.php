<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecurityDefaultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('security_settings')->updateOrInsert(
            ['id' => 1],
            [
                'two_factor_enabled' => false,
                'session_timeout_minutes' => 60,
                'max_failed_attempts' => 5,
                'lockout_minutes' => 15,
                'password_min_length' => 8,
                'password_require_uppercase' => true,
                'password_require_lowercase' => true,
                'password_require_number' => true,
                'password_require_symbol' => false,
                'password_expire_days' => null,
                'password_history_count' => null,
                'database_encryption_enabled' => false,
                'backup_encryption_enabled' => false,
                'backup_password' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('data_retention_policies')->updateOrInsert(
            ['id' => 1],
            [
                'sales_days' => 730,
                'logs_days' => 90,
                'login_history_days' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('security_alert_settings')->updateOrInsert(
            ['id' => 1],
            [
                'email_on_new_location_login' => false,
                'whatsapp_on_password_change' => false,
                'alert_on_backup_failure' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
