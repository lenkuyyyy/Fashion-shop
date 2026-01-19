@extends('admin.layouts.AdminLayouts')
@section('title-page')
    <h3>Quản lý Vouchers</h3>
@endsection
@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header">
                {{-- Giữ nguyên form tìm kiếm --}}
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <form class="d-flex gap-2" method="GET" action="{{ route('admin.coupons.index') }}">
                            <input type="text" name="search" class="form-control" placeholder="Tìm theo mã voucher..." value="{{ request('search') }}">
                            <select name="discount_type" class="form-select w-50">
                                <option value="">Tất cả loại giảm giá</option>
                                <option value="percent" {{ request('discount_type') == 'percent' ? 'selected' : '' }}>Phần trăm</option>
                                <option value="fixed" {{ request('discount_type') == 'fixed' ? 'selected' : '' }}>Cố định</option>
                                <option value="free_shipping" {{ request('discount_type') == 'free_shipping' ? 'selected' : '' }}>Miễn phí vận chuyển</option>
                                <option value="fixed_shipping" {{ request('discount_type') == 'fixed_shipping' ? 'selected' : '' }}>Giảm giá vận chuyển</option>
                            </select>
                            <select name="status" class="form-select w-25">
                                <option value="">Trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Vô hiệu</option>
                            </select>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('admin.coupons.trashed') }}" class="btn btn-danger me-2">
                            <i class="bi bi-trash me-1"></i> Thùng rác ({{ $trashedCount }})
                        </a>
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Thêm mới
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if (session('success'))
                    <div class="alert alert-success m-3">{{ session('success') }}</div>
                @endif
                <table class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Mã Voucher</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Đơn tối thiểu</th>
                            <th>Số lượng còn lại</th>
                            <th>Trạng thái</th>
                            <th width="150">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coupons as $key => $coupon)
                            <tr>
                                <td>{{ $coupons->firstItem() + $key }}</td>
                                <td class="fw-bold">{{ $coupon->code }}</td>
                                <td>{{ $coupon->friendly_discount_type }}</td>
                                <td class="text-success fw-bold">{{ $coupon->formatted_value }}</td>
                                {{-- [CẬP NHẬT] --}}
                                <td>{{ $coupon->formatted_min_order_value }}</td>
                                <td><span class="badge bg-primary fs-6">{{ $coupon->usage_limit - $coupon->used_count }}</span></td>
                                <td>
                                    @if ($coupon->status == 'active' && $coupon->end_date > now())
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">
                                            {{ $coupon->end_date <= now() ? 'Hết hạn' : 'Vô hiệu' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn chuyển voucher này vào thùng rác?')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">Không tìm thấy voucher nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3 p-3">
                    {{ $coupons->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection