<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Transaction identification
            $table->string('reference_number')->unique(); // e.g., KCB Merchant/Transaction Ref or Receipt No
            $table->string('checkout_request_id')->nullable(); // M-Pesa / STK push tracking ID
            
            // Categorization
            $table->enum('type', ['registration_fee', 'wallet_topup', 'benevolence_contribution']);
            $table->string('case_number')->nullable(); // Linked benevolence case if applicable
            
            // Financial details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('KES');
            
            // Status tracking
            $table->enum('status', ['pending', 'success', 'failed', 'reversed'])->default('pending');
            $table->string('phone_number'); // Number charged
            
            // Gateway responses & Metadata
            $table->text('description')->nullable();
            $table->json('gateway_response')->nullable(); // Store raw IPN or API response JSON
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};