<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Upload Avatar Request
 * 
 * Validates avatar upload data
 */
class UploadAvatarRequest extends FormRequest
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
            'avatar' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png',
                'max:5120', // 5MB
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'avatar.required' => 'Vui lòng chọn file ảnh',
            'avatar.file' => 'File không hợp lệ',
            'avatar.image' => 'File phải là ảnh',
            'avatar.mimes' => 'Chỉ chấp nhận file JPG, JPEG, PNG',
            'avatar.max' => 'File ảnh không được quá 5MB',
        ];
    }
}