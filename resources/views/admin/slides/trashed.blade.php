@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Thùng rác Slide</h3>
@endsection

@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
        <table class="table table-bordered table-striped table-hover text-center">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tiêu đề</th>
                    <th scope="col">Ngày xóa</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trashedSlides as $slide)
                    <tr>
                        <td>{{ $slide->id }}</td>
                        <td>{{ $slide->title }}</td>
                        <td>{{ $slide->deleted_at }}</td>
                        <td>
                            <form action="{{ route('admin.slides.restore', $slide->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Khôi phục slide này?')">Khôi phục</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Chưa có dữ liệu</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $trashedSlides->links() }}
        </div>
        <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
    </div>
</div>
@endsection