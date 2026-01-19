@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <title>Quản lý người dùng</title>
@endsection

@section('content')

<style>
    table {
        table-layout: fixed;
        width: 100%;
    }
    th, td {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }
</style>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row g-4 mb-4">
            <div class="col-12">
                <!-- Tiêu đề chính -->
                <h2 class="mb-3">Danh sách người dùng</h2>

                <!-- Thanh tìm kiếm + nút thêm + nút danh sách bị cấm -->
                <div class="d-flex mb-3">
                    <!-- Form tìm kiếm -->
                    <form class="d-flex flex-grow-1 me-2" role="search" action="{{ route('admin.users.index') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-light" id="search-icon"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Nhập tên hoặc email để tìm kiếm" aria-label="Tìm kiếm" aria-describedby="search-icon" name="search" value="{{ $search }}">
                            <button class="btn btn-primary" type="submit">Tìm</button>
                        </div>
                    </form>

                    <!-- Nút Thêm người dùng -->
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success ms-2">
                        <i class="bi bi-plus-circle"></i> Thêm
                    </a>

                    <!-- Nút danh sách bị cấm -->
                    <a href="{{ route('admin.users.banned') }}" class="btn btn-danger ms-2">
                        <i class="bi bi-slash-circle"></i> Danh sách bị cấm
                    </a>
                </div>

                <!-- Hiển thị thông báo thành công -->
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Bảng danh sách người dùng -->
                <div style="overflow-x:auto;">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">STT</th>
                                <th class="text-center" style="width: 12%;">Họ tên</th>
                                <th class="text-center" style="width: 20%;">Email</th>
                                <th class="text-center" style="width: 10%;">SĐT</th>
                                <th class="text-center" style="width: 23%;">Địa chỉ</th>
                                <th class="text-center" style="width: 15%;">Ngày tạo</th>
                                <th class="text-center" style="width: 10%;">Quyền</th>
                                <th class="text-center" style="width: 10%;">Trạng thái</th>
                                <th class="text-center" style="width: 15%;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td class="text-center" title="{{ $users->firstItem() + $index }}">{{ $users->firstItem() + $index }}</td>
                                    <td class="text-center" title="{{ $user->name }}">{{ $user->name }}</td>
                                    <td class="text-center" title="{{ $user->email }}">
                                        <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    </td>
                                    <td class="text-center" title="{{ $user->phone_number }}">{{ $user->phone_number }}</td>
                                    <td class="text-center" title="{{ $user->address }}">{{ $user->address }}</td>
                                    <td class="text-center" title="{{ $user->created_at }}">{{ $user->created_at }}</td>
                                    <td class="text-center" title="{{ $user->role ? $user->role->name : 'user' }}">
                                        @php $roleName = $user->role ? $user->role->name : 'user'; @endphp
                                        {{ $roleName == 'admin' ? 'Quản trị viên' : 'Khách hàng' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $user->status == 'active' ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <!-- Nút sửa -->
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm" title="Chỉnh sửa người dùng">
                                           <i class="bi bi-pencil-square"></i> Sửa
                                        </a>

                                        <!-- Nút cấm người dùng -->
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn cấm tài khoản này?')">
                                                <i class="bi bi-slash-circle"></i>  Cấm
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Không có người dùng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
