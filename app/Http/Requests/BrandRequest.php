<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép mọi user sử dụng request này
    }

    public function rules(): array
    {
        $brandId = $this->route('id'); // Lấy ID từ route (dùng cho update)

        return [
            'name' => [
                'required',
                Rule::unique('brands', 'name')->ignore($brandId),
            ],
            'slug' => [
                'required',
                'string',
                'max:100',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],
            'status' => 'in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên thương hiệu là bắt buộc.',
            'name.unique' => 'Tên thương hiệu đã tồn tại.',
            'slug.required' => 'Slug là bắt buộc.',
            'slug.unique' => 'Slug đã tồn tại.',
            'slug.max' => 'Slug không được vượt quá 100 ký tự.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
