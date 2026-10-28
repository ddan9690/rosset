<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agm_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            
            $table->integer('year'); // e.g. 2026, 2027
            $table->decimal('amount', 10, 2);
            $table->string('slug')->unique(); 
            $table->string('payment_channel')->default('manual_admin'); // 'manual_admin', 'stk_push', 'c2b_paybill'
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('success');
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agm_payments');
    }
};