<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->coupon->id;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($couponId)],
            'discount_type' => ['required', Rule::in(['percent', 'fixed', 'free_shipping', 'fixed_shipping'])],
            'discount_value' => [
                'required_unless:discount_type,free_shipping',
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('discount_type') == 'percent' && $value > 100) {
                        $fail('Giá trị giảm giá theo phần trăm không được vượt quá 100%.');
                    }
                },
            ],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => [
                'nullable',
                'numeric',
                'min:0',
                // function ($attribute, $value, $fail) {
                //     $discountType = $this->input('discount_type');
                //     if (in_array($discountType, ['fixed', 'fixed_shipping']) && !is_null($value) && $value < $this->input('discount_value')) {
                //         $fail('Giảm tối đa phải lớn hơn hoặc bằng giá trị giảm.');
                //     }
                // },
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'user_usage_limit' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Mã voucher là bắt buộc.',
            'code.unique' => 'Mã voucher này đã tồn tại.',
            'discount_type.required' => 'Loại giảm giá là bắt buộc.',
            'discount_value.required_unless' => 'Giá trị giảm là bắt buộc.',
            'discount_value.numeric' => 'Giá trị giảm phải là một số.',
            'discount_value.min' => 'Giá trị giảm không được âm.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ];
    }
}