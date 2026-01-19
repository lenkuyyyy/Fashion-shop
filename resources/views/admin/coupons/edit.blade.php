@extends('admin.layouts.AdminLayouts')
@section('title-page')
    <h3>Chỉnh sửa Voucher: <strong class="text-primary">{{ $coupon->code }}</strong></h3>
@endsection
@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Lỗi nhập liệu:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8">
                            {{-- Giữ nguyên các trường mã, loại giảm giá --}}
                            <div class="mb-3">
                                <label for="code" class="form-label">Mã Voucher <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $coupon->code) }}" maxlength="50">
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="discount_type" class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                                <select name="discount_type" id="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                                    <option value="" disabled>-- Chọn loại giảm giá --</option>
                                    <optgroup label="Giảm giá đơn hàng">
                                        <option value="percent" {{ old('discount_type', $coupon->discount_type) == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                                        <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Số tiền cố định (VNĐ)</option>
                                    </optgroup>
                                    <optgroup label="Giảm giá vận chuyển">
                                        <option value="free_shipping" {{ old('discount_type', $coupon->discount_type) == 'free_shipping' ? 'selected' : '' }}>Miễn phí vận chuyển</option>
                                        <option value="fixed_shipping" {{ old('discount_type', $coupon->discount_type) == 'fixed_shipping' ? 'selected' : '' }}>Giảm giá vận chuyển (VNĐ)</option>
                                    </optgroup>
                                </select>
                                @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3" id="discount_value_wrapper">
                                    <label for="discount_value" class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                                    {{-- [CẬP NHẬT] --}}
                                    <input type="number" name="discount_value" id="discount_value" class="form-control @error('discount_value') is-invalid @enderror" step="1" min="0" value="{{ old('discount_value', (int)$coupon->discount_value) }}" placeholder="Nhập giá trị">
                                    @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3" id="max_discount_wrapper" style="display: none;">
                                    <label for="max_discount" class="form-label">Giảm tối đa (VNĐ)</label>
                                    {{-- [CẬP NHẬT] --}}
                                    <input type="number" name="max_discount" id="max_discount" class="form-control @error('max_discount') is-invalid @enderror" step="1" min="0" value="{{ old('max_discount', (int)$coupon->max_discount) }}" placeholder="Không bắt buộc">
                                    @error('max_discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="min_order_value" class="form-label">Giá trị đơn hàng tối thiểu (VNĐ)</label>
                                {{-- [CẬP NHẬT] --}}
                                <input type="number" name="min_order_value" id="min_order_value" class="form-control @error('min_order_value') is-invalid @enderror" step="1" min="0" value="{{ old('min_order_value', (int)$coupon->min_order_value) }}" placeholder="Không bắt buộc">
                                <small class="form-text text-muted">Bỏ trống nếu không áp dụng.</small>
                                @error('min_order_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            {{-- Giữ nguyên các trường ngày tháng, số lượng, trạng thái --}}
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $coupon->start_date->format('Y-m-d\TH:i')) }}">
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $coupon->end_date->format('Y-m-d\TH:i')) }}">
                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                             <div class="mb-3">
                                <label for="usage_limit" class="form-label">Tổng số lượt sử dụng <span class="text-danger">*</span></label>
                                <input type="number" name="usage_limit" id="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" min="1" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                                @error('usage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="user_usage_limit" class="form-label">Lượt sử dụng/mỗi người dùng <span class="text-danger">*</span></label>
                                <input type="number" name="user_usage_limit" id="user_usage_limit" class="form-control @error('user_usage_limit') is-invalid @enderror" min="1" value="{{ old('user_usage_limit', $coupon->user_usage_limit) }}">
                                @error('user_usage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                             <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $coupon->status) == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                                    <option value="inactive" {{ old('status', $coupon->status) == 'inactive' ? 'selected' : '' }}>Vô hiệu hóa</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Cập nhật</button>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Giữ nguyên JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const discountTypeSelect = document.getElementById('discount_type');
    const discountValueWrapper = document.getElementById('discount_value_wrapper');
    const discountValueInput = document.getElementById('discount_value');
    const maxDiscountWrapper = document.getElementById('max_discount_wrapper');

    function toggleFields() {
        const selectedType = discountTypeSelect.value;
        discountValueWrapper.style.display = 'block';
        maxDiscountWrapper.style.display = 'none';
        discountValueInput.disabled = false;
        if (selectedType === 'percent') {
            maxDiscountWrapper.style.display = 'block';
            discountValueInput.placeholder = 'Nhập % giảm, ví dụ: 15';
            discountValueInput.max = 100;
        } else if (selectedType === 'fixed') {
             discountValueInput.placeholder = 'Nhập số tiền giảm';
             discountValueInput.removeAttribute('max');
        } else if (selectedType === 'free_shipping') {
            discountValueWrapper.style.display = 'none';
            discountValueInput.disabled = true;
        } else if (selectedType === 'fixed_shipping') {
            discountValueInput.placeholder = 'Nhập số tiền giảm cho ship';
            discountValueInput.removeAttribute('max');
        }
    }
    discountTypeSelect.addEventListener('change', toggleFields);
    toggleFields();
});
</script>
@endpush