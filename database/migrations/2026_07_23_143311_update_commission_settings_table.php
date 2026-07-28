<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('commission_settings', function (Blueprint $table) {
            $table->dropColumn(['level_1', 'level_2', 'level_3', 'level_4']);
            $table->integer('level_count')->default(4);
            $table->json('commissions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_settings', function (Blueprint $table) {
            $table->dropColumn(['level_count', 'commissions']);
            $table->decimal('level_1', 5, 2)->default(0);
            $table->decimal('level_2', 5, 2)->default(0);
            $table->decimal('level_3', 5, 2)->default(0);
            $table->decimal('level_4', 5, 2)->default(0);
        });
    }
};
