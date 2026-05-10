<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curriculum_mappings', function (Blueprint $table) {
            // The subject a student must have passed (any prior year) to take this subject
            $table->foreignId('prerequisite_subject_id')
                ->nullable()
                ->after('subject_id')
                ->constrained('subjects')
                ->nullOnDelete();

            // Minimum passing grade required (defaults to system passing grade)
            $table->decimal('prerequisite_min_grade', 5, 2)
                ->default(75.00)
                ->after('prerequisite_subject_id');
        });
    }

    public function down(): void
    {
        Schema::table('curriculum_mappings', function (Blueprint $table) {
            $table->dropForeign(['prerequisite_subject_id']);
            $table->dropColumn(['prerequisite_subject_id', 'prerequisite_min_grade']);
        });
    }
};
