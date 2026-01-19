@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <title>Chỉnh sửa người dùng</title>
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
        font-size: 16px;
    }
</style>

<div class="container-fluid">
    <div class="col-lg-12">
        <h1 class="text-center mb-4">Chỉnh sửa người dùng</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Hiển thị lỗi tổng thể --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="user-info-container">
                        {{-- Tên --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Tên:</div>
                            <div class="user-info-value">
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control form-control-lg">
                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Email:</div>
                            <div class="user-info-value">
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control form-control-lg">
                                @error('email')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Số điện thoại --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Số điện thoại:</div>
                            <div class="user-info-value">
                                <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="form-control form-control-lg">
                                @error('phone_number')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Địa chỉ --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Địa chỉ:</div>
                            <div class="user-info-value">
                                <textarea name="address" rows="2" class="form-control form-control-lg">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Quyền --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Quyền:</div>
                            <div class="user-info-value">
                                <select name="role_id" class="form-control form-control-lg">
                                    <!-- Option Quản trị viên -->
                                    <option value="1" {{ old('role_id', $user->role_id) == 1 ? 'selected' : '' }}>Quản trị viên</option>
                                    <!-- Option Khách hàng -->
                                    <option value="2" {{ old('role_id', $user->role_id) == 2 ? 'selected' : '' }}>Khách hàng</option>
                                </select>
                                @error('role_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Trạng thái --}}
                        <div class="user-info-row">
                            <div class="user-info-label">Trạng thái:</div>
                            <div class="user-info-value">
                                <select name="status" class="form-control form-control-lg">
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="btn-group-left mt-4">
                        <!-- Nút quay lại danh sách người dùng -->
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-lg">Quay lại</a>

                        <!-- Nút cập nhật thông tin -->
                        <button type="submit" class="btn btn-success btn-lg">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
