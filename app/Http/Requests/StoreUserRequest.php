<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cho phép mọi user (hoặc chỉnh theo logic quyền nếu cần)
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email:rfc,dns|max:100|unique:users,email',
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,banned',
            'role_id' => 'nullable|exists:roles,id',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Họ tên là bắt buộc.',
            'name.max' => 'Tên không được vượt quá 50 kí tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'avatar.image' => 'File ảnh phải có định dạng jpeg, png, jpg hoặc gif.',
            'avatar.max' => 'Ảnh không được lớn hơn 2MB.',
            'phone_number.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'role_id.exists' => 'Quyền không hợp lệ.',
        ];
    }
}

