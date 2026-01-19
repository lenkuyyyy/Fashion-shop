@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Thùng rác Thương hiệu</h3>
@endsection

@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
        {{-- Hiển thị thông báo thành công nếu có --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong class="me-2">Thành công!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-info text-white fw-bold">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h5 class="mb-0">Danh sách Thương hiệu trong Thùng rác</h5>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-light text-primary">
                            <i class="bi bi-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Tên thương hiệu</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Ngày xóa</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($trashedBrands->count())
                            @foreach($trashedBrands as $brand)
                                <tr>
                                    <td>{{ $brand->id }}</td>
                                    <td>{{ $brand->name }}</td>
                                    <td>{{ $brand->slug }}</td>
                                    <td>{{ $brand->deleted_at->format('d-m-Y H:i') }}</td>
                                    <td class="d-flex justify-content-center align-items-center">
                                        <!-- Nút khôi phục -->
                                        <form action="{{ route('admin.brands.restore', $brand->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục thương hiệu này?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success me-1">
                                                <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                            </button>
                                        </form>
                                        
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">Chưa có thương hiệu nào trong thùng rác</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{-- Phân trang --}}
                <div class="d-flex justify-content-center">
                    {{ $trashedBrands->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection