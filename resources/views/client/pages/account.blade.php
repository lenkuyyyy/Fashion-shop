@extends('client.pages.page-layout')

@section('content')
    <div class="container-fluid px-4">
        <div class="row g-4">
            <!-- Left Column: User Profile -->
            <div class="col-lg-12 py-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3 rounded-3 border-0 shadow-sm"
                            role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('account.update') }}" class="needs-validation" novalidate enctype="multipart/form-data">
                            @csrf

                            <!-- Avatar Section -->
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('assets/images/default-avatar.jpg') }}"
                                        class="rounded-circle border border-4 border-white shadow-lg" width="120"
                                        height="120" alt="Avatar">
                                    <label for="avatarInput" class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2 cursor-pointer">
                                        <i class="bi bi-camera-fill text-white"></i>
                                        <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                                    </label>
                                </div>
                                <h4 class="mt-3 mb-1 fw-bold text-dark">{{ Auth::user()->name }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-envelope me-2"></i>{{ Auth::user()->email }}
                                </p>
                                @error('avatar')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Form Fields -->
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" name="name"
                                            class="form-control rounded-3 @error('name') is-invalid @enderror"
                                            id="floatingName" value="{{ Auth::user()->name }}" placeholder="Họ tên">
                                        <label for="floatingName">
                                            <i class="bi bi-person me-2"></i>Họ tên
                                        </label>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control rounded-3 bg-light" id="floatingEmail"
                                            value="{{ Auth::user()->email }}" placeholder="Email" disabled>
                                        <label for="floatingEmail">
                                            <i class="bi bi-envelope me-2"></i>Email
                                        </label>
                                    </div>
                                    <div class="d-flex align-items-center mt-2">
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                            <i class="bi bi-shield-check me-1"></i>Đã xác minh
                                        </span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" name="phone_number"
                                            class="form-control rounded-3 @error('phone_number') is-invalid @enderror"
                                            id="floatingPhone" value="{{ Auth::user()->phone_number ?? '' }}"
                                            placeholder="Số điện thoại">
                                        <label for="floatingPhone">
                                            <i class="bi bi-telephone me-2"></i>Số điện thoại
                                        </label>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if (!Auth::user()->google_id)
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="password" name="old_password"
                                                class="form-control rounded-3 @error('old_password') is-invalid @enderror"
                                                id="floatingOldPassword" placeholder="Mật khẩu cũ">
                                            <label for="floatingOldPassword">
                                                <i class="bi bi-lock me-2"></i>Mật khẩu cũ
                                            </label>
                                            @error('old_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="password" name="new_password"
                                                class="form-control rounded-3 @error('new_password') is-invalid @enderror"
                                                id="floatingNewPassword" placeholder="Mật khẩu mới">
                                            <label for="floatingNewPassword">
                                                <i class="bi bi-key me-2"></i>Mật khẩu mới
                                            </label>
                                            @error('new_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea name="address" class="form-control rounded-3 @error('address') is-invalid @enderror" id="floatingAddress"
                                            placeholder="Địa chỉ" style="height: 100px;">{{ Auth::user()->address ?? '' }}</textarea>
                                        <label for="floatingAddress">
                                            <i class="bi bi-geo-alt me-2"></i>Địa chỉ
                                        </label>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 shadow-sm">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Cập nhật thông tin
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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

        .form-floating>.form-control:focus~label {
            color: #667eea;
        }

        .form-floating>.form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Preview avatar before upload
            const avatarInput = document.getElementById('avatarInput');
            const avatarImg = document.querySelector('.rounded-circle.border.border-4.border-white.shadow-lg');
            
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarImg.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

    @vite('resources/js/app.js')
@endsection