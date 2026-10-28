<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            
        $table->unsignedInteger('max_missed_benevolence_cases')->default(1)->after('defaulting_waiting_period_days');
         
        });

    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'max_missed_benevolence_cases')) {
                $table->dropColumn('max_missed_benevolence_cases');
            }
        });
    }
};