<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique(); // KCB transaction reference
            $table->decimal('amount', 12, 2);
            $table->string('type'); // e.g., credit, debit
            $table->string('channel'); // e.g., KCB Paybill / STK IPN
            $table->string('account_identifier'); // e.g., 7936435#121
            $table->string('phone_number')->nullable();
            $table->string('description')->nullable();
            $table->string('status')->default('Completed');
            $table->json('raw_payload')->nullable(); // Full JSON response/callback from KCB
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_ledgers');
    }
};