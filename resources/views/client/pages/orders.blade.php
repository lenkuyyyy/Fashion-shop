@extends('client.pages.page-layout')

@section('content')
    <div class="container-fluid px-4 pt-3">
        <div class="row g-4">

            <div class="col-lg-11" style="margin: 0 auto;">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                    @if (session('order-success'))
                        <div class="alert alert-success alert-dismissible fade show m-3 rounded-3 border-0 shadow-sm"
                            role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('order-success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('cancel-request-success'))
                        <div class="alert alert-success alert-dismissible fade show m-3 rounded-3 border-0 shadow-sm"
                            role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('cancel-request-success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @elseif (session('cancel-request-error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3 rounded-3 border-0 shadow-sm"
                            role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('cancel-request-error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('return-success'))
                        <div class="alert alert-success alert-dismissible fade show m-3 rounded-3 border-0 shadow-sm"
                            role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('return-success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('return-error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3 rounded-3 border-0 shadow-sm"
                            role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('return-error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card-header bg-gradient-primary text-white p-4 border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-receipt-cutoff fs-4 me-3"></i>
                                <h5 class="mb-0 fw-bold">Lịch sử đơn hàng</h5>
                            </div>
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2">
                                {{ $totalOrderCount }} đơn hàng
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="accordion accordion-flush" id="orderAccordion">
                            @forelse ($orders as $order)
                                <div class="accordion-item border-0 mb-4 rounded-4 shadow-sm overflow-hidden"
                                    data-order-code="{{ $order->order_code }}">
                                    <h2 class="accordion-header" id="heading{{ $order->id }}">
                                        <div class="d-flex align-items-end bg-light rounded-4 p-4 border-0 flex-column">
                                            <button
                                                class="accordion-button collapsed bg-transparent border-0 p-0 flex-grow-1 shadow-none"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $order->id }}">
                                                <div class="d-flex align-items-center justify-content-between w-100">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary rounded-circle p-2 me-3">
                                                            <i class="bi bi-box-seam text-white"></i>
                                                        </div>
                                                        <div>
                                                            <div class="mb-3 d-flex flex-column">

                                                                <h6 class="fw-bold text-dark mb-1">
                                                                    Người nhận: <span class="text-primary">
                                                                        {{ $order->shippingAddress->name ?? $order->user->name }}</span>
                                                                </h6>

                                                                <small class="mb-0 text-muted mb-1">
                                                                    <i class="bi bi-telephone me-1"></i>
                                                                    {{ $order->shippingAddress->phone_number }}
                                                                </small>

                                                                <small class="text-muted d-block mb-1">
                                                                    <i class="bi bi-calendar3 me-1"></i>
                                                                    {{ $order->created_at->format('d/m/Y - H:i') }}
                                                                </small>

                                                                <small class="text-muted d-block mb-1">
                                                                    <i class="bi bi-geo-alt me-1"></i>
                                                                    {{ $order->shippingAddress->full_address }}
                                                                </small>

                                                                <small class="text-muted d-block mb-1">
                                                                    <i class="bi bi-box me-1"></i>
                                                                    @php
                                                                        $products = $order->orderDetails
                                                                            ->take(2)
                                                                            ->map(function ($detail) {
                                                                                return $detail->productVariant->product
                                                                                    ->name .
                                                                                    ' (x' .
                                                                                    $detail->quantity .
                                                                                    ')';
                                                                            })
                                                                            ->join(', ');
                                                                        $more =
                                                                            $order->orderDetails->count() > 2
                                                                                ? '...'
                                                                                : '';
                                                                    @endphp
                                                                    Sản phẩm: {{ $products }}{{ $more }}
                                                                </small>

                                                                <small class="" style="font-size: 0.95rem">
                                                                    {{ $order->getPaymentMethod($order->payment_method)['label'] }}
                                                                    -
                                                                    <span class="fw-semibold"
                                                                        style="color: {{ $order->getPaymentStatus($order->payment_status)['color'] }}">
                                                                        {{ $order->getPaymentStatus($order->payment_status)['label'] }}
                                                                    </span>
                                                                </small>
                                                            </div>

                                                            {{-- hiển thị trạng thái yêu cầu huỷ (nếu có) --}}
                                                            @php
                                                                $isRequested = $order->cancellation_requested;
                                                                $isConfirmed = $order->cancel_confirmed;
                                                                $isCancelled = $order->status === 'cancelled';
                                                                $customerReason = $order->cancel_reason;
                                                                $adminReason = $order->admin_cancel_note;
                                                            @endphp

                                                            @if ($isRequested || $isCancelled || $adminReason)
                                                                <div class="mt-2">
                                                                    <div class="bg-light border rounded px-2 py-1 mt-1 small text-muted"
                                                                        style="font-size: 0.85rem;">
                                                                        {{-- ✅ Trường hợp 1: Khách yêu cầu và admin xác nhận -> đơn bị huỷ --}}
                                                                        @if ($isCancelled && $isRequested && $isConfirmed)
                                                                            <i
                                                                                class="bi bi-person-fill text-primary me-1"></i>
                                                                            <span class="text-dark">Bạn đã yêu cầu
                                                                                huỷ:</span>
                                                                            <em>{{ $customerReason }}</em><br>

                                                                            <i
                                                                                class="bi bi-shield-check text-success me-1"></i>
                                                                            <span class="text-success">Admin đã xác nhận và
                                                                                đơn đã được huỷ theo yêu cầu của bạn.</span>

                                                                            {{-- 🛑 Trường hợp 2: Admin chủ động huỷ (không có yêu cầu từ khách) --}}
                                                                        @elseif ($isCancelled && !$isRequested && $adminReason)
                                                                            <i
                                                                                class="bi bi-person-badge-fill text-danger me-1"></i>
                                                                            <span class="text-dark">Admin huỷ đơn
                                                                                hàng:</span>
                                                                            <em>{{ $adminReason }}</em>

                                                                            {{-- ⏳ Trường hợp 3: Khách gửi yêu cầu, chưa xác nhận --}}
                                                                        @elseif ($isRequested && !$isConfirmed && !$isCancelled)
                                                                            <i
                                                                                class="bi bi-person-fill text-primary me-1"></i>
                                                                            <span class="text-dark">Bạn đã gửi yêu
                                                                                cầu huỷ:</span>
                                                                            <em>{{ $customerReason ?? 'Không có lý do' }}</em><br>

                                                                            <i class="bi bi-clock-history me-1"></i>
                                                                            <span class="text-muted fst-italic">Đang chờ
                                                                                phản hồi từ admin.</span>

                                                                            {{-- ❌ Trường hợp 4: Khách yêu cầu nhưng bị từ chối --}}
                                                                        @elseif ($isRequested && $isConfirmed && !$isCancelled && $adminReason)
                                                                            <i
                                                                                class="bi bi-person-fill text-primary me-1"></i>
                                                                            <span class="text-dark">Bạn đã gửi yêu
                                                                                cầu huỷ:</span>
                                                                            <em>{{ $customerReason }}</em><br>

                                                                            <i class="bi bi-shield-x text-danger me-1"></i>
                                                                            <span class="text-danger">Admin đã từ chối yêu
                                                                                cầu huỷ:</span>
                                                                            <em>{{ $adminReason }}</em>

                                                                            {{-- trường hợp: khách huỷ thanh toán đơn --}}
                                                                        @elseif(!empty($order->vnp_txn_ref) && $order->payment_status === 'failed')
                                                                            <i
                                                                                class="bi bi-person-fill text-primary me-1"></i>
                                                                            <span class="text-dark">Bạn đã huỷ thanh toán
                                                                                nên đơn hàng bị huỷ</span>
                                                                            {{-- ❓ Không rõ lý do --}}
                                                                        @else
                                                                            <span class="text-muted fst-italic">Không có lý
                                                                                do được cung cấp.</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            {{-- end --}}

                                                            {{-- Hiển thị trạng thái yêu cầu trả hàng (nếu có) --}}
                                                            @php
                                                                $returnRequest = $order->returnRequest;
                                                                $hasReturnRequest = $returnRequest !== null;
                                                            @endphp

                                                            @if ($hasReturnRequest)
                                                                <div class="mt-2">
                                                                    <div class="bg-light border rounded px-2 py-1 mt-1 small text-muted"
                                                                        style="font-size: 0.85rem;">
                                                                        {{-- 📦 Trạng thái trả hàng --}}
                                                                        <i
                                                                            class="bi bi-arrow-return-left text-primary me-1"></i>
                                                                        <span class="text-dark">Bạn đã gửi yêu cầu trả
                                                                            hàng:</span>
                                                                        <em>{{ $returnRequest->reason ?? 'Không có lý do' }}</em><br>

                                                                        @if ($returnRequest->status === 'requested')
                                                                            <i class="bi bi-clock-history me-1"></i>
                                                                            <span class="text-muted fst-italic">Đang chờ
                                                                                phản hồi từ admin.</span>
                                                                        @elseif ($returnRequest->status === 'approved')
                                                                            <i
                                                                                class="bi bi-shield-check text-success me-1"></i>
                                                                            <span class="text-success">
                                                                                {{ $returnRequest->admin_note ?? 'Yêu cầu trả hàng đã được phê duyệt.' }}
                                                                            </span>
                                                                        @elseif ($returnRequest->status === 'rejected')
                                                                            <i class="bi bi-shield-x text-danger me-1"></i>
                                                                            <span class="text-danger">
                                                                                Yêu cầu trả hàng đã bị từ chối. Lý do:
                                                                                {{ $returnRequest->admin_note ?? 'Không có lý do' }}
                                                                            </span>
                                                                        @elseif ($returnRequest->status === 'refunded')
                                                                            <i
                                                                                class="bi bi-check-circle-fill text-primary me-1"></i>
                                                                            <span class="text-primary">
                                                                                {{ $returnRequest->admin_note ?? 'Yêu cầu trả hàng / hoàn tiền đã hoàn tất.' }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            {{-- end --}}
                                                        </div>
                                                    </div>

                                                    <div class="text-end">
                                                        @php
                                                            $return = $order->returnRequest;
                                                            $cancelRequested =
                                                                $order->cancellation_requested &&
                                                                !$order->cancel_confirmed &&
                                                                $order->status !== 'cancelled';
                                                            $cancelConfirmed =
                                                                $order->cancellation_requested &&
                                                                $order->cancel_confirmed &&
                                                                $order->status === 'cancelled';
                                                            $returnStatus = $return
                                                                ? [
                                                                        'requested' => [
                                                                            'color' => 'info',
                                                                            'icon' => 'bi bi-clock-history',
                                                                            'title' =>
                                                                                'Yêu cầu trả hàng đang chờ xử lý',
                                                                        ],
                                                                        'approved' => [
                                                                            'color' => 'success',
                                                                            'icon' => 'bi bi-shield-check',
                                                                            'title' =>
                                                                                'Yêu cầu trả hàng được phê duyệt',
                                                                        ],
                                                                        'rejected' => [
                                                                            'color' => 'danger',
                                                                            'icon' => 'bi bi-shield-x',
                                                                            'title' => 'Yêu cầu trả hàng bị từ chối',
                                                                        ],
                                                                        'refunded' => [
                                                                            'color' => 'success',
                                                                            'icon' => 'bi bi-check-circle-fill',
                                                                            'title' => 'Đã hoàn tiền',
                                                                        ],
                                                                    ][$return->status] ?? null
                                                                : null;
                                                        @endphp

                                                        {{-- Ưu tiên hiển thị: hoàn hàng > huỷ đơn > trạng thái đơn --}}
                                                        @if ($return && in_array($return->status, ['requested', 'approved', 'rejected', 'refunded']))
                                                            <span
                                                                class="badge bg-{{ $returnStatus['color'] }} px-3 py-2 rounded-pill"
                                                                data-status-badge>
                                                                <i class="{{ $returnStatus['icon'] }} me-1"></i>
                                                                {{ $returnStatus['title'] }}
                                                            </span>
                                                        @elseif ($order->status === 'refund_in_processing')
                                                            <span class="badge bg-info px-3 py-2 rounded-pill"
                                                                data-status-badge>
                                                                <i class="bi bi-clock-history me-1"></i> Đang xử lý yêu cầu
                                                                trả hàng
                                                            </span>
                                                        @elseif ($cancelRequested)
                                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"
                                                                data-status-badge>
                                                                <i class="bi bi-hourglass-split me-1"></i> Yêu cầu huỷ đang
                                                                chờ xử lý
                                                            </span>
                                                        @elseif ($cancelConfirmed)
                                                            <span class="badge bg-danger px-3 py-2 rounded-pill"
                                                                data-status-badge>
                                                                <i class="bi bi-x-octagon me-1"></i> Đơn hàng đã huỷ
                                                            </span>
                                                        @else
                                                            <span
                                                                class="badge {{ $order->getStatusMeta($order->status)['color'] }} px-3 py-2 rounded-pill"
                                                                data-status-badge>
                                                                {{ $order->getStatusMeta($order->status)['label'] }}
                                                            </span>
                                                        @endif

                                                        {{-- Tổng tiền --}}
                                                        <div class="mt-1">
                                                            <span class="fw-bold text-primary">
                                                                {{ number_format($order->total_price, 0, ',', '.') }}₫
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </button>

                                            @php
                                                $vpnRetry =
                                                    in_array($order->payment_method, ['online', 'bank_transfer']) &&
                                                    $order->payment_status === 'pending' &&
                                                    !empty($order->vnp_txn_ref) &&
                                                    !($isRequested || $isCancelled || $adminReason);
                                            @endphp

                                            @if (isset($momoRetry))
                                                <form id="auto-momo-form-{{ $order->id }}"
                                                    action="{{ route('momo_payment') }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                    <input type="hidden" name="total_momo"
                                                        value="{{ $order->total_price }}">
                                                </form>
                                                <a href="javascript:void(0)"
                                                    onclick="document.getElementById('auto-momo-form-{{ $order->id }}').submit();"
                                                    class="btn btn-outline-primary">
                                                    Thanh toán lại
                                                </a>
                                            @elseif ($vpnRetry)
                                                <form id="retry-payment-form-{{ $order->id }}"
                                                    action="{{ route('checkout.retry', $order->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                </form>
                                                <a href="javascript:void(0)"
                                                    onclick="document.getElementById('retry-payment-form-{{ $order->id }}').submit();"
                                                    class="btn btn-outline-primary">
                                                    🔁 Thanh toán lại
                                                </a>
                                            @endif

                                            @if (in_array($order->status, ['pending', 'processing']))
                                                @if (!$isRequested && !$isCancelled)
                                                    <div class="d-flex justify-content-end mt-1">
                                                        <button type="button"
                                                            class="btn btn-outline-danger open-client-cancel-modal"
                                                            data-order-id="{{ $order->id }}">
                                                            <i class="bi bi-x-circle me-2"></i>Huỷ đơn hàng
                                                        </button>
                                                    </div>
                                                @endif
                                            @elseif($order->status === 'delivered')
                                                <div class="d-flex justify-content-end gap-3 mt-1 flex-wrap">
                                                    @php
                                                        $return = $order->returnRequest;
                                                    @endphp

                                                    {{-- Nếu chưa gửi yêu cầu trả hàng và chưa hoàn thành --}}
                                                    @if (!$return && $order->status !== 'completed')
                                                        <button type="button" class="btn btn-success btn-received"
                                                            data-order-id="{{ $order->id }}"
                                                            data-order-code="{{ $order->order_code }}">
                                                            <i class="bi bi-check-circle me-2"></i>Đã nhận hàng
                                                        </button>

                                                        <button type="button" class="btn btn-outline-primary"
                                                            onclick="showReturnRequestPrompt({{ $order->id }})">
                                                            <i class="bi bi-arrow-return-left me-2"></i>Trả hàng/Hoàn tiền
                                                        </button>
                                                        {{-- Nếu yêu cầu trả hàng bị từ chối và chưa hoàn thành --}}
                                                    @elseif($return && $return->status === 'rejected' && $order->status !== 'completed')
                                                        <button type="button" class="btn btn-success btn-received"
                                                            data-order-id="{{ $order->id }}"
                                                            data-order-code="{{ $order->order_code }}">
                                                            <i class="bi bi-check-circle me-2"></i>Xác nhận đã nhận hàng
                                                        </button>
                                                    @endif
                                                </div>
                                                {{-- @elseif($order->status === 'refund_in_processing')
                                                <div class="d-flex justify-content-end gap-3 mt-1 flex-wrap">
                                                    <span class="text-info">Đang xử lý yêu cầu trả hàng</span>
                                                </div>
                                            @elseif($order->status === 'refunded')
                                                <div class="d-flex justify-content-end gap-3 mt-1 flex-wrap">
                                                    <span class="text-success">Đã hoàn tiền</span>
                                                </div> --}}
                                            @endif
                                        </div>
                                    </h2>

                                    <div id="collapse{{ $order->id }}" class="accordion-collapse collapse"
                                        data-bs-parent="#orderAccordion">
                                        <div class="accordion-body p-4 bg-white">

                                            <div class="mb-3 bg-light p-3 rounded shadow-sm border">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <span class="mb-0 fw-bold text-secondary">Mã đơn hàng:</span>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <span class="text-primary fw-bold">{{ $order->order_code }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="border-0 fw-bold">Sản phẩm</th>
                                                            <th class="border-0 fw-bold">Màu</th>
                                                            <th class="border-0 fw-bold">Size</th>
                                                            <th class="border-0 fw-bold">SL</th>
                                                            <th class="border-0 fw-bold">Đơn giá</th>
                                                            <th class="border-0 fw-bold text-end">Tổng</th>
                                                            <th class="border-0 fw-bold text-center">Đánh giá</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($order->orderDetails as $detail)
                                                            <tr>
                                                                <td class="align-middle">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="p-2 me-3">
                                                                            <img src="{{ $detail->productVariant->image }}"
                                                                                alt="{{ $detail->productVariant->product->name }}"
                                                                                style="width: 50px; height: 50px; object-fit:fill;">
                                                                        </div>
                                                                        <span class="fw-medium">
                                                                            <a
                                                                                href="{{ route('detail-product', $detail->productVariant->product->id) }}">
                                                                                {{ $detail->productVariant->product->name }}</a>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <span
                                                                        class="badge bg-secondary p-2">{{ $detail->productVariant->color }}</span>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <span
                                                                        class="badge bg-info p-2">{{ $detail->productVariant->size }}</span>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <span
                                                                        class="badge bg-primary p-2">{{ $detail->quantity }}</span>
                                                                </td>
                                                                <td class="align-middle">
                                                                    {{ number_format($detail->price, 0, ',', '.') }}₫</td>
                                                                <td class="align-middle text-end fw-bold">
                                                                    {{ number_format($detail->subtotal, 0, ',', '.') }}₫
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    @if ($order->status === 'completed')
                                                                        @php
                                                                            $review = \App\Models\Review::where(
                                                                                'order_detail_id',
                                                                                $detail->id,
                                                                            )->first();
                                                                        @endphp
                                                                        @if ($review)
                                                                            <div
                                                                                class="text-warning d-flex align-items-center justify-content-center">
                                                                                <i class="bi bi-star-fill me-1"></i>
                                                                                <span
                                                                                    class="fw-bold">{{ $review->rating }}</span>
                                                                            </div>
                                                                        @else
                                                                            <button class="btn btn-outline-warning btn-sm"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#reviewModal"
                                                                                data-product-id="{{ $detail->productVariant->product->id }}"
                                                                                data-product-name="{{ $detail->productVariant->product->name }}"
                                                                                data-order-detail-id="{{ $detail->id }}">
                                                                                <i class="bi bi-star"></i> Đánh giá
                                                                            </button>
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="row justify-content-end mt-3">
                                                <div class="col-md-12">
                                                    <div class="card border-0 bg-light">
                                                        <div class="card-body">

                                                            {{-- phí vận chuyển --}}
                                                            <div class="d-flex justify-content-between">
                                                                <span class="fw-bold text-success">Phí vận chuyển:</span>
                                                                <span
                                                                    class="fw-bold text-primary">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                                                            </div>
                                                            {{-- giảm giá phí vận chuyển --}}

                                                            @if ($order->order_discount > 0)
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="fw-bold  text-success">Giảm giá cho đơn
                                                                        hàng:</span>
                                                                    <span class="fw-bold text-primary">
                                                                        -{{ number_format($order->order_discount, 0, ',', '.') }}₫
                                                                    </span>
                                                                </div>
                                                            @endif
                                                            {{-- giảm giá phí vận chuyển --}}
                                                            @if ($order->shipping_discount > 0)
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="fw-bold   text-success">Giảm giá phí vận
                                                                        chuyển:</span>
                                                                    <span class="fw-bold text-primary ">
                                                                        -{{ number_format($order->shipping_discount, 0, ',', '.') }}₫
                                                                    </span>
                                                                </div>
                                                            @endif
                                                            {{-- tổng giảm giá nếu  --}}
                                                            @if ($order->order_discount > 0 || $order->shipping_discount > 0)
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="fw-bold  text-success">Tổng giảm
                                                                        giá:</span>
                                                                    <span class="fw-bold text-primary">
                                                                        -{{ number_format($order->order_discount + $order->shipping_discount, 0, ',', '.') }}₫
                                                                    </span>
                                                                </div>
                                                            @endif

                                                            <hr>
                                                            <div class="d-flex justify-content-between">
                                                                <span class="fw-bold fs-5 text-danger">Thành tiền:</span>
                                                                <span
                                                                    class="fw-bold fs-5 text-primary">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                    <h4 class="text-muted mb-2">Chưa có đơn hàng nào</h4>
                                    <p class="text-muted">Hãy bắt đầu mua sắm để tạo đơn hàng đầu tiên của bạn!</p>
                                    <a href="#" class="btn btn-primary btn-lg rounded-pill px-4">
                                        <i class="bi bi-shop me-2"></i>Bắt đầu mua sắm
                                    </a>
                                </div>
                            @endforelse
                        </div>
                        <div>
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clientCancelModal" tabindex="-1" aria-labelledby="clientCancelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="client-cancel-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="clientCancelModalLabel"><i class="bi bi-x-circle me-2"></i>Yêu cầu
                            huỷ
                            đơn hàng</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cancel_reason" class="form-label">Lý do huỷ đơn hàng:</label>
                            <textarea name="cancel_reason" id="cancel_reason" rows="3" class="form-control"
                                placeholder="Nhập lý do huỷ..." required></textarea>
                            <div class="invalid-feedback">
                                Vui lòng nhập lý do huỷ đơn hàng (ít nhất 10 ký tự).
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">Gửi yêu cầu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-gradient-success text-white rounded-top-4 border-0">
                    <h5 class="modal-title fw-bold" id="orderModalLabel">
                        <i class="bi bi-check-circle-fill me-2"></i>Thành công
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Đóng"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-3"></h4>
                    <p class="text-muted">Cảm ơn bạn đã tin tưởng sử dụng dịch vụ của chúng tôi!</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-success btn-lg rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-hand-thumbs-up me-2"></i>Tuyệt vời
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="orderErrorModal" tabindex="-1" aria-labelledby="orderErrorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-gradient-danger text-white rounded-top-4 border-0">
                    <h5 class="modal-title fw-bold" id="orderErrorModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Có lỗi xảy ra
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Đóng"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-3 text-danger"></h4>
                    <p class="text-muted">Vui lòng thử lại sau hoặc liên hệ với chúng tôi để được hỗ trợ.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-outline-danger btn-lg rounded-pill px-4"
                        data-bs-dismiss="modal">
                        <i class="bi bi-arrow-clockwise me-2"></i>Thử lại
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Đánh giá sản phẩm: <span
                            id="productNameToReview"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm" action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" id="productIdToReview">
                        <input type="hidden" name="order_detail_id" id="orderDetailIdToReview">
                        <div class="mb-3">
                            <label class="form-label">Điểm đánh giá</label>
                            <select class="form-select w-auto" name="rating" required>
                                <option value="">Chọn số sao</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} sao</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nội dung</label>
                            <textarea class="form-control" rows="4" name="comment" placeholder="Nhận xét của bạn về sản phẩm..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark w-100"><i class="bi bi-send me-1"></i>Gửi đánh
                            giá</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        }

        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }

        .form-floating>.form-control:focus~label {
            color: #667eea;
        }

        .form-floating>.form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success') || session('received-success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('orderModal'));
                modal.show();
            });
        </script>
    @endif

    @if (session('received-error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('orderErrorModal'));
                modal.show();
                setTimeout(() => modal.hide(), 4000);
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reviewModal = document.getElementById('reviewModal');
            if (reviewModal) {
                reviewModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const productId = button.getAttribute('data-product-id');
                    const productName = button.getAttribute('data-product-name');
                    const orderDetailId = button.getAttribute('data-order-detail-id');

                    const modalTitle = reviewModal.querySelector('#productNameToReview');
                    const productIdInput = reviewModal.querySelector('#productIdToReview');
                    const orderDetailIdInput = reviewModal.querySelector('#orderDetailIdToReview');

                    modalTitle.textContent = productName;
                    productIdInput.value = productId;
                    orderDetailIdInput.value = orderDetailId;
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.open-client-cancel-modal');
            const form = document.getElementById('client-cancel-form');
            const modal = new bootstrap.Modal(document.getElementById('clientCancelModal'));
            const reasonField = document.getElementById('cancel_reason');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.dataset.orderId;
                    form.action = `/order/${orderId}/cancel-request`;
                    form.reset();
                    modal.show();
                });
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const reason = reasonField.value.trim();

                if (reason.length < 10) {
                    reasonField.classList.add('is-invalid');
                    reasonField.focus();
                    return;
                }

                reasonField.classList.remove('is-invalid');

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            cancel_reason: reason
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        modal.hide();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: data.message,
                                showConfirmButton: true,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: data.message,
                                showConfirmButton: true,
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        modal.hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Đã có lỗi xảy ra. Vui lòng thử lại!',
                            showConfirmButton: true,
                            confirmButtonText: 'OK'
                        });
                    });
            });
        });
    </script>

    <script>
        function showReturnRequestPrompt(orderId) {
            Swal.fire({
                title: 'Yêu cầu trả hàng',
                input: 'textarea',
                inputLabel: 'Lý do yêu cầu trả hàng (bắt buộc)',
                inputPlaceholder: 'Vui lòng mô tả vấn đề của bạn...',
                inputAttributes: {
                    'aria-label': 'Lý do trả hàng',
                    'rows': 4
                },
                inputValidator: (value) => {
                    if (!value.trim()) {
                        return 'Bạn phải nhập lý do trả hàng!';
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Gửi yêu cầu',
                cancelButtonText: 'Huỷ'
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(`/orders/${orderId}/return-request`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                reason: result.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const orderElement = document.querySelector(
                                    `[data-order-code="${data.order_code}"]`);
                                if (orderElement) {
                                    const statusBadge = orderElement.querySelector('[data-status-badge]');
                                    if (statusBadge) {
                                        statusBadge.textContent = getStatusLabel('refund_in_processing');
                                        statusBadge.className =
                                            `badge ${getStatusColor('refund_in_processing')} px-3 py-2 rounded-pill`;
                                    }
                                    updateOrderActions(orderElement, 'refund_in_processing', orderId);
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công',
                                    text: data.message,
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi',
                                    text: data.message || 'Đã có lỗi xảy ra!',
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Đã có lỗi xảy ra khi gửi yêu cầu!',
                                showConfirmButton: true,
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(event) {
                if (event.target.closest('.btn-received')) {
                    const button = event.target.closest('.btn-received');
                    const orderId = button.dataset.orderId;
                    const orderCode = button.dataset.orderCode;

                    Swal.fire({
                        title: 'Xác nhận nhận hàng',
                        text: `Bạn có chắc chắn đã nhận được đơn hàng #${orderCode}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            button.disabled = true;
                            button.innerHTML =
                                '<i class="bi bi-hourglass-split me-2"></i>Đang xử lý...';
                            return fetch("{{ route('order.received', ':id') }}".replace(':id',
                                    orderId), {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(
                                            `HTTP error! Status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(`Lỗi: ${error.message}`);
                                });
                        }
                    }).then((result) => {
                        button.disabled = false;
                        button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Đã nhận hàng';
                        if (result.isConfirmed) {
                            const modal = new bootstrap.Modal(document.getElementById(result.value
                                .success ? 'orderModal' : 'orderErrorModal'));
                            const modalMessage = modal._element.querySelector('.modal-body h4');
                            modalMessage.textContent = result.value.message;
                            modal.show();
                            if (result.value.success) {
                                setTimeout(() => {
                                    modal.hide();
                                    location.reload();
                                }, 4000);
                            }
                        }
                    });
                }
            });
        });
    </script>

    <script>
        sessionStorage.removeItem('paymentInProgress');
    </script>

    @vite('resources/js/app.js')
    <script defer>
        window.addEventListener('load', () => {
            console.log('User ID:', {{ auth()->id() ?? 'null' }});
            console.log('Echo:', window.Echo);
            if (window.Echo) {
                window.Echo.channel(`orders.{{ auth()->id() }}`)
                    .subscribed(() => {
                        console.log('Đã đăng ký kênh orders.{{ auth()->id() }}');
                    })
                    .listen('.order.status.updated', (e) => {
                        console.log('Đơn hàng đã cập nhật:', e);
                        console.log('Tìm order_code:', e.order_code);
                        const orderElement = document.querySelector(`[data-order-code="${e.order_code}"]`);
                        console.log('Order element:', orderElement);
                        if (orderElement) {
                            const statusBadge = orderElement.querySelector('[data-status-badge]');
                            console.log('Status badge:', statusBadge);
                            if (statusBadge) {
                                console.log('Cập nhật status:', e.status, 'Label:', getStatusLabel(e.status));
                                statusBadge.textContent = getStatusLabel(e.status);
                                statusBadge.className =
                                    `badge ${getStatusColor(e.status)} px-3 py-2 rounded-pill`;
                            } else {
                                console.error('Không tìm thấy status badge');
                            }
                            updateOrderActions(orderElement, e.status, e.id);
                        } else {
                            console.error('Không tìm thấy order element với order_code:', e.order_code);
                        }
                    })
                    .error((error) => {
                        console.error('Lỗi khi đăng ký kênh:', error);
                    });
            } else {
                console.error('Echo không được khởi tạo');
            }
        });

        function getStatusLabel(status) {
            const statusMap = {
                'pending': 'Chờ xác nhận',
                'processing': 'Đang xử lý',
                'shipped': 'Đang giao hàng',
                'delivered': 'Đã giao hàng',
                'completed': 'Đã hoàn thành',
                'cancelled': 'Đơn đã hủy',
                'refund_in_processing': 'Đang xử lý trả hàng',
                'refunded': 'Đã hoàn tiền'
            };
            return statusMap[status] || status;
        }

        function getStatusColor(status) {
            const colorMap = {
                'pending': 'bg-warning text-dark',
                'processing': 'bg-primary',
                'shipped': 'bg-info',
                'delivered': 'bg-success',
                'completed': 'bg-dark',
                'cancelled': 'bg-danger',
                'refund_in_processing': 'bg-info',
                'refunded': 'bg-success'
            };
            return colorMap[status] || 'bg-secondary';
        }

        function updateOrderActions(orderElement, status, orderId) {
            const cancelButton = orderElement.querySelector('.open-client-cancel-modal');
            const oldActionContainers = orderElement.querySelectorAll('.d-flex.justify-content-end.gap-3.mt-1');
            oldActionContainers.forEach(container => container.remove());

            const actionContainer = createActionContainer(orderElement);

            if (cancelButton) {
                cancelButton.style.display = ['pending', 'processing'].includes(status) ? 'block' : 'none';
            }

            if (status === 'delivered') {
                const receiveButton = document.createElement('button');
                receiveButton.type = 'button';
                receiveButton.className = 'btn btn-success btn-received';
                receiveButton.dataset.orderId = orderId;
                receiveButton.dataset.orderCode = orderElement.dataset.orderCode;
                receiveButton.innerHTML = '<i class="bi bi-check-circle me-2"></i>Đã nhận hàng';

                const returnButton = document.createElement('button');
                returnButton.type = 'button';
                returnButton.className = 'btn btn-outline-primary';
                returnButton.innerHTML = '<i class="bi bi-arrow-return-left me-2"></i>Trả hàng/Hoàn tiền';
                returnButton.onclick = () => showReturnRequestPrompt(orderId);

                actionContainer.appendChild(receiveButton);
                actionContainer.appendChild(returnButton);
                // } else if (status === 'refund_in_processing') {
                //     const span = document.createElement('span');
                //     span.className = 'text-info';
                //     span.textContent = 'Đang xử lý yêu cầu trả hàng';
                //     actionContainer.appendChild(span);
                // } else if (status === 'refunded') {
                //     const span = document.createElement('span');
                //     span.className = 'text-success';
                //     span.textContent = 'Đã hoàn tiền';
                //     actionContainer.appendChild(span);
                // }
            }

            function createActionContainer(orderElement) {
                const container = document.createElement('div');
                container.className = 'd-flex justify-content-end gap-3 mt-1 flex-wrap';
                const accordionHeader = orderElement.querySelector('.accordion-header');
                const flexColumn = accordionHeader.querySelector(
                    '.d-flex.align-items-end.bg-light.rounded-4.p-4.border-0.flex-column');
                if (flexColumn) {
                    flexColumn.appendChild(container);
                } else {
                    accordionHeader.appendChild(container);
                }
                return container;
            }
        }
    </script>
@endsection
