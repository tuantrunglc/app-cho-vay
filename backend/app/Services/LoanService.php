<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoanApplication;
use App\Models\Loan;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

/**
 * Loan Service
 * 
 * Handles loan-related operations
 */
class LoanService
{
    /**
     * Calculate loan details
     * 
     * @param array $data
     * @return array
     */
    public function calculateLoan(array $data): array
    {
        $amount = $data['amount'];
        $term = $data['term'];
        $interestRate = $data['interestRate'] ?? SystemSetting::get('loan_base_interest_rate', 2.0);

        // Calculate monthly payment using compound interest formula
        $monthlyRate = $interestRate / 100 / 12;
        
        if ($monthlyRate == 0) {
            $monthlyPayment = $amount / $term;
        } else {
            $monthlyPayment = $amount * ($monthlyRate * pow(1 + $monthlyRate, $term)) / (pow(1 + $monthlyRate, $term) - 1);
        }

        $totalPayment = $monthlyPayment * $term;
        $totalInterest = $totalPayment - $amount;

        return [
            'loanAmount' => $amount,
            'term' => $term,
            'interestRate' => $interestRate,
            'monthlyPayment' => round($monthlyPayment, 0),
            'totalPayment' => round($totalPayment, 0),
            'totalInterest' => round($totalInterest, 0),
            'paymentSchedule' => $this->generatePaymentSchedule($amount, $term, $interestRate, $monthlyPayment)
        ];
    }

    /**
     * Submit loan application
     * 
     * @param User $user
     * @param array $data
     * @return array
     */
    public function submitApplication(User $user, array $data): array
    {
        try {
            DB::beginTransaction();

            // Check if user is verified
            if (!$user->isVerified()) {
                return [
                    'success' => false,
                    'message' => 'Tài khoản chưa được xác thực. Vui lòng hoàn tất xác thực trước khi vay.',
                    'code' => 'LOAN_002'
                ];
            }

            // Check if user has pending applications
            $pendingApplication = LoanApplication::where('user_id', $user->id)
                                                ->where('status', 'pending')
                                                ->first();

            if ($pendingApplication) {
                return [
                    'success' => false,
                    'message' => 'Bạn đã có đơn vay đang chờ duyệt. Vui lòng chờ kết quả.',
                    'code' => 'LOAN_003'
                ];
            }

            // Check if user has active loans
            $activeLoan = Loan::where('user_id', $user->id)
                             ->where('status', 'active')
                             ->first();

            if ($activeLoan) {
                return [
                    'success' => false,
                    'message' => 'Bạn đang có khoản vay chưa thanh toán xong.',
                    'code' => 'LOAN_005'
                ];
            }

            // Validate loan amount
            $minAmount = SystemSetting::get('loan_min_amount', 10000000);
            $maxAmount = SystemSetting::get('loan_max_amount', 500000000);

            if ($data['amount'] < $minAmount || $data['amount'] > $maxAmount) {
                return [
                    'success' => false,
                    'message' => "Số tiền vay phải từ " . number_format($minAmount) . " đến " . number_format($maxAmount) . " VND",
                    'code' => 'LOAN_006'
                ];
            }

            // Calculate loan details
            $loanDetails = $this->calculateLoan($data);

            // Create loan application
            $application = LoanApplication::create([
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'term' => $data['term'],
                'purpose' => $data['purpose'],
                'status' => 'pending',
                'verification_status' => 'pending',
                'interest_rate' => $loanDetails['interestRate'],
                'monthly_payment' => $loanDetails['monthlyPayment'],
                'emergency_contact' => $data['emergencyContact'] ?? null,
                'priority' => $this->calculatePriority($user, $data['amount'])
            ]);

            DB::commit();

            return [
                'success' => true,
                'application' => $application
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Gửi đơn vay thất bại'
            ];
        }
    }

    /**
     * Get user's loan applications
     * 
     * @param User $user
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserApplications(User $user, array $filters = [])
    {
        $query = LoanApplication::where('user_id', $user->id)
                               ->with(['documents', 'loan'])
                               ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $perPage = $filters['limit'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Lookup loan by phone number
     * 
     * @param string $phone
     * @return array
     */
    public function lookupByPhone(string $phone): array
    {
        try {
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin với số điện thoại này'
                ];
            }

            // Get latest application
            $latestApplication = LoanApplication::where('user_id', $user->id)
                                              ->orderBy('created_at', 'desc')
                                              ->first();

            // Get active loan
            $activeLoan = Loan::where('user_id', $user->id)
                             ->where('status', 'active')
                             ->first();

            return [
                'success' => true,
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'status' => $user->status
                    ],
                    'latestApplication' => $latestApplication ? [
                        'loanId' => $latestApplication->loan_id,
                        'amount' => $latestApplication->amount,
                        'status' => $latestApplication->status,
                        'createdAt' => $latestApplication->created_at->format('d/m/Y H:i')
                    ] : null,
                    'activeLoan' => $activeLoan ? [
                        'loanId' => $activeLoan->loan_id,
                        'originalAmount' => $activeLoan->original_amount,
                        'remainingAmount' => $activeLoan->remaining_amount,
                        'nextPaymentDate' => $activeLoan->next_payment_date->format('d/m/Y'),
                        'monthlyPayment' => $activeLoan->monthly_payment,
                        'isOverdue' => $activeLoan->is_overdue
                    ] : null
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Tra cứu thông tin thất bại'
            ];
        }
    }

    /**
     * Generate payment schedule
     * 
     * @param float $amount
     * @param int $term
     * @param float $interestRate
     * @param float $monthlyPayment
     * @return array
     */
    private function generatePaymentSchedule(float $amount, int $term, float $interestRate, float $monthlyPayment): array
    {
        $schedule = [];
        $remainingBalance = $amount;
        $monthlyRate = $interestRate / 100 / 12;
        
        for ($month = 1; $month <= $term; $month++) {
            $interestPayment = $remainingBalance * $monthlyRate;
            $principalPayment = $monthlyPayment - $interestPayment;
            $remainingBalance -= $principalPayment;
            
            $schedule[] = [
                'month' => $month,
                'principalPayment' => round($principalPayment, 0),
                'interestPayment' => round($interestPayment, 0),
                'totalPayment' => round($monthlyPayment, 0),
                'remainingBalance' => round(max(0, $remainingBalance), 0)
            ];
        }
        
        return $schedule;
    }

    /**
     * Calculate application priority
     * 
     * @param User $user
     * @param float $amount
     * @return string
     */
    private function calculatePriority(User $user, float $amount): string
    {
        $score = 0;

        // Credit score factor
        if ($user->credit_score >= 700) {
            $score += 3;
        } elseif ($user->credit_score >= 600) {
            $score += 2;
        } elseif ($user->credit_score >= 500) {
            $score += 1;
        }

        // Income factor
        $incomeRatio = $amount / ($user->monthly_income * 12);
        if ($incomeRatio <= 0.3) {
            $score += 3;
        } elseif ($incomeRatio <= 0.5) {
            $score += 2;
        } elseif ($incomeRatio <= 0.7) {
            $score += 1;
        }

        // Determine priority
        if ($score >= 5) {
            return 'high';
        } elseif ($score >= 3) {
            return 'medium';
        } else {
            return 'low';
        }
    }
}