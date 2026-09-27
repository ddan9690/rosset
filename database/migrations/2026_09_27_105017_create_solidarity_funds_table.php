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
        Schema::create('solidarity_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('balance')->default(0); // Current available virtual wallet balance
            $table->unsignedInteger('total_topups')->default(0); // Lifetime deposits via STK/paybill
            $table->unsignedInteger('total_deductions')->default(0); // Lifetime deductions for benevolence cases
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solidarity_funds');
    }
};
