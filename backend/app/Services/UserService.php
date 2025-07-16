<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * User Service
 * 
 * Handles user-related operations
 */
class UserService
{
    /**
     * Update user profile
     * 
     * @param User $user
     * @param array $data
     * @return array
     */
    public function updateProfile(User $user, array $data): array
    {
        try {
            // Check if email is being changed and already exists
            if (isset($data['email']) && $data['email'] !== $user->email) {
                if (User::where('email', $data['email'])->where('id', '!=', $user->id)->exists()) {
                    return [
                        'success' => false,
                        'message' => 'Email đã được sử dụng',
                        'errors' => ['email' => ['Email đã được sử dụng']]
                    ];
                }
            }

            // Update user data
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'address' => $data['address'] ?? $user->address,
                'occupation' => $data['occupation'] ?? $user->occupation,
                'monthly_income' => $data['monthlyIncome'] ?? $user->monthly_income,
            ]);

            return [
                'success' => true,
                'user' => $user->fresh()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Cập nhật thông tin thất bại'
            ];
        }
    }

    /**
     * Change user password
     * 
     * @param User $user
     * @param array $data
     * @return array
     */
    public function changePassword(User $user, array $data): array
    {
        try {
            // Verify current password
            if (!Hash::check($data['currentPassword'], $user->password)) {
                return [
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không chính xác'
                ];
            }

            // Update password
            $user->update([
                'password' => Hash::make($data['newPassword'])
            ]);

            return [
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Đổi mật khẩu thất bại'
            ];
        }
    }

    /**
     * Upload user avatar
     * 
     * @param User $user
     * @param UploadedFile $file
     * @return array
     */
    public function uploadAvatar(User $user, UploadedFile $file): array
    {
        try {
            // Validate file
            if (!$file->isValid()) {
                return [
                    'success' => false,
                    'message' => 'File không hợp lệ'
                ];
            }

            // Check file size (max 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                return [
                    'success' => false,
                    'message' => 'File quá lớn (tối đa 5MB)'
                ];
            }

            // Check file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return [
                    'success' => false,
                    'message' => 'Chỉ chấp nhận file JPG, JPEG, PNG'
                ];
            }

            // Delete old avatar if exists
            if ($user->avatar_url) {
                $oldPath = str_replace('/storage/', '', $user->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            // Generate unique filename
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $path = $file->storeAs('avatars', $filename, 'public');
            $avatarUrl = '/storage/' . $path;

            // Update user avatar
            $user->update(['avatar_url' => $avatarUrl]);

            return [
                'success' => true,
                'avatar_url' => $avatarUrl
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Upload ảnh đại diện thất bại'
            ];
        }
    }
}