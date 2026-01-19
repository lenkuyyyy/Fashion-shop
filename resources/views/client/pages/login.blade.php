@extends('client.pages.page-layout')

@section('content')
    <div class="container d-flex justify-content-center align-items-center">
        <div class="card shadow-lg p-4 w-100" style="max-width: 500px;">
            {{-- thông báo lỗi --}}
            @if (session('warning'))
                <div class="container">
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            {{-- thông báo thành công --}}
            @if (session('success'))
                <div class="alert alert-success  text-center">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            {{-- thông báo --}}
            <div class="text-center mb-4">
                <i class="bi bi-box-arrow-in-right text-success" style="font-size: 2rem;"></i>
                <h4 class="mt-2">Đăng nhập tài khoản</h4>
                <p class="text-muted">Chào mừng bạn quay trở lại!</p>
            </div>

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Địa chỉ Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="email@example.com"
                        value="{{ old('email') }}">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="********">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                </div>
                <div class="d-grid">
                    <button class="btn btn-outline-success" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i>
                        Đăng nhập</button>
                </div>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('google.login') }}" class="btn btn-outline-danger w-100">
                    <i class="bi bi-google me-1"></i> Đăng nhập bằng Google
                </a>
            </div>
            <div class="text-center mt-3">
                <button class="btn btn-link text-decoration-none" data-bs-toggle="modal"
                    data-bs-target="#forgotPasswordModal">
                    🔐 Quên mật khẩu?
                </button>
            </div>

            <div class="text-center mt-3">
                <small>Bạn chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></small>
            </div>
        </div>
    </div>

    <!-- Modal Quên Mật Khẩu -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="forgotPasswordModalLabel">Quên mật khẩu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <p>Nhập email để nhận liên kết đặt lại mật khẩu:</p>
                        <input type="email" name="email" class="form-control" placeholder="email@example.com">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning w-100">📩 Gửi liên kết</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
