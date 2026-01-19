<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('variants') && is_string($this->variants)) {
            $this->merge([
                'variants' => json_decode($this->variants, true)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name,' . $this->route('product') . ',id',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive,out_of_stock',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:2000',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'variants' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $combinations = [];
                    foreach ($value as $index => $variant) {
                        $key = $variant['color'] . '|' . $variant['size'];
                        if (in_array($key, $combinations)) {
                            $fail("Biến thể tại vị trí $index trùng lặp (màu: {$variant['color']}, kích cỡ: {$variant['size']}).");
                        }
                        $combinations[] = $key;
                    }
                },
            ],
            'variants.*.color' => 'required|string|max:50',
            'variants.*.size' => 'required|string|max:50',
            'variants.*.import_price' => 'required|numeric|min:0',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'variants.*.quantity' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'brand_id.required' => 'Thương hiệu là bắt buộc.',
            'brand_id.exists' => 'Thương hiệu đã chọn không hợp lệ.',
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái phải là một trong các giá trị: Kích hoạt, Không kích hoạt, Hết hàng.',
            'short_description.string' => 'Mô tả ngắn phải là chuỗi ký tự.',
            'short_description.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'description.max' => 'Mô tả không được vượt quá 2000 ký tự.',
            'thumbnail.image' => 'Ảnh đại diện phải là file ảnh.',
            'thumbnail.mimes' => 'Ảnh đại diện chỉ hỗ trợ định dạng JPG, JPEG, PNG.',
            'thumbnail.max' => 'Ảnh đại diện không được vượt quá 2MB.',
            'variants.required' => 'Vui lòng nhập ít nhất một biến thể.',
            'variants.array' => 'Dữ liệu biến thể phải là một mảng.',
            'variants.*.color.required' => 'Màu sắc của biến thể là bắt buộc.',
            'variants.*.color.string' => 'Màu sắc phải là chuỗi ký tự.',
            'variants.*.color.max' => 'Màu sắc không được vượt quá 50 ký tự.',
            'variants.*.size.required' => 'Kích cỡ của biến thể là bắt buộc.',
            'variants.*.size.string' => 'Kích cỡ phải là chuỗi ký tự.',
            'variants.*.size.max' => 'Kích cỡ không được vượt quá 50 ký tự.',
            'variants.*.import_price.required' => 'Giá nhập của biến thể là bắt buộc.',
            'variants.*.import_price.numeric' => 'Giá nhập phải là số.',
            'variants.*.import_price.min' => 'Giá nhập không được nhỏ hơn 0.',
            'variants.*.price.required' => 'Giá bán của biến thể là bắt buộc.',
            'variants.*.price.numeric' => 'Giá bán phải là số.',
            'variants.*.price.min' => 'Giá bán không được nhỏ hơn 0.',
            'variants.*.image.image' => 'Ảnh biến thể phải là file ảnh.',
            'variants.*.image.mimes' => 'Ảnh biến thể chỉ hỗ trợ định dạng JPG, JPEG, PNG.',
            'variants.*.image.max' => 'Ảnh biến thể không được vượt quá 2MB.',
            'variants.*.quantity.required' => 'Số lượng tồn kho là bắt buộc.',
            'variants.*.quantity.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'variants.*.quantity.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
        ];
    }
}