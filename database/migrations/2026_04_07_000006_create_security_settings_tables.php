<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();

            // Login Security
            $table->boolean('two_factor_enabled')->default(false);
            $table->integer('session_timeout_minutes')->nullable();
            $table->integer('max_failed_attempts')->default(5);
            $table->integer('lockout_minutes')->default(15);

            // Password Policy
            $table->integer('password_min_length')->default(8);
            $table->boolean('password_require_uppercase')->default(true);
            $table->boolean('password_require_lowercase')->default(true);
            $table->boolean('password_require_number')->default(true);
            $table->boolean('password_require_symbol')->default(false);
            $table->integer('password_expire_days')->nullable();
            $table->integer('password_history_count')->nullable();

            // Database Encryption (placeholder config flag)
            $table->boolean('database_encryption_enabled')->default(false);

            // Backup Security
            $table->boolean('backup_encryption_enabled')->default(false);
            $table->string('backup_password')->nullable();

            $table->timestamps();
        });

        Schema::create('ip_whitelists', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('label')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key_hash');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('data_retention_policies', function (Blueprint $table) {
            $table->id();
            $table->integer('sales_days')->nullable();
            $table->integer('logs_days')->nullable();
            $table->integer('login_history_days')->nullable();
            $table->timestamps();
        });

        Schema::create('security_alert_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('email_on_new_location_login')->default(false);
            $table->boolean('whatsapp_on_password_change')->default(false);
            $table->boolean('alert_on_backup_failure')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_alert_settings');
        Schema::dropIfExists('data_retention_policies');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('blocked_ips');
        Schema::dropIfExists('ip_whitelists');
        Schema::dropIfExists('security_settings');
    }
};
