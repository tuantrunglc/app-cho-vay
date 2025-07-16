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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['loan_approved', 'loan_rejected', 'payment_reminder', 'payment_overdue', 'system', 'promotion']);
            $table->boolean('read')->default(false);
            $table->json('data')->nullable(); // Additional data for the notification
            $table->enum('channel', ['database', 'email', 'sms', 'push'])->default('database');
            $table->boolean('sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'read']);
            $table->index(['user_id', 'type']);
            $table->index('type');
            $table->index('sent');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};