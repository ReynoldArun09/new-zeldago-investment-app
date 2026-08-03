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
        Schema::create('admin_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('target'); // 'all', 'investors', 'agents', 'specific'
            $table->unsignedBigInteger('target_user_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->timestamps();
            
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notification_logs');
    }
};
