<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Authentication Service
 * 
 * Handles user authentication logic
 */
class AuthService
{
    /**
     * Login user with credentials
     * 
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        try {
            // Find user by phone and role
            $user = User::where('phone', $credentials['phone'])
                       ->where('role', $credentials['role'])
                       ->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return [
                    'success' => false,
                    'message' => 'Thông tin đăng nhập không chính xác'
                ];
            }

            // Check if user is active
            if (!$user->isActive()) {
                return [
                    'success' => false,
                    'message' => 'Tài khoản đã bị khóa hoặc chưa được kích hoạt'
                ];
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);
            $refreshToken = $this->generateRefreshToken($user);

            // Update last activity
            $user->update(['last_activity' => now()]);

            return [
                'success' => true,
                'user' => $this->formatUserData($user),
                'token' => $token,
                'refresh_token' => $refreshToken,
                'expires_in' => config('jwt.ttl') * 60
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Đăng nhập thất bại'
            ];
        }
    }

    /**
     * Admin login with username
     * 
     * @param array $credentials
     * @return array
     */
    public function adminLogin(array $credentials): array
    {
        try {
            // Find admin by username
            $user = User::where('username', $credentials['username'])
                       ->where('role', 'admin')
                       ->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return [
                    'success' => false,
                    'message' => 'Thông tin đăng nhập không chính xác'
                ];
            }

            // Check if admin is active
            if (!$user->isActive()) {
                return [
                    'success' => false,
                    'message' => 'Tài khoản admin đã bị khóa'
                ];
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);
            $refreshToken = $this->generateRefreshToken($user);

            // Update last activity
            $user->update(['last_activity' => now()]);

            return [
                'success' => true,
                'user' => $this->formatAdminData($user),
                'token' => $token,
                'refresh_token' => $refreshToken,
                'expires_in' => config('jwt.ttl') * 60
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Đăng nhập thất bại'
            ];
        }
    }

    /**
     * Register new customer
     * 
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        try {
            DB::beginTransaction();

            // Check if phone already exists
            if (User::where('phone', $data['phone'])->exists()) {
                return [
                    'success' => false,
                    'message' => 'Số điện thoại đã được sử dụng',
                    'errors' => ['phone' => ['Số điện thoại đã được sử dụng']]
                ];
            }

            // Check if email already exists (if provided)
            if (!empty($data['email']) && User::where('email', $data['email'])->exists()) {
                return [
                    'success' => false,
                    'message' => 'Email đã được sử dụng',
                    'errors' => ['email' => ['Email đã được sử dụng']]
                ];
            }

            // Check if ID card already exists
            if (User::where('id_card', $data['idCard'])->exists()) {
                return [
                    'success' => false,
                    'message' => 'Số CMND/CCCD đã được sử dụng',
                    'errors' => ['idCard' => ['Số CMND/CCCD đã được sử dụng']]
                ];
            }

            // Create user
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'id_card' => $data['idCard'],
                'address' => $data['address'],
                'date_of_birth' => $data['dateOfBirth'],
                'occupation' => $data['occupation'],
                'monthly_income' => $data['monthlyIncome'],
                'role' => 'customer',
                'status' => 'pending_verification',
                'verification_status' => 'pending',
                'credit_score' => 0,
            ]);

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            DB::commit();

            return [
                'success' => true,
                'user' => $this->formatUserData($user),
                'token' => $token
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Đăng ký thất bại'
            ];
        }
    }

    /**
     * Logout user
     * 
     * @return void
     */
    public function logout(): void
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            // Token already invalid
        }
    }

    /**
     * Refresh JWT token
     * 
     * @param string $refreshToken
     * @return array
     */
    public function refreshToken(string $refreshToken): array
    {
        try {
            // Validate refresh token (implement your own logic)
            // For now, we'll use JWT refresh
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            $user = JWTAuth::user();
            
            $newRefreshToken = $this->generateRefreshToken($user);

            return [
                'success' => true,
                'token' => $newToken,
                'refresh_token' => $newRefreshToken,
                'expires_in' => config('jwt.ttl') * 60
            ];

        } catch (JWTException $e) {
            return [
                'success' => false,
                'message' => 'Token không hợp lệ hoặc đã hết hạn'
            ];
        }
    }

    /**
     * Generate refresh token
     * 
     * @param User $user
     * @return string
     */
    private function generateRefreshToken(User $user): string
    {
        // Simple refresh token generation
        // In production, you should store this in database with expiration
        return base64_encode($user->id . '|' . time() . '|' . str_random(32));
    }

    /**
     * Format user data for response
     * 
     * @param User $user
     * @return array
     */
    private function formatUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'createdAt' => $user->created_at->toISOString()
        ];
    }

    /**
     * Format admin data for response
     * 
     * @param User $user
     * @return array
     */
    private function formatAdminData(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'permissions' => $user->permissions ?? []
        ];
    }
}