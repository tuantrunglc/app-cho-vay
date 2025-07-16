<?php

namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SystemSetting;

/**
 * Submit Loan Application Request
 * 
 * Validates loan application submission data
 */
class SubmitLoanApplicationRequest extends FormRequest
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
            'purpose' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],
            'emergencyContact' => [
                'sometimes',
                'array',
            ],
            'emergencyContact.name' => [
                'required_with:emergencyContact',
                'string',
                'min:2',
                'max:100',
            ],
            'emergencyContact.phone' => [
                'required_with:emergencyContact',
                'string',
                'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/',
            ],
            'emergencyContact.relationship' => [
                'required_with:emergencyContact',
                'string',
                'in:spouse,parent,sibling,child,friend,colleague,other',
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
            
            'purpose.required' => 'Mục đích vay là bắt buộc',
            'purpose.min' => 'Mục đích vay phải có ít nhất 10 ký tự',
            'purpose.max' => 'Mục đích vay không được quá 500 ký tự',
            
            'emergencyContact.name.required_with' => 'Tên người liên hệ khẩn cấp là bắt buộc',
            'emergencyContact.name.min' => 'Tên người liên hệ phải có ít nhất 2 ký tự',
            'emergencyContact.name.max' => 'Tên người liên hệ không được quá 100 ký tự',
            
            'emergencyContact.phone.required_with' => 'Số điện thoại người liên hệ khẩn cấp là bắt buộc',
            'emergencyContact.phone.regex' => 'Số điện thoại người liên hệ không đúng định dạng',
            
            'emergencyContact.relationship.required_with' => 'Mối quan hệ với người liên hệ là bắt buộc',
            'emergencyContact.relationship.in' => 'Mối quan hệ không hợp lệ',
        ];
    }
}