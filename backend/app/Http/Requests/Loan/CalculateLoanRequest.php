<?php

namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SystemSetting;

/**
 * Calculate Loan Request
 * 
 * Validates loan calculation data
 */
class CalculateLoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $minAmount = SystemSetting::get('loan_min_amount', 10000000);
        $maxAmount = SystemSetting::get('loan_max_amount', 500000000);
        $stepAmount = SystemSetting::get('loan_step_amount', 1000000);
        $availableTerms = SystemSetting::get('loan_terms', [6, 12, 18, 24, 36, 48]);

        return [
            'amount' => [
                'required',
                'numeric',
                'min:' . $minAmount,
                'max:' . $maxAmount,
                function ($attribute, $value, $fail) use ($stepAmount) {
                    if ($value % $stepAmount !== 0) {
                        $fail('Số tiền vay phải là bội số của ' . number_format($stepAmount) . ' VND');
                    }
                },
            ],
            'term' => [
                'required',
                'integer',
                'in:' . implode(',', $availableTerms),
            ],
            'interestRate' => [
                'sometimes',
                'numeric',
                'min:0.1',
                'max:50',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $minAmount = SystemSetting::get('loan_min_amount', 10000000);
        $maxAmount = SystemSetting::get('loan_max_amount', 500000000);
        $availableTerms = SystemSetting::get('loan_terms', [6, 12, 18, 24, 36, 48]);

        return [
            'amount.required' => 'Số tiền vay là bắt buộc',
            'amount.numeric' => 'Số tiền vay phải là số',
            'amount.min' => 'Số tiền vay tối thiểu ' . number_format($minAmount) . ' VND',
            'amount.max' => 'Số tiền vay tối đa ' . number_format($maxAmount) . ' VND',
            
            'term.required' => 'Thời hạn vay là bắt buộc',
            'term.integer' => 'Thời hạn vay phải là số nguyên',
            'term.in' => 'Thời hạn vay phải là một trong các giá trị: ' . implode(', ', $availableTerms) . ' tháng',
            
            'interestRate.numeric' => 'Lãi suất phải là số',
            'interestRate.min' => 'Lãi suất tối thiểu 0.1%',
            'interestRate.max' => 'Lãi suất tối đa 50%',
        ];
    }
}