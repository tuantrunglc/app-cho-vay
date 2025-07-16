<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\CalculateLoanRequest;
use App\Http\Requests\Loan\SubmitLoanApplicationRequest;
use App\Http\Resources\LoanApplicationResource;
use App\Http\Resources\LoanConfigResource;
use App\Models\LoanApplication;
use App\Models\SystemSetting;
use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Loan Controller
 * 
 * Handles loan-related operations for customers
 */
class LoanController extends Controller
{
    protected LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
        $this->middleware('auth:api', ['except' => ['config', 'calculate', 'lookup']]);
    }

    /**
     * Get loan configuration
     * 
     * @return JsonResponse
     */
    public function config(): JsonResponse
    {
        try {
            $config = SystemSetting::getLoanConfig();

            return response()->json([
                'success' => true,
                'data' => $config,
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => '1.0.0'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy cấu hình khoản vay',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Calculate loan details
     * 
     * @param CalculateLoanRequest $request
     * @return JsonResponse
     */
    public function calculate(CalculateLoanRequest $request): JsonResponse
    {
        try {
            $result = $this->loanService->calculateLoan($request->validated());

            return response()->json([
                'success' => true,
                'data' => $result,
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tính toán khoản vay',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Submit loan application
     * 
     * @param SubmitLoanApplicationRequest $request
     * @return JsonResponse
     */
    public function submitApplication(SubmitLoanApplicationRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $result = $this->loanService->submitApplication($user, $request->validated());

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null,
                    'code' => $result['code'] ?? 'LOAN_001'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => new LoanApplicationResource($result['application']),
                'message' => 'Đơn vay đã được gửi thành công',
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gửi đơn vay thất bại',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Get user's loan applications
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function myApplications(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $filters = $request->only(['status', 'page', 'limit']);
            
            $applications = $this->loanService->getUserApplications($user, $filters);

            return response()->json([
                'success' => true,
                'data' => [
                    'applications' => LoanApplicationResource::collection($applications->items()),
                    'pagination' => [
                        'currentPage' => $applications->currentPage(),
                        'totalPages' => $applications->lastPage(),
                        'totalItems' => $applications->total(),
                        'itemsPerPage' => $applications->perPage()
                    ]
                ],
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách đơn vay',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Get loan application details
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function applicationDetails(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $application = LoanApplication::where('loan_id', $id)
                                        ->where('user_id', $user->id)
                                        ->with(['documents', 'loan'])
                                        ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn vay',
                    'code' => 'LOAN_004'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new LoanApplicationResource($application),
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy chi tiết đơn vay',
                'code' => 'SYS_001'
            ], 500);
        }
    }

    /**
     * Lookup loan by phone number
     * 
     * @param string $phone
     * @return JsonResponse
     */
    public function lookup(string $phone): JsonResponse
    {
        try {
            $result = $this->loanService->lookupByPhone($phone);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'meta' => [
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tra cứu thông tin',
                'code' => 'SYS_001'
            ], 500);
        }
    }
}