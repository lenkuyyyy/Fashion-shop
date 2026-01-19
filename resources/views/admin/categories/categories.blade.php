@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <title>Quản lý danh mục</title>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="row g-4 mb-4">
                <div class="col-md-9">
                    <div class="d-flex justify-content-between mb-3">
                        <h2>Danh sách danh mục</h2>
                        <ul class="nav nav-tabs mb-3"> 
                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}"

                                    href="{{ route('admin.categories.index') }}">Danh mục</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.categories.trashed') ? 'active' : '' }}"
                                    href="{{ route('admin.categories.trashed') }}">Danh mục đã xóa</a>
                            </li>
                        </ul>
                        <div>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-success me-2">
                                <i class="bi bi-plus-lg"></i> Thêm danh mục
                            </a>
                            <a href="{{ route('admin.categories.trashed') }}" class="btn btn-secondary">
                                <i class="bi bi-trash"></i> Xem danh mục đã xóa
                            </a>
                        </div>
                    </div>
                           @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Thành công!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Thất bại!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    {{-- Form tìm kiếm --}}
                    <form class="d-flex mb-3" role="search" action="{{ route('admin.categories.index') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-light" id="search-icon">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Tìm kiếm danh mục" aria-label="Tìm kiếm"
                                aria-describedby="search-icon" name="q" value="{{ request('q') }}">
                            <div class="col-auto">
                                <select class="form-select ms-1" name="status">
                                    <option value="">--Tất cả Trạng thái--</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Còn bán
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng
                                        bán</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" type="submit">Tìm</button>
                        </div>
                    </form>
                    {{-- Kết thúc tìm kiếm --}}

                    <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                            <tr>
                                <th scope="col">STT</th>
                                <th scope="col">Tên danh mục</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Nhóm danh mục</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Ngày tạo</th>
                                <th scope="col">Ngày cập nhật</th>
                                <th scope="col" width="237">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $index => $category)
                                <tr>
                                    <td scope="row">{{ $categories->firstItem() + $index }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>{{ optional($category->group)->name }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $category->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $category->status == 'active' ? 'Còn bán' : 'Ngừng bán' }}
                                        </span>
                                    </td>
                                    <td>{{ $category->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $category->updated_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil-square"></i> Sửa
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                                <i class="bi bi-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Không có danh mục nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Phân trang --}}
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Page navigation">
                            {{ $categories->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                    {{-- Kết thúc phân trang --}}
                </div>

                <div class="col-md-3">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">TOP Danh mục bán chạy</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                </button>
                            </div>
                        </div>
                        @forelse ($topCategories as $category)
                            <div class="card-body border-bottom d-flex align-items-center gap-2">
                                <img src="https://tse1.mm.bing.net/th?id=OIP.B9OwLAa-NJ8fajNWalNA6QHaJ4&pid=Api&P=0&h=180"
                                    alt="img" class="rounded" height="30px">
                                <strong><a href="{{ route('admin.categories.show', $category) }}"
                                        class="text-decoration-none ms-4">{{ $category->name }}</a></strong>
                            </div>
                        @empty
                            <div class="card-body">
                                <p>Chưa có danh mục nào.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
