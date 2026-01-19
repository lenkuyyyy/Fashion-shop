<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlideRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Thay bằng kiểm tra quyền nếu cần
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'order' => 'required|integer',
            'status' => 'required|in:0,1',
            'news_id' => 'nullable|exists:news,id',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'image.image' => 'Tệp phải là hình ảnh.',
            'image.max' => 'Hình ảnh không được vượt quá 2MB.',
            'order.required' => 'Thứ tự là bắt buộc.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'news_id.exists' => 'Tin tức không tồn tại.',
        ];
    }
}