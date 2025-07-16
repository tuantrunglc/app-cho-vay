<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * File Helper
 * 
 * Provides file handling utilities
 */
class FileHelper
{
    /**
     * Upload file to storage
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @return array
     */
    public static function upload(UploadedFile $file, string $directory = 'uploads', string $disk = 'public'): array
    {
        try {
            // Generate unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $path = $file->storeAs($directory, $filename, $disk);
            
            return [
                'success' => true,
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'path' => $path,
                'url' => Storage::disk($disk)->url($path),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Upload file thất bại: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete file from storage
     * 
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function delete(string $path, string $disk = 'public'): bool
    {
        try {
            return Storage::disk($disk)->delete($path);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get file size in human readable format
     * 
     * @param int $bytes
     * @return string
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Validate file type
     * 
     * @param UploadedFile $file
     * @param array $allowedTypes
     * @return bool
     */
    public static function validateFileType(UploadedFile $file, array $allowedTypes): bool
    {
        return in_array($file->getClientOriginalExtension(), $allowedTypes);
    }

    /**
     * Validate file size
     * 
     * @param UploadedFile $file
     * @param int $maxSize (in KB)
     * @return bool
     */
    public static function validateFileSize(UploadedFile $file, int $maxSize): bool
    {
        return $file->getSize() <= ($maxSize * 1024);
    }

    /**
     * Generate document ID
     * 
     * @return string
     */
    public static function generateDocumentId(): string
    {
        return 'DOC' . date('Ymd') . strtoupper(Str::random(6));
    }
}