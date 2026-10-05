<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // Fees stored as full integer digits (e.g., KES)
            $table->unsignedInteger('registration_fee')->default(150);
            $table->unsignedInteger('agm_contribution_fee')->default(150);
            
            // Annual Deadline Configuration (End of February)
            $table->unsignedTinyInteger('registration_deadline_month')->default(2); // February
            $table->unsignedTinyInteger('registration_deadline_day')->default(28);
            
            // Waiting Periods (in days)
            $table->unsignedInteger('late_registration_waiting_period_days')->default(30);
            $table->unsignedInteger('defaulting_waiting_period_days')->default(90);
            
            $table->timestamps();
        });

        // Insert default initial row
        DB::table('settings')->insert([
            'registration_fee' => 150,
            'agm_contribution_fee' => 150,
            'registration_deadline_month' => 2,
            'registration_deadline_day' => 28,
            'late_registration_waiting_period_days' => 30,
            'defaulting_waiting_period_days' => 90,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};