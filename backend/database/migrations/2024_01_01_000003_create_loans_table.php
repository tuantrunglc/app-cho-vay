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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('loan_id')->unique(); // Same as loan_application.loan_id
            $table->decimal('original_amount', 15, 2);
            $table->decimal('remaining_amount', 15, 2);
            $table->integer('term'); // in months
            $table->integer('remaining_term');
            $table->decimal('interest_rate', 5, 2);
            $table->decimal('monthly_payment', 15, 2);
            $table->date('start_date');
            $table->date('next_payment_date');
            $table->enum('status', ['active', 'completed', 'overdue', 'defaulted'])->default('active');
            $table->boolean('is_overdue')->default(false);
            $table->integer('days_past_due')->default(0);
            $table->decimal('penalty_amount', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->json('payment_schedule')->nullable(); // Array of payment schedule
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('is_overdue');
            $table->index('next_payment_date');
            $table->index('loan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};