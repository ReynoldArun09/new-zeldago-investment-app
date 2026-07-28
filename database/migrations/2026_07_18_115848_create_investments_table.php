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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('trx_id')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('contribution_amount', 15, 2)->default(0);
            $table->string('contribution_frequency')->default('None');
            $table->decimal('total_return', 15, 2)->default(0);
            $table->string('type')->default('INITIAL');
            $table->string('status')->default('PENDING');
            $table->string('payment_proof')->nullable();
            $table->timestamp('roi_cycle_start_date')->useCurrent();
            $table->timestamp('next_roi_date')->nullable();
            $table->integer('duration_months')->default(12);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
