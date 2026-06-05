<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // When a student drops or withdraws mid-quarter, faculty records it here.
            // A grade with dropped_at set is excluded from averages and aggregate reports.
            $table->timestamp('dropped_at')->nullable()->after('remarks');
            $table->text('drop_reason')->nullable()->after('dropped_at');
            $table->unsignedBigInteger('dropped_by')->nullable()->after('drop_reason');
            $table->foreign('dropped_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['dropped_by']);
            $table->dropColumn(['dropped_at', 'drop_reason', 'dropped_by']);
        });
    }
};
