<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Change Password Request
 * 
 * Validates password change data
 */
class ChangePasswordRequest extends FormRequest
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
            'currentPassword' => [
                'required',
                'string',
            ],
            'newPassword' => [
                'required',
                'string',
                'min:6',
                'max:255',
                'different:currentPassword',
            ],
            'confirmPassword' => [
                'required',
                'string',
                'same:newPassword',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'currentPassword.required' => 'Mật khẩu hiện tại là bắt buộc',
            
            'newPassword.required' => 'Mật khẩu mới là bắt buộc',
            'newPassword.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'newPassword.different' => 'Mật khẩu mới phải khác mật khẩu hiện tại',
            
            'confirmPassword.required' => 'Xác nhận mật khẩu là bắt buộc',
            'confirmPassword.same' => 'Xác nhận mật khẩu không khớp',
        ];
    }
}