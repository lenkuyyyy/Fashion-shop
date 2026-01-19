@extends('admin.layouts.AdminLayouts')
@section('title-page')
    <h3>Thông báo của khách hàng đến admin</h3>
      {{-- nội dung phần này e có chỉnh lại layout nếu anh gộp sẽ lỗi a lấy file của e này nhé thay cho trước đó--}}
@endsection
@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
      
        <div class="row g-4 mb-4 justify-content-center border shadow p-4">
            <!-- Nút lọc và quản lý -->
        <div class="col-md-11">
                {{-- thông báo --}}
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        {{-- kêt thúc thông báo --}}
                <div class="d-flex justify-content-between ">
                    <div>
                        <button class="btn btn-danger me-2" id="filter-unread"><i class="bi bi-bell-fill"></i>Chưa đọc</button>
                        <button class="btn btn-success me-2" id="filter-read"><i class="bi bi-pencil-square"></i>Đã đọc</button>
                    </div>
                    <div>
                        <a href="{{ route('customer-notifications') }}" class="btn btn-warning"> <i class="bi bi-bell-fill"></i>Quản lý thông báo khách hàng</a>
                    </div>
                </div>

            </div>
            
            <!-- Nội dung chính -->
            <div class="col-md-11">
                @forelse ($notifications as $notification)
                    <div class="card border-primary shadow-sm mb-1 notification-card hover-card" data-read="{{ $notification->is_read ? '1' : '0' }}">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-9">
                                    <h3 class="card-title mb-0">
                                        <span class="badge {{ $notification->is_read ? 'bg-secondary' : 'bg-danger' }} me-2">
                                            {{ $notification->is_read ? 'Đã Xem' : 'New' }}
                                        </span>
                                        {{ $notification->title }}
                                    </h3>
                                </div>
                                <div class="col-3 d-flex align-items-center justify-content-end">
                                    <span class="text-muted small me-3">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    <a href="{{ route('admin.notifications.markAsRead', $notification->id) }}" 
                                       class="btn btn-sm btn-success me-2" 
                                       title="Đánh dấu đã đọc">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-info" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#notif{{ $notification->id }}" 
                                            aria-expanded="false" 
                                            aria-controls="notif{{ $notification->id }}">
                                        <i class="bi bi-plus-lg"></i>
                                        <i class="bi bi-dash-lg d-none"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="notif{{ $notification->id }}">
                            <div class="card-body">
                                {{ $notification->message }}
                                @if ($notification->order_id)
                                    <a href="{{ route('admin.orders.show', $notification->order_id) }}" 
                                       class="btn btn-sm btn-outline-primary mt-2">
                                        Xem đơn hàng
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-primary shadow-sm">
                        <div class="card-body text-center">
                            Không có thông báo nào.
                        </div>
                    </div>
                @endforelse

                <!-- Phân trang -->
                @if ($notifications->hasPages())
                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    // Xử lý toggle collapse và icon
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
        button.addEventListener('click', () => {
            const expandIcon = button.querySelector('.bi-plus-lg');
            const collapseIcon = button.querySelector('.bi-dash-lg');
            expandIcon.classList.toggle('d-none');
            collapseIcon.classList.toggle('d-none');
        });
    });

    // Xử lý lọc thông báo
    document.getElementById('filter-unread').addEventListener('click', () => {
        filterNotifications(false);
    });
    document.getElementById('filter-read').addEventListener('click', () => {
        filterNotifications(true);
    });

    function filterNotifications(readStatus) {
        const cards = document.querySelectorAll('.notification-card');
        cards.forEach(card => {
            const isRead = card.getAttribute('data-read') === '1';
            card.style.display = (readStatus === isRead) ? 'block' : 'none';
        });
    }

    // Xử lý đánh dấu tất cả là đã đọc
    document.getElementById('mark-all-read').addEventListener('change', function() {
        if (this.checked) {
            fetch('{{ route('admin.notifications.markAllRead') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    document.querySelectorAll('.notification-card').forEach(card => {
                        card.setAttribute('data-read', '1');
                        const badge = card.querySelector('.badge');
                        badge.classList.remove('bg-danger');
                        badge.classList.add('bg-secondary');
                        badge.textContent = 'Đã Xem';
                    });
                    this.checked = false; // Reset checkbox
                }
            });
        }
    });
</script>
@endsection