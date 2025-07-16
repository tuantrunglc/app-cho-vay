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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_id')->unique(); // DOC001, DOC002, etc.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_application_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_url');
            $table->enum('type', ['id_card_front', 'id_card_back', 'income_proof', 'bank_statement', 'avatar', 'other']);
            $table->string('mime_type');
            $table->integer('file_size'); // in bytes
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'type']);
            $table->index('loan_application_id');
            $table->index('status');
            $table->index('document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};