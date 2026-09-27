<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unallocated_payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // KCB transaction reference
            $table->decimal('amount', 12, 2);
            $table->string('customer_reference'); // e.g., raw account number sent by member
            $table->string('customer_name')->nullable();
            $table->string('phone_number');
            $table->string('reason');
            $table->enum('status', ['pending', 'reassigned', 'refunded'])->default('pending');
            $table->json('raw_payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unallocated_payments');
    }
};