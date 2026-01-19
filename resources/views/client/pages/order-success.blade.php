@extends('client.pages.page-layout')

@section('styles')
    <style>
        .order-success {
            padding: 60px 0;
        }
        .order-success .card {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .order-success .icon-check {
            font-size: 64px;
            color: #28a745;
        }
    </style>
@endsection

@section('content')
<div class="container order-success">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center p-4">
                <div class="mb-3">
                    <i class="icon-check bi bi-check-circle-fill" style="color: #28a745;"></i>
                </div>
                <h1 class="card-title mb-3 text-success">Thanh toán VNpay thành công!</h1>
                <p class="card-text mb-4 text-muted">Cảm ơn bạn đã đặt hàng. Bạn có thể xem lịch sử đơn hàng của mình trong tài khoản.</p>
                <ul class="list-group list-group-flush mb-4 text-start">
                    <li class="list-group-item">
                        <strong>Ngày đặt:</strong>
                        {{ $order->created_at->format('d/m/Y H:i:s') }}
                    </li>
                    <li class="list-group-item">
                        <strong>Tổng tiền:</strong>
                        {{ number_format($order->total_price, 0, ',', '.') }}₫
                    </li>
                    <li class="list-group-item">
                        <strong>Phương thức thanh toán:</strong>
                        {{ ucfirst($order->payment_method) }}
                    </li>
                </ul>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary text-dark"><i class="bi bi-arrow-left"></i>Trang chủ</a>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary"><i class="bi bi-clock-history"></i> Lịch sử đơn hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
