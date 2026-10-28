<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solidarity_funds', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('total_deductions');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete()->after('notes');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('recorded_by');
        });
    }

    public function down(): void
    {
        Schema::table('solidarity_funds', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['notes', 'recorded_by', 'updated_by']);
        });
    }
};