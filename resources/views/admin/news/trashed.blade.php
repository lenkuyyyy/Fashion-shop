@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Thùng rác Bài viết</h3>
@endsection

@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
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
                        <h5 class="mb-0">Danh sách Bài viết trong Thùng rác</h5>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="{{ route('admin.news.index') }}" class="btn btn-light text-primary">
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
                            <th scope="col">Tiêu đề</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Ngày xóa</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($trashedNews->count())
                            @foreach($trashedNews as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->slug }}</td>
                                    <td>{{ $item->deleted_at->format('d-m-Y H:i') }}</td>
                                    <td class="d-flex justify-content-center align-items-center">
                                        <form action="{{ route('admin.news.restore', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục bài viết này?')">
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
                                <td colspan="5" class="text-center">Chưa có bài viết nào trong thùng rác</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $trashedNews->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection