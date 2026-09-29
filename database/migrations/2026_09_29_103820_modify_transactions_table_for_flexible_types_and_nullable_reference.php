<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // 1. Drop the unique index on reference_number first so nulls or duplicates during pending don't crash
            $table->dropUnique(['reference_number']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            // 2. Make reference_number nullable and string
            $table->string('reference_number')->nullable()->change();

            // 3. Change type from enum to string for flexibility
            $table->string('type')->change();

            // 4. Optional: Add helpful tracking metadata columns if you want extra resilience
            if (!Schema::hasColumn('transactions', 'merchant_request_id')) {
                $table->string('merchant_request_id')->nullable()->after('checkout_request_id');
            }
            if (!Schema::hasColumn('transactions', 'receipt_number')) {
                $table->string('receipt_number')->nullable()->after('reference_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('reference_number')->nullable(false)->unique()->change();
            $table->enum('type', ['registration_fee', 'wallet_topup', 'benevolence_contribution'])->change();
            
            if (Schema::hasColumn('transactions', 'merchant_request_id')) {
                $table->dropColumn('merchant_request_id');
            }
            if (Schema::hasColumn('transactions', 'receipt_number')) {
                $table->dropColumn('receipt_number');
            }
        });
    }
};