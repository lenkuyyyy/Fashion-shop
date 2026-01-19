@extends('client.pages.page-layout')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-4">
                    {{-- BÊN TRÁI: Địa chỉ & Sản phẩm --}}
                    <div class="col-md-6">
                        {{-- 🏠 Địa chỉ giao hàng --}}
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="mb-3">Địa chỉ nhận hàng</h5>
                                <p><strong>Người nhận:</strong> {{ $order->shippingAddress->name }}</p>
                                <p><strong>Điện thoại:</strong> {{ $order->shippingAddress->phone_number }}</p>
                                <p><strong>Địa chỉ:</strong>
                                    {{ $order->shippingAddress->address }}{{ $order->shippingAddress->ward ? ', ' . $order->shippingAddress->ward : '' }}{{ $order->shippingAddress->district ? ', ' . $order->shippingAddress->district : '' }}{{ $order->shippingAddress->city ? ', ' . $order->shippingAddress->city : '' }}
                                </p>
                            </div>
                        </div>

                        {{-- 📦 Danh sách sản phẩm --}}
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="mb-3">Sản phẩm trong đơn</h5>
                                <ul class="list-group">
                                    @foreach ($order->orderDetails as $detail)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $detail->productVariant->product->name }}</strong>
                                                <span>x{{ $detail->quantity }}</span><br>
                                                <small>Size: {{ $detail->productVariant->size ?? 'N/A' }}, Màu:
                                                    {{ $detail->productVariant->color ?? 'N/A' }}</small>
                                            </div>
                                            <strong>{{ number_format($detail->subtotal, 0, ',', '.') }} ₫</strong>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- 🔄 Trạng thái thanh toán --}}
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="mb-3">Trạng thái thanh toán</h5>
                                <p class="mb-2">⏳ Đang chờ thanh toán từ Momo...</p>
                                <p class="text-muted small">
                                    Sau khi bạn hoàn tất thanh toán, hệ thống sẽ tự động xác nhận đơn hàng.<br>
                                    Nếu gặp sự cố, vui lòng liên hệ CSKH.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- BÊN PHẢI: Thông tin thanh toán ATM Momo --}}
                    <div class="col-md-6">
                        <div class="card mb-4 shadow-sm text-center p-4">
                            <h5 class="mb-3">Số tiền cần thanh toán:</h5>
                            <h3 class="text-danger fw-bold">{{ number_format($total, 0, ',', '.') }} ₫</h3>
                            <p class="text-muted mb-3">Vui lòng chuyển khoản qua ATM Momo</p>

                            {{-- Hướng dẫn thanh toán ATM Momo --}}
                            <div class="text-start mb-3">
                                <h6>Hướng dẫn thanh toán:</h6>
                                <ul>
                                    <li><strong>Số điện thoại Momo:</strong> 0123 456 789</li>
                                    <li><strong>Chủ tài khoản:</strong> Công ty XYZ</li>
                                    <li><strong>Số tiền:</strong> {{ number_format($total, 0, ',', '.') }} ₫</li>
                                    <li><strong>Nội dung chuyển khoản:</strong> Thanh toán đơn hàng #{{ $order->order_code }}</li>
                                </ul>
                                <p class="text-muted small">Sau khi chuyển khoản, đơn hàng sẽ được xác nhận trong vòng 24 giờ.</p>
                            </div>

                            <form action="{{ url('/momo_payment') }}" method="post">
                                @csrf
                                <input type="hidden" name="total_momo" value="{{ (int)$total }}">
                                <button type="submit" class="btn btn-danger w-100" name="payUrl">Thanh toán MOMO</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
