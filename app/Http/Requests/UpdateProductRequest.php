<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Xác định người dùng có quyền thực hiện request này không.
     */
    public function authorize(): bool
    {
        return true; // Có thể tùy chỉnh logic phân quyền nếu cần
    }

    protected function prepareForValidation()
    {
        if ($this->has('variants') && is_string($this->variants)) {
            $decoded = json_decode($this->variants, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge([
                    'variants' => $decoded
                ]);
            } else {
                $this->merge([
                    'variants' => [] // Gán mảng rỗng nếu JSON không hợp lệ
                ]);
            }
        }
    }

    /**
     * Các rules validate.
     */
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

            'variants' => 'nullable|array',
            'variants.*.id' => 'required|exists:product_variants,id',
            'variants.*.color' => 'required|string|max:50',
            'variants.*.size' => 'required|string|max:50',
            // Thêm trường giá nhập vào validate
            'variants.*.import_price' => 'required|numeric|min:0',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.status' => 'required|in:active,inactive',
            'variants.*.image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Thông điệp lỗi tùy chỉnh bằng tiếng Việt.
     */
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
            'variants.array' => 'Dữ liệu biến thể phải là một mảng.',
            'variants.*.id.required' => 'ID biến thể là bắt buộc.',
            'variants.*.id.exists' => 'ID biến thể không hợp lệ.',
            'variants.*.color.required' => 'Màu sắc của biến thể là bắt buộc.',
            'variants.*.color.string' => 'Màu sắc phải là chuỗi ký tự.',
            'variants.*.color.max' => 'Màu sắc không được vượt quá 50 ký tự.',
            'variants.*.size.required' => 'Kích cỡ của biến thể là bắt buộc.',
            'variants.*.size.string' => 'Kích cỡ phải là chuỗi ký tự.',
            // Thêm thông điệp lỗi cho trường giá nhập
            'variants.*.import_price.required' => 'Giá nhập của biến thể là bắt buộc.',
            'variants.*.import_price.numeric' => 'Giá nhập phải là số.',
            'variants.*.import_price.min' => 'Giá nhập không được nhỏ hơn 0.',
            'variants.*.size.max' => 'Kích cỡ không được vượt quá 50 ký tự.',
            'variants.*.price.required' => 'Giá của biến thể là bắt buộc.',
            'variants.*.price.numeric' => 'Giá phải là số.',
            'variants.*.price.min' => 'Giá không được nhỏ hơn 0.',
            'variants.*.stock_quantity.required' => 'Số lượng tồn kho là bắt buộc.',
            'variants.*.stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'variants.*.stock_quantity.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'variants.*.status.required' => 'Trạng thái của biến thể là bắt buộc.',
            'variants.*.status.in' => 'Trạng thái của biến thể phải là Kích hoạt hoặc Không kích hoạt.',
            'variants.*.image.image' => 'Ảnh biến thể phải là file ảnh.',
            'variants.*.image.mimes' => 'Ảnh biến thể chỉ hỗ trợ định dạng JPG, JPEG, PNG.',
            'variants.*.image.max' => 'Ảnh biến thể không được vượt quá 2MB.',
        ];
    }
}
