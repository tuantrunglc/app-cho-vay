<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Document Model
 * 
 * Represents uploaded documents for loan applications
 */
class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'loan_application_id',
        'filename',
        'original_filename',
        'file_path',
        'file_url',
        'type',
        'mime_type',
        'file_size',
        'status',
        'verification_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Generate unique document ID
     */
    public static function generateDocumentId(): string
    {
        $lastDoc = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastDoc ? (int)substr($lastDoc->document_id, 3) + 1 : 1;
        return 'DOC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate document_id
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($document) {
            if (empty($document->document_id)) {
                $document->document_id = self::generateDocumentId();
            }
        });
    }

    /**
     * Get the user that owns the document
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the loan application this document belongs to
     */
    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * Check if document is verified
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Check if document is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if document is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Verify the document
     */
    public function verify(string $verifiedBy, string $notes = null): void
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Reject the document
     */
    public function reject(string $verifiedBy, string $notes): void
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get document type label
     */
    public function getTypeLabel(): string
    {
        $labels = [
            'id_card_front' => 'CMND/CCCD mặt trước',
            'id_card_back' => 'CMND/CCCD mặt sau',
            'income_proof' => 'Chứng minh thu nhập',
            'bank_statement' => 'Sao kê ngân hàng',
            'avatar' => 'Ảnh đại diện',
            'other' => 'Khác',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    /**
     * Scope for verified documents
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope for pending documents
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for rejected documents
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for specific document type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}