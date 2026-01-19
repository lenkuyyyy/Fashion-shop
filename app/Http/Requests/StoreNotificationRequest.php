<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép tất cả người dùng sử dụng request này
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'message' => 'required|string',
            'type' => 'required|in:system,email,order,product,news,promotion,other',
            'target' => 'required|in:all_customers,all_admins',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'message.required' => 'Nội dung thông báo là bắt buộc.',
            'type.required' => 'Loại thông báo là bắt buộc.',
            'type.in' => 'Loại thông báo không hợp lệ.',
            'target.required' => 'Mục tiêu gửi là bắt buộc.',
            'target.in' => 'Mục tiêu gửi không hợp lệ.',
        ];
    }
}
