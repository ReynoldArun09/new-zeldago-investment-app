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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('role')->default('INVESTOR')->after('email');
            $table->boolean('is_active')->default(true)->after('password');
            $table->string('kyc_status')->default('UNVERIFIED')->after('is_active');
            $table->decimal('wallet_balance', 15, 2)->default(0.00)->after('kyc_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'is_active', 'kyc_status', 'wallet_balance']);
        });
    }
};
