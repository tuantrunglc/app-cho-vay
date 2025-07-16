<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\UploadAvatarRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * User Controller
 * 
 * Handles user profile management
 */
class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth:api');
    }

    /**
     * Get user profile
     * 
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'data' => new UserResource($user),
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => '1.0.0'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin profile',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Update user profile
     * 
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $result = $this->userService->updateProfile($user, $request->validated());

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => new UserResource($result['user']),
                'message' => 'Cập nhật thông tin thành công',
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật thông tin thất bại',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Change user password
     * 
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $result = $this->userService->changePassword($user, $request->validated());

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công',
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đổi mật khẩu thất bại',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Upload user avatar
     * 
     * @param UploadAvatarRequest $request
     * @return JsonResponse
     */
    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $result = $this->userService->uploadAvatar($user, $request->file('avatar'));

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'avatarUrl' => $result['avatar_url']
                ],
                'message' => 'Cập nhật ảnh đại diện thành công',
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload ảnh đại diện thất bại',
                'code' => 'SYS_003'
            ], 500);
        }
    }
}