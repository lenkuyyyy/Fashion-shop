@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Danh sách đơn hàng</h3>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="row g-4 mb-4">
                <div class="col-md-12">
                    {{-- Tìm kiếm đơn hàng --}}
                    <form class="d-flex mb-1" role="search" action="{{ route('admin.orders.index') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-light" id="search-icon">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="text" class="form-control" placeholder="Tìm kiếm đơn hàng" aria-label="Tìm kiếm"
                                aria-describedby="search-icon" name="q" value="{{ request('q') }}">

                            <select class="form-select ms-1" name="status" style="max-width: 200px;">
                                <option value="">--Tất cả Trạng thái--</option>
                                @foreach ($statuses as $key => $status)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="btn btn-primary ms-2" type="submit">Tìm</button>
                            <a class="btn btn-secondary ms-2" href="{{ route('admin.orders.index') }}">Đặt lại</a>
                        </div>
                    </form>
                    {{-- Kết thúc tìm kiếm --}}

                    {{-- Hiển thị thông báo --}}
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

                    {{-- Hiển thị danh sách đơn hàng --}}
                    @if ($noResults)
                        <div class="alert alert-warning" role="alert">
                            Không tìm thấy đơn hàng nào.
                        </div>
                    @endif
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Mã đơn hàng</th>
                                <th scope="col">Ngày đặt hàng</th>
                                <th scope="col">Tên người nhận</th>
                                <th scope="col">Địa chỉ</th>
                                <th scope="col">Số điện thoại</th>
                                <th scope="col">Phương thức thanh toán</th>
                                <th scope="col">Trạng thái thanh toán</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Trả hàng / Hoàn tiền</th>
                                <th scope="col">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $order->shippingAddress->name }}</td>
                                    <td style="overflow-wrap: break-word; max-width: 250px;">
                                        {{ $order->shippingAddress->full_address }}
                                    </td>
                                    <td>{{ $order->shippingAddress->phone_number }}</td>
                                    <td style="color: {{ $order->getPaymentMethod($order->payment_method)['color'] }} ">
                                        {{ $order->getPaymentMethod($order->payment_method)['label'] }}</td>
                                    <td style="color: {{ $order->getPaymentStatus($order->payment_status)['color'] }} ">
                                        {{ $order->getPaymentStatus($order->payment_status)['label'] }}</td>
                                    <td>
                                        @php
                                            $status = $order->getStatusLabel();
                                        @endphp
                                        {{-- Hiển thị trạng thái đơn hàng --}}
                                        <span class="badge {{ $status['color'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>

                                    {{-- Xử lý yêu cầu trả hàng --}}
                                    <td>
                                        @if ($order->returnRequest)
                                            @php
                                                $returnStatus = $order->returnRequest->return_status;
                                                $status = $order->returnRequest->status;
                                                $paymentMethod = $order->payment_method;
                                                $paymentStatus = $order->payment_status;

                                                // Xác định hành động có thể thực hiện
                                                $canApprove = $status === 'requested';
                                                $canReject = $status === 'requested';
                                                $canRefund =
                                                    $status === 'approved' &&
                                                    in_array($paymentMethod, ['online', 'bank_transfer']) &&
                                                    $paymentStatus !== 'refunded' &&
                                                    $paymentStatus === 'refund_in_processing';
                                                // Với COD, cho phép đánh dấu hoàn tất (không hoàn tiền)
                                                $canMarkDone = $status === 'approved' && $paymentMethod === 'cod';
                                            @endphp

                                            <div class="d-flex flex-column gap-1">
                                                {{-- Hiển thị trạng thái --}}
                                                <span class="badge bg-{{ $returnStatus['color'] }}">
                                                    <i class="bi {{ $returnStatus['icon'] }}"></i>
                                                    {{ $returnStatus['title'] }}
                                                </span>

                                                {{-- Nút hành động --}}
                                                @if ($canApprove)
                                                    <button class="btn btn-success btn-sm"
                                                        onclick="handleReturnAction('{{ route('admin.return-requests.update', $order->returnRequest->id) }}', 'approved')">
                                                        ✅ Duyệt
                                                    </button>
                                                @endif

                                                @if ($canReject)
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="handleReturnAction('{{ route('admin.return-requests.update', $order->returnRequest->id) }}', 'rejected')">
                                                        ❌ Từ chối
                                                    </button>
                                                @endif

                                                {{-- Xử lý với COD --}}
                                                @if ($canMarkDone)
                                                    <button class="btn btn-outline-success btn-sm"
                                                        onclick="handleReturnAction('{{ route('admin.return-requests.update', $order->returnRequest->id) }}', 'refunded')">
                                                        ✅ Đánh dấu đã hoàn tất
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Form ẩn --}}
                                            <form method="POST" id="return-request-form-{{ $order->returnRequest->id }}"
                                                style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status">
                                                <input type="hidden" name="admin_note">
                                            </form>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Các nút và modal --}}
                                    <td>
                                        <div class="d-flex gap-2">
                                            {{-- Nút Chi tiết đơn hàng --}}
                                            <a class="btn btn-sm btn-primary"
                                                href="{{ route('admin.orders.show', $order->id) }}">
                                                <i class="bi bi-info-circle"></i> Chi tiết
                                            </a>

                                            @if (!in_array($order->status, ['delivered', 'completed', 'cancelled', 'refund_in_processing', 'refunded']))
                                                {{-- Nút xác nhận trạng thái tiếp theo --}}
                                                @php
                                                    $cancelMessages = [
                                                        'pending' =>
                                                            'Đơn hàng đang chờ xác nhận. Bạn có chắc muốn huỷ không?',
                                                        'processing' =>
                                                            'Đơn hàng đang xử lý. Bạn có chắc muốn huỷ không?',
                                                        'shipped' =>
                                                            'Đơn đã giao cho đơn vị vận chuyển. Bạn có chắc muốn huỷ không?',
                                                    ];

                                                    $cancelMessage =
                                                        $cancelMessages[$order->status] ??
                                                        'Bạn có chắc muốn huỷ đơn hàng này không?';

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
                                                            'label' => 'Đã giao hàng',
                                                            'next_status' => 'delivered',
                                                        ],
                                                    ];

                                                    $action = $statusActions[$order->status] ?? null;
                                                @endphp

                                                @if ($action)
                                                    <button class="btn btn-sm btn-success"
                                                        onclick="
                                                        submitStatusUpdate('{{ route('admin.orders.update', $order->id) }}',
                                                            '{{ $action['next_status'] }}', '{{ $action['label'] }}')">
                                                        <i class="bi bi-pencil-square"></i> {{ $action['label'] }}
                                                    </button>
                                                @endif

                                                {{-- Nút Hủy đơn --}}
                                                <button type="button" class="btn btn-danger btn-sm open-cancel-modal"
                                                    data-order-id="{{ $order->id }}">
                                                    <i class="bi bi-x-circle"></i> Huỷ đơn
                                                </button>

                                                {{-- Nút từ chối yêu cầu huỷ đơn --}}
                                                @if (
                                                    $order->cancellation_requested &&
                                                        !$order->cancel_confirmed &&
                                                        $order->payment_status !== 'failed')
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectCancelRequestModal{{ $order->id }}">
                                                        <i class="bi bi-x-circle"></i> Từ chối yêu cầu huỷ
                                                    </button>
                                                @endif
                                            @endif

                                            {{-- Nút Đánh dấu đã hoàn tiền --}}
                                            @if ($order->status === 'cancelled' && $order->payment_status === 'refund_in_processing')
                                                <form method="POST"
                                                    action="{{ route('admin.orders.refunded', $order->id) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Bạn có chắc chắn đánh dấu đơn hàng này là đã hoàn tiền?');">
                                                        <i class="bi bi-cash-coin"></i> Đã hoàn tiền
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Nút Đánh dấu đã hoàn tiền cho trạng thái refund_in_processing --}}
                                            @if ($order->status === 'refund_in_processing' && $order->returnRequest && $order->returnRequest->status === 'approved' && in_array($order->payment_method, ['online', 'bank_transfer']) && $order->payment_status !== 'refunded')
                                                <form method="POST"
                                                    action="{{ route('admin.orders.refunded', $order->id) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Bạn có chắc chắn đánh dấu đơn hàng này là đã hoàn tiền?');">
                                                        <i class="bi bi-cash-coin"></i> Đã hoàn tiền
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        @php
                                            $hideCustomerReason =
                                                !empty($order->cancel_reason) &&
                                                !empty($order->admin_cancel_note) &&
                                                $order->cancel_confirmed &&
                                                $order->status !== 'cancelled';
                                        @endphp

                                        <!-- Modal xác nhận huỷ đơn hàng -->
                                        <div class="modal fade" id="cancelOrderModal{{ $order->id }}" tabindex="-1"
                                            aria-labelledby="cancelOrderModalLabel{{ $order->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form method="POST" class="cancel-order-form"
                                                    action="{{ route('admin.orders.cancel', $order->id) }}"
                                                    data-cancellation-requested="{{ $order->cancellation_requested ? 'true' : 'false' }}">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title"
                                                                id="cancelOrderModalLabel{{ $order->id }}">
                                                                {{ $hideCustomerReason
                                                                    ? 'Bạn muốn huỷ đơn hàng này?'
                                                                    : 'Xác nhận huỷ đơn hàng của khách hàng ' . $order->user->name }}
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Đóng"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            {{-- Hiển thị lý do khách hàng yêu cầu huỷ --}}
                                                            @if ($order->cancel_reason && !$hideCustomerReason)
                                                                <div class="mb-3">
                                                                    <p class="mb-1"><strong>Lý do khách hàng yêu cầu huỷ
                                                                            đơn:</strong></p>
                                                                    <div class="border rounded p-2 bg-light text-dark">
                                                                        {{ $order->cancel_reason }}
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            {{-- Ghi chú từ admin --}}
                                                            <div class="mb-3">
                                                                <label for="admin_cancel_note_{{ $order->id }}"
                                                                    class="form-label">Lý do huỷ đơn (Admin):</label>
                                                                <textarea name="admin_cancel_note" id="admin_cancel_note_{{ $order->id }}" class="form-control" rows="3"></textarea>
                                                                <div class="invalid-feedback">
                                                                    Vui lòng nhập lý do huỷ đơn (tối thiểu 10 ký tự).
                                                                </div>
                                                            </div>

                                                            <div class="alert alert-warning">
                                                                Bạn chắc chắn muốn huỷ đơn hàng này? Hành động này không thể
                                                                hoàn tác.
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" class="btn btn-danger">Xác nhận huỷ
                                                                đơn</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Modal từ chối yêu cầu huỷ đơn hàng -->
                                        <div class="modal fade" id="rejectCancelRequestModal{{ $order->id }}"
                                            tabindex="-1"
                                            aria-labelledby="rejectCancelRequestModalLabel{{ $order->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form method="POST" class="reject-cancel-request-form"
                                                    action="{{ route('admin.orders.cancel.reject', $order->id) }}">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning text-dark">
                                                            <h5 class="modal-title"
                                                                id="rejectCancelRequestModalLabel{{ $order->id }}">
                                                                Từ chối yêu cầu huỷ đơn hàng của khách hàng
                                                                {{ $order->user->name }}
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Đóng"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            {{-- Hiển thị lý do khách hàng yêu cầu huỷ --}}
                                                            @if ($order->cancel_reason)
                                                                <div class="mb-3">
                                                                    <p class="mb-1"><strong>Lý do khách yêu cầu huỷ
                                                                            đơn:</strong></p>
                                                                    <div class="border rounded p-2 bg-light text-dark">
                                                                        {{ $order->cancel_reason }}
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            {{-- Ghi chú lý do từ chối của admin --}}
                                                            <div class="mb-3">
                                                                <label for="admin_cancel_note_reject_{{ $order->id }}"
                                                                    class="form-label">
                                                                    Lý do từ chối (Admin):
                                                                </label>
                                                                <textarea name="admin_cancel_note" id="admin_cancel_note_reject_{{ $order->id }}" class="form-control"
                                                                    rows="3"></textarea>
                                                                <div class="invalid-feedback">
                                                                    Vui lòng nhập lý do từ chối (tối thiểu 10 ký tự).
                                                                </div>
                                                            </div>

                                                            <div class="alert alert-warning">
                                                                Bạn chắc chắn muốn <strong>từ chối yêu cầu huỷ</strong> này?
                                                                Khách hàng sẽ nhận được thông báo phản hồi từ hệ thống.
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" class="btn btn-warning">Xác nhận từ
                                                                chối</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Phân trang -->
                    {{ $orders->links() }}
                    {{-- Kết thúc phân trang --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Form lấy status mới --}}
    <form id="statusUpdateForm" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" id="statusInput">
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Xử lý yêu cầu trả hàng --}}
    <script>
        function handleReturnAction(actionUrl, newStatus) {
            Swal.fire({
                title: 'Ghi chú xử lý',
                input: 'textarea',
                inputLabel: 'Lý do (hiển thị cho khách hàng)',
                inputPlaceholder: 'Nhập lý do xử lý...',
                inputAttributes: {
                    'aria-label': 'Nhập ghi chú xử lý'
                },
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Huỷ',
                inputValidator: (value) => {
                    // ✅ Chỉ bắt buộc nếu là rejected
                    if (newStatus === 'rejected' && !value.trim()) {
                        return 'Bạn phải cung cấp lý do từ chối yêu cầu này!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = actionUrl;

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PATCH';
                    form.appendChild(methodInput);

                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = '{{ csrf_token() }}';
                    form.appendChild(tokenInput);

                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = newStatus;
                    form.appendChild(statusInput);

                    const noteInput = document.createElement('input');
                    noteInput.type = 'hidden';
                    noteInput.name = 'admin_note';
                    noteInput.value = result.value;
                    form.appendChild(noteInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    {{-- hiển thị thông báo --}}
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
    </script>

    {{-- hiển thị modal huỷ đơn, từ chối huỷ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gán click mở modal
            document.querySelectorAll('.open-cancel-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.dataset.orderId;
                    const modalId = `cancelOrderModal${orderId}`;
                    const modalElement = document.getElementById(modalId);

                    if (modalElement) {
                        const bsModal = bootstrap.Modal.getOrCreateInstance(modalElement);
                        bsModal.show();
                    } else {
                        console.error('Không tìm thấy modal với ID:', modalId);
                    }
                });
            });

            document.querySelectorAll('.reject-cancel-request-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const textarea = form.querySelector('textarea[name="admin_cancel_note"]');
                    if (!textarea.value.trim() || textarea.value.trim().length < 10) {
                        textarea.classList.add('is-invalid');
                        e.preventDefault();
                    } else {
                        textarea.classList.remove('is-invalid');
                    }
                });
            });

            // ✅ Chỉ gán validate cho mỗi form một lần duy nhất sau khi DOM tải xong
            document.querySelectorAll('.cancel-order-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const textarea = this.querySelector('textarea[name="admin_cancel_note"]');
                    const reason = textarea.value.trim();

                    // ✅ Nếu có yêu cầu huỷ từ khách => cho phép bỏ qua nhập lý do
                    const isRequestFromCustomer = this.dataset.cancellationRequested === 'true';

                    if (!isRequestFromCustomer && reason.length < 10) {
                        e.preventDefault(); // chặn gửi form
                        textarea.classList.add('is-invalid');
                        textarea.focus();
                    } else {
                        textarea.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endsection
