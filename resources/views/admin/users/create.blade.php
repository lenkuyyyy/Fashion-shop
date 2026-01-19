@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <title>Thêm người dùng</title>
@endsection

@section('content')
<style>
    .user-info-container {
        margin: 0 auto;
        width: 60%;
    }

    .user-info-row {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #e0e0e0;
    }

    .user-info-label {
        font-weight: bold;
        font-size: 24px;
        width: 35%;
        text-align: right;
        padding-right: 20px;
        white-space: nowrap;
    }

    .user-info-value {
        width: 65%;
        text-align: left;
    }

    .form-control-lg {
        font-size: 18px;
        padding: 10px;
    }

    .avatar-image {
        width: 128px;
        height: 128px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
    }

    .btn-group-left {
        display: flex;
        justify-content: flex-start;
        gap: 12px;
    }

    .text-danger {
        font-size: 14px;
        margin-top: 4px;
        display: block;
    }
</style>

<div class="container-fluid">
    <div class="col-lg-12">
        <h1 class="text-center mb-4">Thêm người dùng</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="user-info-container">

                        {{-- Tên --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Tên:</div>
                            <div class="user-info-value">
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-lg">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Email:</div>
                            <div class="user-info-value">
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Mật khẩu --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Mật khẩu:</div>
                            <div class="user-info-value">
                                <input type="password" name="password" class="form-control form-control-lg">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Xác nhận mật khẩu --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Xác nhận mật khẩu:</div>
                            <div class="user-info-value">
                                <input type="password" name="password_confirmation" class="form-control form-control-lg">
                            </div>
                        </div>

                        {{-- Số điện thoại --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Số điện thoại:</div>
                            <div class="user-info-value">
                                <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="form-control form-control-lg">
                                @error('phone_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Địa chỉ --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Địa chỉ:</div>
                            <div class="user-info-value">
                                <textarea name="address" rows="2" class="form-control form-control-lg">{{ old('address') }}</textarea>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Quyền --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Quyền:</div>
                            <div class="user-info-value">
                                <select name="role_id" class="form-control form-control-lg">
                                    <option value="">-- Chọn quyền --</option>
                                    <option value="1" {{ old('role_id') == 1 ? 'selected' : '' }}>Quản trị viên</option>
                                    <option value="2" {{ old('role_id') == 2 ? 'selected' : '' }}>Khách hàng</option>
                                </select>
                                @error('role_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        {{-- Trạng thái --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Trạng thái:</div>
                            <div class="user-info-value">
                                <select name="status" class="form-control form-control-lg">
                                    <option value="">-- Chọn trạng thái --</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="btn-group-left mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-lg">Quay lại</a>
                        <button type="submit" class="btn btn-primary btn-lg">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- JS preview avatar --}}
<script>
    const avatarInput = document.querySelector('input[name="avatar"]');
    const previewAvatar = document.getElementById('preview_avatar');

    avatarInput.addEventListener('change', function(){
        const file = this.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                previewAvatar.src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            previewAvatar.src = "{{ asset('dist/assets/img/user1-128x128.jpg') }}";
        }
    });
</script>
@endsection

@extends('admin.layouts.AdminLayouts')

@section('title')
    <title>Thêm người dùng</title>
@endsection

@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
        <h1>Thêm người dùng</h1>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Họ tên</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Số điện thoại</label>
                <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}">
                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="role_id" class="form-label">Quyền</label>
                <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror">
                    <option value="">Chọn quyền</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Thêm</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</div>
@endsection