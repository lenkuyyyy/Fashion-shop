@extends('client.pages.page-layout')

@section('content')
    <div class="container">
        <h4 class="mb-4"><i class="bi bi-bell-fill text-warning me-2"></i>Thông báo của bạn <span
                class="badge bg-danger unread-count">{{ $unreadCount }}</span></h4>
        @if (session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif
        <!-- Bộ lọc + thao tác -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 bg-light p-3 rounded shadow-sm">
            <div class="d-flex flex-wrap gap-3">
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" id="filter-all" checked>
                    <label class="form-check-label" for="filter-all">Tất cả</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" id="filter-read">
                    <label class="form-check-label" for="filter-read">Đã đọc</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" id="filter-unread">
                    <label class="form-check-label" for="filter-unread">Chưa đọc</label>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                <form action="{{ route('client.notifications.markAllRead') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-eye me-1"></i> Đánh dấu tất cả đã
                        đọc</button>
                </form>
            </div>
        </div>

        <!-- Danh sách thông báo -->
        @guest
            <div class="alert alert-info text-center">
                <i class="bi bi-inbox me-2"></i> Hãy đăng nhập để nhận các thông báo ưu đãi dành riêng cho thành viên bạn nhé!!
            </div>
        @endguest
        @auth
            <div class="accordion" id="notificationAccordion">
                @forelse ($notifications as $notification)
                    <div class="accordion-item notification-card mb-3 {{ $notification->is_read ? 'notification-item-read' : 'notification-item-unread' }} bg-white shadow-sm border rounded"
                        data-read="{{ $notification->is_read ? 'true' : 'false' }}" data-id="{{ $notification->id }}">
                        <div class="d-flex align-items-center p-3  ms-3">
                            <!-- Nội dung thông báo -->
                            <div class="flex-grow-1">
                                <h2 class="accordion-header" id="heading{{ $notification->id }}">
                                    <button class="accordion-button collapsed px-3 py-2 rounded" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#notif{{ $notification->id }}">
                                        <div>
                                            @php
                                                // Xác định nhãn và màu sắc dựa trên loại thông báo
                                                $typeLabels = [
                                                    'system' => ['label' => 'Hệ thống', 'color' => 'secondary'],
                                                    'email' => ['label' => 'Email', 'color' => 'info'],
                                                    'order' => ['label' => 'Đơn hàng', 'color' => 'primary'],
                                                    'product' => ['label' => 'Sản phẩm', 'color' => 'success'],
                                                    'news' => ['label' => 'Tin tức', 'color' => 'warning'],
                                                    'promotion' => ['label' => 'Khuyến mãi', 'color' => 'danger'],
                                                    'other' => ['label' => 'Khác', 'color' => 'dark'],
                                                ];

                                                // Lấy loại thông báo và xác định nhãn, màu sắc
                                                $type = $notification->type;
                                                $typeData = $typeLabels[$type] ?? [
                                                    'label' => 'Không xác định',
                                                    'color' => 'light',
                                                ];
                                            @endphp

                                            <span class="badge bg-{{ $typeData['color'] }} mb-1 px-3 py-2">
                                                {{-- Hiển thị nhãn loại thông báo --}}
                                                {{ $typeData['label'] }}
                                            </span>
                                            
                                            <div class="fw-semibold mb-1">
                                                {{ $notification->title }}
                                            </div>
                                            <small class="text-muted">Ngày nhận:
                                                {{ $notification->created_at->format(' H:i. d/m/Y') }}</small>
                                        </div>
                                    </button>
                                </h2>
                                <div id="notif{{ $notification->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#notificationAccordion">
                                    <div class="accordion-body p-3">
                                        {{ $notification->message }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-secondary text-center">
                        <i class="bi bi-bell-slash me-2"></i> Bạn chưa có thông báo nào.
                    </div>
                @endforelse

            </div>
        @endauth
        <!-- Phân trang -->
        <div class="text-center mt-4">
            {{ $notifications->links() }}
        </div>
    </div>

    <style>
        .notification-card {
            transition: box-shadow 0.2s ease-in-out;
        }

        .notification-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .notification-item-unread {
            background-color: #f8f9fa;
        }

        .notification-item-read {
            background-color: #ffffff;
        }

        .badge {
            font-size: 0.85em;
        }

        .unread-count {
            font-size: 0.9em;
        }
    </style>
@endsection

@section('scripts')
    <script>
        const filters = document.querySelectorAll('.filter-checkbox');
        const notifications = document.querySelectorAll('.notification-card');
        const selectAllBtn = document.getElementById('select-all');
        const notificationCheckboxes = document.querySelectorAll('.notification-checkbox');

        // Lọc thông báo
        filters.forEach(filter => {
            filter.addEventListener('change', () => {
                filters.forEach(f => {
                    if (f !== filter) f.checked = false;
                });
                const filterValue = filter.id;
                notifications.forEach(notification => {
                    const isRead = notification.dataset.read === 'true';
                    notification.style.display =
                        (filterValue === 'filter-all') ||
                        (filterValue === 'filter-read' && isRead) ||
                        (filterValue === 'filter-unread' && !isRead) ? 'block' : 'none';
                });
            });
        });
    </script>
@endsection
