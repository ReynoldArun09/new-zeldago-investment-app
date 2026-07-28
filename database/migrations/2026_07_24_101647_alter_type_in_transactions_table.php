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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE transactions MODIFY type VARCHAR(255) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this might cause data loss if there are 'ROI' entries.
        // It's safer to leave as string or manually handle it.
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE transactions MODIFY type ENUM('commission', 'investment', 'withdrawal') NOT NULL");
    }
};
