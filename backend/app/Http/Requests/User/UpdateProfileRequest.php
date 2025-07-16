<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update Profile Request
 * 
 * Validates user profile update data
 */
class UpdateProfileRequest extends FormRequest
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
        $user = $this->user();
        
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'address' => [
                'sometimes',
                'required',
                'string',
                'min:10',
                'max:500',
            ],
            'occupation' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:100',
            ],
            'monthlyIncome' => [
                'sometimes',
                'required',
                'numeric',
                'min:1000000',
                'max:1000000000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Họ tên là bắt buộc',
            'name.min' => 'Họ tên phải có ít nhất 2 ký tự',
            'name.max' => 'Họ tên không được quá 100 ký tự',
            'name.regex' => 'Họ tên chỉ được chứa chữ cái và khoảng trắng',
            
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã được sử dụng',
            
            'address.required' => 'Địa chỉ là bắt buộc',
            'address.min' => 'Địa chỉ phải có ít nhất 10 ký tự',
            'address.max' => 'Địa chỉ không được quá 500 ký tự',
            
            'occupation.required' => 'Nghề nghiệp là bắt buộc',
            'occupation.min' => 'Nghề nghiệp phải có ít nhất 2 ký tự',
            'occupation.max' => 'Nghề nghiệp không được quá 100 ký tự',
            
            'monthlyIncome.required' => 'Thu nhập hàng tháng là bắt buộc',
            'monthlyIncome.numeric' => 'Thu nhập hàng tháng phải là số',
            'monthlyIncome.min' => 'Thu nhập hàng tháng tối thiểu 1,000,000 VND',
            'monthlyIncome.max' => 'Thu nhập hàng tháng tối đa 1,000,000,000 VND',
        ];
    }
}