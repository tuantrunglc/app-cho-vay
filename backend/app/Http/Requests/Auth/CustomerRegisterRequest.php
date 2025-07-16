<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Customer Register Request
 * 
 * Validates customer registration data
 */
class CustomerRegisterRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\s]+$/u', // Only letters and spaces
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/',
                Rule::unique('users', 'phone'),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
            ],
            'confirmPassword' => [
                'required',
                'string',
                'same:password',
            ],
            'idCard' => [
                'required',
                'string',
                'regex:/^[0-9]{9,12}$/',
                Rule::unique('users', 'id_card'),
            ],
            'address' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],
            'dateOfBirth' => [
                'required',
                'date',
                'before:' . now()->subYears(18)->toDateString(), // Must be at least 18 years old
                'after:' . now()->subYears(80)->toDateString(), // Not older than 80
            ],
            'occupation' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
            'monthlyIncome' => [
                'required',
                'numeric',
                'min:1000000', // Minimum 1 million VND
                'max:1000000000', // Maximum 1 billion VND
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
            
            'phone.required' => 'Số điện thoại là bắt buộc',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
            'phone.unique' => 'Số điện thoại đã được sử dụng',
            
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã được sử dụng',
            
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            
            'confirmPassword.required' => 'Xác nhận mật khẩu là bắt buộc',
            'confirmPassword.same' => 'Xác nhận mật khẩu không khớp',
            
            'idCard.required' => 'Số CMND/CCCD là bắt buộc',
            'idCard.regex' => 'Số CMND/CCCD không đúng định dạng',
            'idCard.unique' => 'Số CMND/CCCD đã được sử dụng',
            
            'address.required' => 'Địa chỉ là bắt buộc',
            'address.min' => 'Địa chỉ phải có ít nhất 10 ký tự',
            'address.max' => 'Địa chỉ không được quá 500 ký tự',
            
            'dateOfBirth.required' => 'Ngày sinh là bắt buộc',
            'dateOfBirth.date' => 'Ngày sinh không đúng định dạng',
            'dateOfBirth.before' => 'Bạn phải đủ 18 tuổi để đăng ký',
            'dateOfBirth.after' => 'Tuổi không được quá 80',
            
            'occupation.required' => 'Nghề nghiệp là bắt buộc',
            'occupation.min' => 'Nghề nghiệp phải có ít nhất 2 ký tự',
            'occupation.max' => 'Nghề nghiệp không được quá 100 ký tự',
            
            'monthlyIncome.required' => 'Thu nhập hàng tháng là bắt buộc',
            'monthlyIncome.numeric' => 'Thu nhập hàng tháng phải là số',
            'monthlyIncome.min' => 'Thu nhập hàng tháng tối thiểu 1,000,000 VND',
            'monthlyIncome.max' => 'Thu nhập hàng tháng tối đa 1,000,000,000 VND',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Remove spaces from phone and idCard
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/\s+/', '', $this->phone)
            ]);
        }

        if ($this->has('idCard')) {
            $this->merge([
                'idCard' => preg_replace('/\s+/', '', $this->idCard)
            ]);
        }

        // Normalize name
        if ($this->has('name')) {
            $this->merge([
                'name' => trim(preg_replace('/\s+/', ' ', $this->name))
            ]);
        }
    }
}