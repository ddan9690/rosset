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
        Schema::create('benevolence_contributions', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys linking to the case, member, and optional automated transaction
            $table->foreignId('benevolence_case_id')->constrained('benevolence_cases')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            
            // Financial details
            $table->unsignedBigInteger('amount'); 
            $table->string('payment_channel')->nullable();
            $table->string('reference_number')->unique()->nullable(); // M-Pesa receipt code or bank reference
            
            // Audit and notes
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            
            // Optional: Index for faster lookups per case and member
            $table->index(['benevolence_case_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benevolence_contributions');
    }
};