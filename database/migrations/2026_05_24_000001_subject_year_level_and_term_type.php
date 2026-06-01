<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adviser feedback: subjects need a year-level tag so the assignment form
 * can filter by section grade. Academic years need a term type so the
 * registrar can switch the institution between quarterly and semestral.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Nullable to keep existing rows valid; new subjects should require it.
            $table->string('year_level', 20)->nullable()->after('subject_name');
            $table->index('year_level');
        });

        Schema::table('academic_years', function (Blueprint $table) {
            // 'quarterly' = 4 quarters, 'semestral' = 2 semesters
            $table->enum('term_type', ['quarterly', 'semestral'])
                  ->default('quarterly')
                  ->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex(['year_level']);
            $table->dropColumn('year_level');
        });

        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn('term_type');
        });
    }
};
