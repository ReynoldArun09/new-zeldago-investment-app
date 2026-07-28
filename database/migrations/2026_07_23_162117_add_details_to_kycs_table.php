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
        Schema::table('kycs', function (Blueprint $table) {
            if (!Schema::hasColumn('kycs', 'document_number')) {
                $table->string('document_number')->nullable()->after('document_type');
            }
            if (Schema::hasColumn('kycs', 'document_proof')) {
                $table->renameColumn('document_proof', 'document_front_proof');
            }
        });
        
        Schema::table('kycs', function (Blueprint $table) {
            if (!Schema::hasColumn('kycs', 'document_back_proof')) {
                $table->string('document_back_proof')->nullable()->after('document_front_proof');
            }
            if (!Schema::hasColumn('kycs', 'country')) {
                $table->string('country')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('kycs', 'address')) {
                $table->text('address')->nullable()->after('country');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kycs', function (Blueprint $table) {
            $table->dropColumn(['document_number', 'document_back_proof', 'country', 'address']);
            $table->renameColumn('document_front_proof', 'document_proof');
        });
    }
};
