<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

/**
 * Response Helper
 * 
 * Provides consistent API response formatting
 */
class ResponseHelper
{
    /**
     * Success response
     * 
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Thành công', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0.0'
            ]
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Error response
     * 
     * @param string $message
     * @param string $code
     * @param mixed $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function error(string $message, string $code = 'ERROR', $errors = null, int $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'code' => $code,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0.0'
            ]
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Validation error response
     * 
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    public static function validationError(array $errors, string $message = 'Dữ liệu không hợp lệ'): JsonResponse
    {
        return self::error($message, 'VALIDATION_ERROR', $errors, 422);
    }

    /**
     * Not found response
     * 
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Không tìm thấy tài nguyên'): JsonResponse
    {
        return self::error($message, 'NOT_FOUND', null, 404);
    }

    /**
     * Unauthorized response
     * 
     * @param string $message
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Không có quyền truy cập'): JsonResponse
    {
        return self::error($message, 'UNAUTHORIZED', null, 401);
    }

    /**
     * Forbidden response
     * 
     * @param string $message
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Bị cấm truy cập'): JsonResponse
    {
        return self::error($message, 'FORBIDDEN', null, 403);
    }

    /**
     * Server error response
     * 
     * @param string $message
     * @return JsonResponse
     */
    public static function serverError(string $message = 'Lỗi hệ thống'): JsonResponse
    {
        return self::error($message, 'SERVER_ERROR', null, 500);
    }

    /**
     * Paginated response
     * 
     * @param mixed $data
     * @param mixed $paginator
     * @param string $message
     * @return JsonResponse
     */
    public static function paginated($data, $paginator, string $message = 'Thành công'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'totalPages' => $paginator->lastPage(),
                'totalItems' => $paginator->total(),
                'itemsPerPage' => $paginator->perPage(),
                'hasNextPage' => $paginator->hasMorePages(),
                'hasPrevPage' => $paginator->currentPage() > 1
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0.0'
            ]
        ]);
    }
}