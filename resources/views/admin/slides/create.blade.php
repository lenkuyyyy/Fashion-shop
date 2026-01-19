@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Thêm Slide</h3>
@endsection

@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
        <form action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Hình ảnh</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"></textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="order" class="form-label">Thứ tự</label>
                <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="0">
                @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="news_id" class="form-label">Tin tức</label>
                <select class="form-control @error('news_id') is-invalid @enderror" id="news_id" name="news_id">
                    <option value="">Chọn tin tức</option>
                    @foreach($newsList as $news)
                        <option value="{{ $news->id }}">{{ $news->title }}</option>
                    @endforeach
                </select>
                @error('news_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
@endsection