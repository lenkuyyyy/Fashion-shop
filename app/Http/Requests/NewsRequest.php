<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class NewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy id của tin tức đang được chỉnh sửa (nếu có)
        $newsId = $this->route('news')?->id;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                // Đảm bảo tiêu đề là duy nhất, bỏ qua chính bài viết đang sửa
                Rule::unique('news', 'title')->ignore($newsId),
            ],
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Quy tắc rõ ràng hơn
            'status' => 'required|in:active,inactive',
            'published_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.unique' => 'Tiêu đề này đã tồn tại.',
            'content.required' => 'Nội dung là bắt buộc.',
            'image.image' => 'Tệp phải là một hình ảnh hợp lệ.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'published_at.date' => 'Thời gian đăng phải là ngày tháng hợp lệ.',
        ];
    }

    /**
     * Lấy dữ liệu đã được xác thực và xử lý thêm
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        // Tự động tạo slug từ title
        $data['slug'] = Str::slug($data['title']);

        // Chuyển đổi status từ 'active'/'inactive' thành true/false (1/0)
        $data['status'] = $data['status'] === 'active';

        return $data;
    }
}