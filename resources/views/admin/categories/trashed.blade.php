@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <title>Danh mục đã xóa</title>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between mb-3">
                <h2>Danh mục đã xóa</h2>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách danh mục
                </a>
            </div>

            {{-- Form tìm kiếm --}}
            <form class="d-flex mb-3" role="search" action="{{ route('admin.categories.trashed') }}" method="GET">
                <div class="input-group">
                    <span class="input-group-text bg-light" id="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Tìm kiếm danh mục đã xóa"
                        aria-label="Tìm kiếm" aria-describedby="search-icon" name="q" value="{{ request('q') }}">
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
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Ngày xóa</th>
                        <th scope="col" width="237">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $index => $category)
                        <tr>
                            <td scope="row">{{ $categories->firstItem() + $index }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>
                                <span
                                    class="badge {{ $category->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $category->status == 'active' ? 'Còn bán' : 'Ngừng bán' }}
                                </span>
                            </td>
                            <td>{{ $category->deleted_at->format('Y-m-d') }}</td>
                            <td>
                                <form action="{{ route('admin.categories.restore', $category->id) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success me-1"
                                        onclick="return confirm('Bạn có chắc muốn khôi phục danh mục này?')">
                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                    </button>
                                </form>
                                <form action="{{ route('admin.categories.forceDelete', $category->id) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Bạn có chắc muốn xóa vĩnh viễn danh mục này?')">
                                        <i class="bi bi-trash"></i> Xóa vĩnh viễn
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Không có danh mục nào đã xóa.</td>
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
    </div>
@endsection