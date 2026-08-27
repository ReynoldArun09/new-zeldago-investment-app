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
        Schema::table('roi_logs', function (Blueprint $table) {
            $table->decimal('direct_roi_amount', 15, 2)->default(0)->after('rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roi_logs', function (Blueprint $table) {
            $table->dropColumn('direct_roi_amount');
        });
    }
};
