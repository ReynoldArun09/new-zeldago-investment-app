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
            $table->string('payment_method')->nullable()->after('status');
            $table->string('payment_trx_id')->nullable()->after('payment_method');
            $table->string('payment_proof')->nullable()->after('payment_trx_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roi_logs', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_trx_id', 'payment_proof']);
        });
    }
};
