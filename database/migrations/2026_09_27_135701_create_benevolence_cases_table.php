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
        Schema::create('benevolence_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('slug')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            $table->foreignId('benevolence_category_id')->constrained()->cascadeOnDelete();
            $table->text('case_details');
            $table->date('deadline');
            $table->enum('status', ['active', 'closed', 'suspended'])->default('active');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benevolence_cases');
    }
};
