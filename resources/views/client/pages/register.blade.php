@extends('client.pages.page-layout')

@section('content')

<div class="container d-flex justify-content-center align-items-center">
  {{-- nội dung để hiển thị thông báo success hoặc lỗi --}}
 
  <div class="card shadow-lg p-4 w-100" style="max-width: 500px;">
    <div class="text-center">
      <i class="bi bi-person-plus-fill text-primary" style="font-size: 2rem;"></i>
      <h4 class="mt-2">Tạo tài khoản mới</h4>
      <p class="text-muted">Vui lòng điền thông tin bên dưới để đăng ký</p>
    </div>

    @if (session('otp_sent'))
      {{-- FORM XÁC MINH OTP --}}
      <form action="{{ route('register.submit.otp') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label for="otp" class="form-label">Nhập mã OTP đã gửi qua email</label>
          <input type="text" name="otp" class="form-control" placeholder="Nhập mã xác minh" >
          @error('otp')
            <small class="text-danger">{{ $message }}</small>
          @enderror
        </div>
        <button type="submit" class="btn btn-success w-100">✅ Xác minh & Đăng ký</button>
      </form>
    @else
      {{-- FORM NHẬP THÔNG TIN & GỬI OTP --}}
  <form action="{{ route('register.otp.send') }}" method="POST">
    @csrf
    <div class="mb-3"> 
      <label for="name" class="form-label">Họ tên</label>
      <input type="text" class="form-control" name="name" id="name" placeholder="Nguyễn Văn A" value="{{ old('name') }}">
      @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Địa chỉ Email</label>
      <input type="email" class="form-control" name="email" id="email" placeholder="email@example.com" value="{{ old('email') }}">
      @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Mật khẩu</label>
      <input type="password" class="form-control" name="password" id="password" placeholder="********">
      @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="mb-3">
      <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
      <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="********">
    </div>
    <button class="btn btn-outline-primary w-100" type="submit">
      <i class="bi bi-envelope-check me-1"></i> Gửi mã xác minh
    </button>
  </form>

    @endif

            @if (session('resent_code'))
                <div class="alert alert-success mt-3">
                    ✅ Mã xác minh đã được gửi tới email!
                </div>
            @endif

            <div class="text-center mt-3">
                <small>Bạn đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></small>
            </div>
        </div>
    </div>
@endsection
