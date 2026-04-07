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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->nullable();
            $table->string('public_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->nullable();
            $table->string('tin')->nullable();
            $table->string('address')->nullable();
            $table->string('currency')->default('TZS');
            $table->string('prefix')->nullable();
            $table->timestamps();
        });

        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('vat_percent')->default(18);
            $table->boolean('vat_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('amount');
            $table->string('category')->nullable();
            $table->date('spent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['spent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('tax_settings');
        Schema::dropIfExists('invoice_settings');
        Schema::dropIfExists('payment_gateway_settings');
        Schema::dropIfExists('payment_methods');
    }
};
