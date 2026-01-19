@extends('client.pages.page-layout')

@section('content')
<div class="container d-flex justify-content-center align-items-center">
  <div class="card shadow-lg p-4 w-100" style="max-width: 500px;">
  {{-- thông báo thành công --}}      
  @if (session('success'))
    <div class="alert alert-success  text-center">
      <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
  @endif

{{-- thông báo lỗi--}}
 @if (session('error'))
    <div class="alert alert-error  text-center">
      <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
    </div>
  @endif

    <div class="text-center mb-3">
      <i class="bi bi-shield-lock-fill text-warning" style="font-size: 2rem;"></i>
      <h4 class="mt-2">Đặt lại mật khẩu</h4>
      <p class="text-muted">Nhập thông tin để đặt lại mật khẩu mới</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
      @csrf

      <input type="hidden" name="token" value="{{ $token }}">

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" >
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Mật khẩu mới</label>
        <input id="password" type="password" class="form-control" name="password" >
        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" >
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-success">✅ Đặt lại mật khẩu</button>
      </div>
    </form>

    <div class="text-center mt-3">
      <a href="{{ route('login') }}" class="text-decoration-none">Quay lại đăng nhập</a>
    </div>
  </div>
</div>
@endsection
