@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <title>Danh sách người dùng bị cấm</title>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">Danh sách người dùng</h2>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Quay lại danh sách
                    </a>
                </div>

                <!-- Form tìm kiếm -->
                <form class="d-flex mb-3" role="search" action="{{ route('admin.users.banned') }}" method="GET">
                    <div class="input-group">
                        <span class="input-group-text bg-light" id="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Tìm kiếm tài khoản bị cấm"
                            aria-label="Tìm kiếm" aria-describedby="search-icon" name="q" value="{{ request('q') }}">
                        <button class="btn btn-primary" type="submit">Tìm</button>
                    </div>
                </form>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div style="overflow-x:auto;">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">STT</th>
                                <th style="width: 12%;" class="text-center">Họ tên</th>
                                <th style="width: 20%;" class="text-center">Email</th>
                                <th style="width: 10%;" class="text-center">SĐT</th>
                                <th style="width: 23%;" class="text-center">Địa chỉ</th>
                                <th style="width: 15%;" class="text-center">Ngày tạo</th>
                                <th style="width: 15%;" class="text-center">Ngày bị cấm</th>
                                <th style="width: 10%;" class="text-center">Quyền</th>
                                <th style="width: 10%;" class="text-center">Trạng thái</th>
                                <th style="width: 15%;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bannedUsers as $index => $user)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center" title="{{ $user->name }}">{{ $user->name }}</td>
                                    <td class="text-center" title="{{ $user->email }}"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                    <td class="text-center" title="{{ $user->phone_number }}">{{ $user->phone_number }}</td>
                                    <td class="text-center" title="{{ $user->address }}">{{ $user->address }}</td>
                                    <td class="text-center" title="{{ $user->created_at }}">{{ $user->created_at }}</td>
                                    <td class="text-center" title="{{ $user->deleted_at }}">{{ $user->deleted_at }}</td>


                                    <td class="text-center" title="{{ $user->role ? $user->role->name : 'user' }}">
                                        @php $roleName = $user->role ? $user->role->name : 'user'; @endphp
                                        {{ $roleName == 'admin' ? 'Quản trị viên' : 'Khách hàng' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">Bị cấm</span>
                                    </td>
                                    <td class="text-center">
                                        <!-- Khôi phục -->
                                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Khôi phục người dùng này?')" title="Khôi phục"><i class="bi bi-arrow-counterclockwise"></i>Khôi phục</button>
                                        </form>

                                        <!-- Xóa vĩnh viễn -->
                                        <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa vĩnh viễn người dùng này?')" title="Xóa vĩnh viễn"><i class="bi bi-trash"></i>Xóa vĩnh viễn</button>
                                        </form>
                                    </td>

                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">Không có người dùng bị cấm.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                {{ $bannedUsers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
