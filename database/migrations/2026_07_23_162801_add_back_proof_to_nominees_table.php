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
        Schema::table('nominees', function (Blueprint $table) {
            if (Schema::hasColumn('nominees', 'identity_proof')) {
                $table->renameColumn('identity_proof', 'identity_front_proof');
            }
        });
        
        Schema::table('nominees', function (Blueprint $table) {
            if (!Schema::hasColumn('nominees', 'identity_back_proof')) {
                $table->string('identity_back_proof')->nullable()->after('identity_front_proof');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nominees', function (Blueprint $table) {
            $table->dropColumn('identity_back_proof');
            $table->renameColumn('identity_front_proof', 'identity_proof');
        });
    }
};
