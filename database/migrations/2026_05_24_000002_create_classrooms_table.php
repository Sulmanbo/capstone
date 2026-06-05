<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Classrooms are a separate manageable entity per the adviser's feedback.
 * Each classroom is scoped to an academic year so the registrar can manage
 * room rosters per year without leaking historical state.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->string('room_name', 50);          // e.g. "Room 101", "Lab A"
            $table->string('building', 50)->nullable();
            $table->integer('capacity')->default(40);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Same room name twice in the same year is meaningless
            $table->unique(['academic_year_id', 'room_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
