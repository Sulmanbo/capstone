<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Per-subject DepEd grade weights. NULL means use the global config value.
            // Values are stored as percentages (e.g. 40.0 = 40%). Must sum to 100 when set.
            $table->decimal('ww_weight', 5, 2)->nullable()->after('credits')
                  ->comment('Written Works weight %. NULL = use global config (default 30%).');
            $table->decimal('pt_weight', 5, 2)->nullable()->after('ww_weight')
                  ->comment('Performance Tasks weight %. NULL = use global config (default 50%).');
            $table->decimal('qa_weight', 5, 2)->nullable()->after('pt_weight')
                  ->comment('Quarterly Assessment weight %. NULL = use global config (default 20%).');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['ww_weight', 'pt_weight', 'qa_weight']);
        });
    }
};
