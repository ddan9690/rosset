<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add soft deletes to categories and cases
        Schema::table('benevolence_categories', function (Blueprint $table) {$table->softDeletes();
        });

        Schema::table('benevolence_cases', function (Blueprint $table) {$table->softDeletes();
            
            // Drop old cascade foreign key for category
            $table->dropForeign(['benevolence_category_id']);
            
            // Re-add with nullOnDelete so deleting a category doesn't wipe out cases
            $table->foreignId('benevolence_category_id')
                  ->nullable()
                  ->change();
            
            $table->foreign('benevolence_category_id')
                  ->references('id')
                  ->on('benevolence_categories')
                  ->nullOnDelete();
        });

        // 2. Link transactions safely to benevolence cases (if you track case_id or case_number)
        // If transactions link via benevolence_case_id, update foreign key constraints here.
        // If you are currently tracking via `case_number` string, consider adding a nullable foreignId:
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'benevolence_case_id')) {
                $table->foreignId('benevolence_case_id')
                      ->nullable()
                      ->after('case_number')
                      ->constrained('benevolence_cases')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('benevolence_cases', function (Blueprint $table) {$table->dropSoftDeletes();
            $table->dropForeign(['benevolence_category_id']);$table->foreign('benevolence_category_id')
                  ->references('id')
                  ->on('benevolence_categories')
                  ->cascadeOnDelete();
        });

        Schema::table('benevolence_categories', function (Blueprint $table) {$table->dropSoftDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'benevolence_case_id')) {
                $table->dropForeign(['benevolence_case_id']);$table->dropColumn('benevolence_case_id');
            }
        });
    }
};