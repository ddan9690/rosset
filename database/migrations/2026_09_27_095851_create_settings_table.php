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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('registration_fee')->nullable(); // Annual membership registration fee charged to new users (full digits)
            $table->unsignedInteger('agm_contribution_fee')->nullable(); // Annual General Meeting contribution fee per member (full digits)
            $table->unsignedInteger('registration_deadline_month')->nullable(); // Month limit for annual registration (e.g., 2 for February)
            $table->unsignedInteger('registration_deadline_day')->nullable(); // Day limit for annual registration (e.g., 28 for 28th February)
            $table->unsignedInteger('late_registration_waiting_period_days')->nullable(); // Waiting period in days before benefiting if registered after deadline 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
