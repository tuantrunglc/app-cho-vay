<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerLoginRequest;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Authentication Controller
 * 
 * Handles user authentication for both customers and admins
 */
class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['except' => ['customerLogin', 'adminLogin', 'customerRegister', 'refresh']]);
    }

    /**
     * Customer login
     * 
     * @param CustomerLoginRequest $request
     * @return JsonResponse
     */
    public function customerLogin(CustomerLoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('phone', 'password');
            $credentials['role'] = 'customer';

            $result = $this->authService->login($credentials);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'code' => 'AUTH_001'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $result['user'],
                    'token' => $result['token'],
                    'refreshToken' => $result['refresh_token'],
                    'expiresIn' => $result['expires_in']
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => '1.0.0'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng nhập thất bại',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Admin login
     * 
     * @param AdminLoginRequest $request
     * @return JsonResponse
     */
    public function adminLogin(AdminLoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('username', 'password');
            $credentials['role'] = 'admin';

            $result = $this->authService->adminLogin($credentials);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'code' => 'AUTH_001'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $result['user'],
                    'token' => $result['token'],
                    'refreshToken' => $result['refresh_token'],
                    'expiresIn' => $result['expires_in']
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => '1.0.0'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng nhập thất bại',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Customer registration
     * 
     * @param CustomerRegisterRequest $request
     * @return JsonResponse
     */
    public function customerRegister(CustomerRegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $result['user'],
                    'token' => $result['token'],
                    'message' => 'Đăng ký thành công. Vui lòng xác thực tài khoản.'
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => '1.0.0'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng ký thất bại',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Logout user
     * 
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công',
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng xuất thất bại',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Refresh JWT token
     * 
     * @param RefreshTokenRequest $request
     * @return JsonResponse
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken($request->refresh_token);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'code' => 'AUTH_002'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $result['token'],
                    'refreshToken' => $result['refresh_token'],
                    'expiresIn' => $result['expires_in']
                ],
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Làm mới token thất bại',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Get authenticated user
     * 
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        try {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'data' => $user,
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin người dùng',
                'code' => 'SYS_001'
            ], 500);
        }
    }
}