@extends('admin.layouts.AdminLayouts')
@section('title-page')
    <h3>Quản lý thống báo khách hàng</h3>
@endsection
@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row g-4 mb-4">
            <!-- Nội dung chính: Danh sách thông báo -->    
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="card-title mb-0">Danh sách thông báo đã gửi</h4>
                    </div>
                    <div class="card-body p-3">
                    @if ($notifications->count())
                        <div class="list-group list-group-flush">
                            @foreach ($notifications as $notification)
                                <div class="list-group-item py-3 px-3 border-bottom">
                                    <div class="d-flex justify-content-between">
                                        <div class="me-3">
                                            <h5 class="fw-bold mb-1">
                                                <i class="bi bi-bell-fill text-primary me-2"></i>{{ $notification->title }}
                                            </h5>
                                            <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                            <small class="text-secondary">
                                                <i class="bi bi-person-fill"></i> Người nhận: #{{ $notification->user_id }} &nbsp; | &nbsp;
                                                <i class="bi bi-tag-fill"></i> 
                                                <span class="badge bg-{{ $notification->type === 'order' ? 'warning' : 'secondary' }}">
                                                    {{ ucfirst($notification->type) }}
                                                </span>
                                                &nbsp; | &nbsp;
                                                <i class="bi bi-clock-fill"></i> {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            @if ($notification->is_read)
                                                <span class="badge bg-success">Đã đọc</span>
                                            @else
                                                <span class="badge bg-danger">Chưa đọc</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">Chưa có thông báo nào được gửi.</div>
                    @endif
                </div>

                    <div class="card-footer">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>

            <!-- Phần aside: Form thêm mới thông báo -->
            <div class="col-md-5">
                <a href="{{ route('notifications') }}" class="btn btn-warning mb-2"><i class="bi bi-bell-fill"></i>Thông báo của admin</a>
                <div class="card border-primary shadow-sm">
                    <div class="card-header">
                        <h3 class="text-primary">Thêm thông báo mới</h3>
                    </div>
                    <div class="card-body">
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

                        <form action="{{ route('admin.customer-notifications.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề</label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title') }}"
                                       placeholder="Nhập tiêu đề thông báo">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Nội dung</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" 
                                          name="message" 
                                          rows="4"
                                          placeholder="Nhập nội dung thông báo">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                        <label for="type" class="form-label">Loại thông báo</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                            <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>Hệ thống</option>
                            <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>email</option>
                            <option value="order" {{ old('type') == 'order' ? 'selected' : '' }}>Đơn hàng</option>
                            <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Sản phẩm</option>
                            <option value="news" {{ old('type') == 'news' ? 'selected' : '' }}>Tin tức</option>
                            <option value="promotion" {{ old('type') == 'promotion' ? 'selected' : '' }}>Khuyến mại</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                            <div class="mb-3">
                                <label for="target" class="form-label">Gửi đến</label>
                                <select class="form-select @error('target') is-invalid @enderror" 
                                        id="target" 
                                        name="target">
                                    <option value="all_customers" {{ old('target') == 'all_customers' ? 'selected' : '' }}>Tất cả khách hàng</option>
                                    <option value="all_admins" {{ old('target') == 'all_admins' ? 'selected' : '' }}>Tất cả admin</option>
                                </select>
                                @error('target')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Gửi thông báo</button>
                        </form>
                    </div>
                </div>
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
</script>
@endsection
