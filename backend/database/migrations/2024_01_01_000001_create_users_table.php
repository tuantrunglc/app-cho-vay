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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('username')->unique()->nullable(); // For admin users
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('id_card')->unique()->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->enum('role', ['customer', 'admin'])->default('customer');
            $table->enum('status', ['active', 'blocked', 'pending_verification'])->default('pending_verification');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->integer('credit_score')->default(0);
            $table->string('avatar_url')->nullable();
            $table->json('permissions')->nullable(); // For admin users
            $table->timestamp('last_activity')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            // Indexes
            $table->index(['role', 'status']);
            $table->index('verification_status');
            $table->index('phone');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};