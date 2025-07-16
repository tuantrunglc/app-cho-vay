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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // TXN001, TXN002, etc.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdrawal', 'loan_disbursement', 'payment', 'refund', 'penalty']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('description');
            $table->json('metadata')->nullable(); // Additional transaction data
            $table->string('reference_id')->nullable(); // Related loan_id, payment_id, etc.
            $table->string('reference_type')->nullable(); // loan, payment, etc.
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'type']);
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
            $table->index('transaction_id');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};