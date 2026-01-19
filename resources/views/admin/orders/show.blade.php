@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Chi tiết đơn hàng <strong class="text-primary">#{{ $order->order_code }}</strong></h3>
@endsection
@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-4 p-4">
            {{-- quay lại trang danh sách đơn hàng --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách đơn hàng
                    </a>
                </div>
            </div>

            {{-- Thông báo lỗi hoặc thành công --}}
            <div class="row">
                @if (session('error'))
                    <div class="alert alert-danger rounded-3">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success rounded-3">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            {{-- Thông tin người gửi, người nhận --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Người gửi</h6>
                    <address class="small text-dark">
                        <strong>HN447</strong><br>
                        Số 1, Trịnh Văn Bô, Hà Nội<br>
                        <strong>Email:</strong> HN_447@company.com<br>
                        <strong>SĐT:</strong> 010-020-0340
                    </address>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Người nhận</h6>
                    <address class="small text-dark">
                        <strong class="text-primary">{{ $order->shippingAddress->name }}</strong><br>
                        {{ $order->shippingAddress->full_address }}<br>
                        <strong>Email:</strong> {{ $order->user->email }}<br>
                        <strong>SĐT:</strong> {{ $order->shippingAddress->phone_number }}
                    </address>
                </div>
            </div>

            {{-- Thông tin đơn hàng --}}
            <div class="row mb-3 align-items-end">
                <div class="row mb-2">
                    <div class="col-md-2">
                        <h6 class="text-muted">Đơn hàng</h6>
                        <p class="fw-semibold">#{{ $order->order_code }}</p>
                    </div>

                    <div class="col-md-2">
                        <h6 class="text-muted">Trạng thái</h6>
                        <span class="badge {{ $order->getStatusLabel()['color'] }}">
                            {{ $order->getStatusLabel()['label'] }}
                        </span>
                    </div>

                    <div class="col-md-2">
                        <h6 class="text-muted">Phương thức thanh toán</h6>
                        <p class="fw-semibold">{{ strtoupper($order->payment_method) }}</p>
                    </div>

                    <div class="col-md-2">
                        <h6 class="text-muted">Trạng thái thanh toán</h6>
                        <span style="color: {{ $order->getPaymentStatus($order->payment_status)['color'] }}">
                            {{ $order->getPaymentStatus($order->payment_status)['label'] }}
                        </span>
                    </div>

                    <div class="col-md-2">
                        <h6 class="text-muted">Phiếu giảm giá</h6>
                        @if ($order->coupon)
                            @php
                                $coupon = $order->coupon;
                                $label =
                                    $coupon->discount_type === 'percent'
                                        ? 'Giảm ' . $coupon->discount_value . '%'
                                        : 'Giảm ' . number_format($coupon->discount_value, 0, ',', '.') . ' đ';
                            @endphp
                            <p class="fw-semibold">{{ $coupon->code }} - {{ $label }}</p>
                        @else
                            <p class="fw-semibold">Không sử dụng</p>
                        @endif
                    </div>

                    <div class="col-md-2">
                        <h6 class="text-muted">Ngày đặt hàng</h6>
                        <p class="fw-semibold">{{ $order->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Nếu có yêu cầu trả hàng --}}
            @if ($order->returnRequest)
                @php
                    $returnStatus = $order->returnRequest->return_status;
                    $canUpdateReturn = in_array($order->returnRequest->status, ['requested', 'approved']);
                @endphp
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="border rounded p-3 bg-light">
                            <div class="row align-items-center">
                                {{-- thông tin --}}
                                <div class="col-md-4">
                                    <strong>Trả hàng / Hoàn tiền:</strong>
                                    <span class="badge bg-{{ $returnStatus['color'] }} ms-1" style="font-size: 0.9rem;">
                                        <i class="bi {{ $returnStatus['icon'] }}"></i> {{ $returnStatus['title'] }}
                                    </span>
                                    @if ($order->returnRequest->reason)
                                        <div class="mt-1 small text-muted fst-italic">
                                            <i class="bi bi-chat-left-text me-1"></i> Lý do:
                                            {{ $order->returnRequest->reason }}
                                        </div>
                                    @endif
                                    @if ($order->returnRequest->admin_note)
                                        <div class="mt-1 small text-muted fst-italic">
                                            <i class="bi bi-chat-left-text me-1"></i> Phẩn hồi từ admin:
                                            {{ $order->returnRequest->admin_note }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Cột form cập nhật trạng thái --}}
                                <div class="col-md-8 d-flex justify-content-end gap-2 flex-wrap">
                                    {{-- Nếu trạng thái là requested --}}
                                    @if ($order->returnRequest->status === 'requested')
                                        <button class="btn btn-success btn-sm"
                                            onclick="handleReturnAction('{{ route('admin.return-requests.update', $order->returnRequest->id) }}', 'approved')">
                                            ✅ Duyệt yêu cầu
                                        </button>

                                        <button class="btn btn-danger btn-sm"
                                            onclick="handleReturnAction('{{ route('admin.return-requests.update', $order->returnRequest->id) }}', 'rejected')">
                                            ❌ Từ chối yêu cầu
                                        </button>
                                    @endif

                                    {{-- Nếu trạng thái là approved --}}
                                    @php
                                        $paymentMethod = $order->payment_method;
                                        $paymentStatus = $order->payment_status;
                                        $canRefund =
                                            $order->returnRequest->status === 'approved' &&
                                            in_array($paymentMethod, ['online', 'bank_transfer']) &&
                                            $paymentStatus === 'refund_in_processing';
                                    @endphp

                                    @if ($order->returnRequest->status === 'approved')
                                        @if ($canRefund)
                                            <button class="btn btn-success btn-sm"
                                                onclick="handleReturnAction('{{ route('admin.return-requests.update', $order->returnRequest->id) }}', 'refunded')">
                                                💸 Đã hoàn tiền
                                            </button>
                                        @elseif ($paymentMethod === 'cod')
                                            <button class="btn btn-warning btn-sm"
                                                onclick="handleReturnAction('{{ route('admin.return-requests.update', $order->returnRequest->id) }}', 'refunded')">
                                                📦 Đã trả hàng (COD)
                                            </button>
                                        @endif
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-12 mt-1 mb-2 p-3 rounded" style="background-color: rgba(164, 146, 194, 0.25);">
                @if (!in_array($order->status, ['delivered', 'completed', 'cancelled']))
                    {{-- Nút Xác nhận tương ứng với trạng thái --}}
                    @php
                        // Xác định thông điệp huỷ đơn dựa trên trạng thái
                        $cancelMessages = [
                            'pending' => 'Đơn hàng đang chờ xác nhận. Bạn có chắc muốn huỷ không?',
                            'processing' => 'Đơn hàng đang xử lý. Bạn có chắc muốn huỷ không?',
                            'shipped' => 'Đơn đã giao cho đơn vị vận chuyển. Bạn có chắc muốn huỷ không?',
                        ];

                        $cancelMessage = $cancelMessages[$order->status] ?? 'Bạn có chắc muốn huỷ đơn hàng này không?';

                        $statusActions = [
                            'pending' => [
                                'label' => 'Xác nhận đơn',
                                'next_status' => 'processing',
                            ],
                            'processing' => [
                                'label' => 'Bắt đầu giao hàng',
                                'next_status' => 'shipped',
                            ],
                            'shipped' => [
                                'label' => 'Giao hàng thành công',
                                'next_status' => 'delivered',
                            ],
                        ];

                        $action = $statusActions[$order->status] ?? null;
                    @endphp

                    @if ($action)
                        <button class="btn btn-sm btn-success me-1"
                            onclick="submitStatusUpdate('{{ route('admin.orders.update', $order->id) }}',
                                                    '{{ $action['next_status'] }}', '{{ $action['label'] }}')">
                            <i class="bi bi-pencil-square"></i> {{ $action['label'] }}
                        </button>
                    @endif

                    {{-- Nút Hủy đơn --}}
                    <button class="btn btn-sm btn-danger me-1"
                        onclick="handleCancelAction({{ $order->id }}, 'approve', '', '{{ $order->user->name }}')">
                        <i class="bi bi-x-circle"></i> Huỷ đơn
                    </button>
                @endif
            </div>

            {{-- Bảng chi tiết đơn hàng --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Mô tả ngắn</th>
                            <th>Số lượng</th>
                            <th>Đơn giá (VNĐ)</th>
                            {{-- <th>Giảm giá (VNĐ)</th> --}}
                            <th>Tổng (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $item)
                            <tr>
                                <td>{{ $item->productVariant->sku }}</td>
                                <td>
                                    @if ($item->productVariant->image)
                                        <img src="{{ asset($item->productVariant->image) }}"
                                            alt="{{ $item->productVariant->name }}" class="rounded shadow-sm"
                                            width="50" height="50" style="object-fit:cover;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->productVariant->product->name }}
                                    <br><small class="text-muted">{{ $item->productVariant->color }} -
                                        {{ $item->productVariant->size }}</small>
                                </td>
                                <td>{{ $item->productVariant->product->short_description }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                               
                                <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tổng tiền và ghi chú --}}
            <div class="row mt-4">
                <div class="col-md-8 d-flex flex-column gap-2">

                    {{-- 📝 Ghi chú đơn hàng (nếu có) --}}
                    @if ($order->note)
                        <div class="alert alert-secondary small" role="alert">
                            <strong>Ghi chú khách hàng:</strong><br>
                            {{ $order->note }}
                        </div>
                    @endif

                    {{-- ❌ Lý do khách yêu cầu huỷ --}}
                    @if ($order->cancellation_requested)
                        <div class="alert alert-warning small" role="alert">
                            <strong>Khách hàng {{ $order->user->name }}, yêu cầu huỷ đơn hàng với lý do:</strong><br>
                            {{ $order->cancel_reason ?? 'Không có lý do được cung cấp.' }}<br>

                            <span class="{{ $order->cancel_confirmed ? 'text-success' : 'text-muted' }}">
                                <span class="d-block mt-2 fw-semibold">
                                    <i class="bi bi-info-circle me-1"></i> Trạng thái xử lý yêu cầu:
                                    @if ($order->cancellation_requested && $order->cancel_confirmed && $order->status === 'cancelled')
                                        <span class="text-primary">Yêu cầu huỷ của khách đã được admin chấp nhận.</span>
                                    @elseif ($order->cancellation_requested && !$order->cancel_confirmed)
                                        <span class="text-muted">Đang chờ xác nhận từ admin.</span>
                                    @elseif ($order->cancellation_requested && $order->cancel_confirmed && $order->status !== 'cancelled')
                                        <span class="text-danger">Yêu cầu huỷ của khách đã bị admin từ chối.</span>
                                    @elseif (!$order->cancellation_requested && $order->cancel_confirmed && $order->status === 'cancelled')
                                        <span class="text-warning">Đơn hàng đã bị admin huỷ trực tiếp.</span>
                                    @else
                                        <span class="text-muted fst-italic">Không có yêu cầu huỷ hoặc trạng thái.</span>
                                    @endif
                                </span>
                            </span>

                            {{-- ✅ Nút duyệt & từ chối nếu chưa được xử lý --}}
                            @if (!$order->cancel_confirmed && $order->status !== 'cancelled')
                                <div class="mt-2 d-flex gap-2">
                                    {{-- Nút Duyệt --}}
                                    <button type="button" class="btn btn-success btn-sm"
                                        onclick="handleCancelAction({{ $order->id }},
                                         'approve', `{{ $order->cancel_reason }}`, `{{ $order->shippingAddress->name }}`)">
                                        <i class="bi bi-check-circle me-1"></i> Chấp nhận
                                    </button>

                                    {{-- Chỉ hiện nút từ chối nếu không phải đơn online chưa thanh toán --}}
                                    @if ($order->cancellation_requested && !$order->cancel_confirmed && $order->payment_status !== 'failed')
                                        {{-- Nút Từ chối --}}
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="handleCancelAction({{ $order->id }}, 'reject',
                 `{{ $order->cancel_reason }}`, `{{ $order->shippingAddress->name }}`)">
                                            <i class="bi bi-x-circle me-1"></i> Từ chối yêu cầu
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- ✅❌ Lý do admin chấp nhận hoặc từ chối yêu cầu huỷ --}}
                    @if ($order->cancel_confirmed && $order->admin_cancel_note)
                        <div class="alert alert-info small" role="alert">
                            <strong>
                                Lý do
                                {{ $order->cancellation_requested
                                    ? ($order->status === 'cancelled'
                                        ? 'admin chấp nhận yêu cầu huỷ từ khách'
                                        : 'admin từ chối yêu cầu huỷ từ khách')
                                    : 'admin chủ động huỷ đơn' }}:
                            </strong><br>
                            {{ $order->admin_cancel_note }}

                            {{-- Trạng thái hiển thị thêm nếu admin chủ động huỷ --}}
                            @unless ($order->cancellation_requested)
                                <div class="mt-1 text-muted fst-italic">
                                    <i class="bi bi-shield-fill-exclamation text-primary me-1"></i>
                                    Trạng thái: Admin đã chủ động huỷ đơn hàng.
                                </div>
                            @endunless
                        </div>
                    @endif

                    {{--  Nếu không có gì hết --}}
                    @if (!$order->note && !$order->cancellation_requested && !$order->admin_cancel_note)
                        <div class="alert alert-secondary small" role="alert">
                            <strong>Ghi chú:</strong><br>
                            Không có ghi chú hay yêu cầu nào cho đơn hàng này.
                        </div>
                    @endif

                </div>

                {{-- Tổng tiền --}}
                <div class="col-md-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-semibold">
                            Tổng tiền hàng:
                            <span>{{ number_format($order->orderDetails->sum('subtotal'), 0, ',', '.') }}₫</span>
                        </li>
                        {{-- xử lý mã mã giảm giá --}}
                       
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center fw-semibold">
                            <span>Phí vận chuyển (gốc):</span>
                            <strong>{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</strong>
                        </div>
                        {{-- giảm giá phí vận chuyển --}}
                        @if ($order->shipping_discount > 0)
                            <div class="list-group-item d-flex justify-content-between align-items-center fw-semibold text-success">
                                <span>Giảm phí vận chuyển:</span>
                                <strong>-{{ number_format($order->shipping_discount, 0, ',', '.') }} ₫</strong>
                            </div>
                        @endif
                         {{-- số tiền giảm giá cho đơn hàng --}}
                        @if ($orderDiscount > 0)
                                <div class="list-group-item d-flex justify-content-between align-items-center fw-semibold text-success">
                                    <span>Giảm giá đơn hàng:</span>
                                    <strong>- {{ number_format($orderDiscount, 0, ',', '.') }} ₫</strong>
                                </div>
                         @endif
                        {{-- tổng giảm giá --}}
                        @php
                            $totalDiscount = $orderDiscount + $order->shipping_discount;
                        @endphp
                        @if ($totalDiscount > 0)
                            <div class="list-group-item d-flex justify-content-between align-items-center fw-semibold text-success">
                                <span>Tổng giảm giá:</span>
                                <strong>-{{ number_format($totalDiscount, 0, ',', '.') }} ₫</strong>
                            </div>
                        @endif
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-semibold text-primary">
                            Thành tiền:
                            <span>{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <form id="statusUpdateForm" method="POST" style="display: none;">
        @csrf
        @method('PATCH') {{-- hoặc PATCH nếu bạn muốn --}}
        <input type="hidden" name="status" id="statusInput">
    </form>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // submit status update form
        function submitStatusUpdate(url, nextStatus, actionLabel) {
            if (confirm(`Bạn có chắc muốn thực hiện hành động: "${actionLabel}" không?`)) {
                const form = document.getElementById('statusUpdateForm');
                form.action = url;
                document.getElementById('statusInput').value = nextStatus;
                form.submit();
            }
        }

        // show cancel confirmation modal
        function showCancelModal(url, message) {
            document.getElementById('cancelForm').action = url;
            document.getElementById('cancelConfirmMessage').innerText = message;
            new bootstrap.Modal(document.getElementById('cancelConfirmModal')).show();
        }

        function escapeJs(str) {
            if (!str) return '';
            return str.replace(/\\/g, '\\\\')
                .replace(/'/g, "\\'")
                .replace(/"/g, '\\"')
                .replace(/\n/g, '\\n')
                .replace(/\r/g, '');
        }

        function handleCancelAction(orderId, action, customerReason = '', customerName = '') {
            const actionLabel = action === 'approve' ? 'Xác nhận yêu cầu huỷ đơn' : 'Từ chối yêu cầu huỷ';
            const actionColor = action === 'approve' ? '#198754' : '#dc3545'; // xanh hoặc đỏ
            const hasCustomerRequest = !!customerReason;

            const title = hasCustomerRequest ?
                `${actionLabel} từ khách hàng ${customerName || 'Ẩn danh'}` :
                'Bạn muốn huỷ đơn hàng này?';


            const htmlContent = `
                <div class="text-start">
                    ${customerReason ? `
                                                                                <label class="form-label fw-bold text-dark mb-1">
                                                                                    <i class="bi bi-person-fill text-primary me-1"></i> Lý do khách yêu cầu huỷ:
                                                                                </label>
                                                                                <div class="bg-light border rounded p-2 mb-3">
                                                                                    <em>${customerReason}</em>
                                                                                </div>
                                                                            ` : ''
                    }

                    <div class="d-flex flex-column">
                        <label for="adminReason" class="form-label fw-bold text-dark mb-1">
                        <i class="bi bi-shield-lock-fill text-danger me-1"></i> Lý do của bạn:
                    </label>
                    <textarea id="adminReason" class="swal2-textarea" placeholder="Nhập lý do của bạn..." rows="3"></textarea>
                    </div>
                </div>
            `;

            Swal.fire({
                title: title,
                html: htmlContent,
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                confirmButtonColor: actionColor,
                cancelButtonText: 'Hủy',
                focusConfirm: false,
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    const reason = document.getElementById('adminReason')?.value.trim();

                    // Nếu không có yêu cầu từ khách, hoặc là hành động "từ chối" → yêu cầu lý do
                    if (!customerReason || action === 'reject') {
                        if (!reason || reason.length < 10) {
                            Swal.showValidationMessage('Lý do phải có ít nhất 10 ký tự.');
                            return false;
                        }
                        return reason;
                    }

                    // Nếu là "chấp nhận" và có yêu cầu từ khách → không cần lý do
                    return '';
                }

            }).then((result) => {
                if (result.isConfirmed) {
                    const adminNote = result.value;

                    fetch(`/admin/orders/cancel-request/${orderId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                action: action,
                                admin_cancel_note: adminNote
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công',
                                    text: data.success,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Lỗi', data.error || 'Đã xảy ra lỗi khi xử lý yêu cầu.', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Lỗi', 'Không thể gửi yêu cầu. Vui lòng thử lại sau.', 'error');
                        });
                }
            });
        }
    </script>

    {{-- Xử lý yêu cầu trả hàng --}}
    <script>
        function handleReturnAction(url, status) {
            const isRejecting = status === 'rejected';
            const title = isRejecting ? 'Từ chối yêu cầu trả hàng' : 'Xác nhận cập nhật trạng thái';
            const inputLabel = isRejecting ? 'Lý do từ chối (bắt buộc)' : 'Ghi chú nội bộ (tuỳ chọn)';

            Swal.fire({
                title: title,
                input: 'textarea',
                inputLabel: inputLabel,
                inputPlaceholder: 'Nhập nội dung...',
                inputAttributes: {
                    rows: 4
                },
                inputValidator: (value) => {
                    if (isRejecting && !value.trim()) {
                        return 'Bạn phải cung cấp lý do từ chối yêu cầu này!';
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Huỷ bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tạo và submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    const token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(token);

                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PATCH';
                    form.appendChild(method);

                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = status;
                    form.appendChild(statusInput);

                    const note = document.createElement('input');
                    note.type = 'hidden';
                    note.name = 'admin_note';
                    note.value = result.value || '';
                    form.appendChild(note);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection
