<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('document_type'); // cert_enrollment, good_moral, form137, transcript, diploma
            $table->string('purpose')->nullable();
            $table->integer('copies')->default(1);
            $table->string('status')->default('pending'); // pending, processing, ready, released, rejected
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('document_requests'); }
};
