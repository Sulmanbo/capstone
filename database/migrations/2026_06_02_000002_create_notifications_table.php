<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // e.g. 'message', 'grade_submitted', 'grade_verified', 'announcement'
            $table->string('title');
            $table->text('body');
            $table->string('related_type')->nullable(); // 'message', 'schedule', 'enrollment', etc.
            $table->unsignedBigInteger('related_id')->nullable(); // ID of the related record
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
