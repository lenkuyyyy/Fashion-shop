@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Thùng rác Vouchers</h3>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left-circle me-1"></i> Quay lại danh sách
        </a>
    </div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover table-bordered align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>Mã Voucher</th>
                        <th>Loại giảm giá</th>
                        <th>Giá trị</th>
                        <th>Ngày xóa</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td>{{ $coupon->code }}</td>
                            <td>{{ $coupon->friendly_discount_type }}</td>
                            <td class="text-success fw-bold">{{ $coupon->formatted_value }}</td>
                            <td>{{ $coupon->deleted_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.coupons.restore', $coupon->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Bạn có muốn khôi phục voucher này?')">
                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                    </button>
                                </form>
                                {{-- Thêm nút xóa vĩnh viễn nếu cần --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Thùng rác trống.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection