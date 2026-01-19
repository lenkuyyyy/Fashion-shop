@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Sửa Slide: {{ $slide->title }}</h3>
@endsection

@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.slides.update', $slide->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $slide->title) }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                        <div class="mt-2">
                            <p>Ảnh hiện tại:</p>
                            {{-- SỬA LẠI CÁCH HIỂN THỊ ẢNH --}}
                            <img src="{{ $slide->image_url }}" width="200" alt="Slide Image">
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $slide->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="order" class="form-label">Thứ tự</label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $slide->order) }}">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            {{-- SỬA LẠI LOGIC CHỌN TRẠNG THÁI --}}
                            <option value="1" {{ old('status', $slide->status) == 1 ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ old('status', $slide->status) == 0 ? 'selected' : '' }}>Tạm dừng</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="news_id" class="form-label">Tin tức liên kết</label>
                        <select class="form-select @error('news_id') is-invalid @enderror" id="news_id" name="news_id">
                            <option value="">Không liên kết</option>
                            @foreach($newsList as $news)
                                <option value="{{ $news->id }}" {{ old('news_id', $slide->news_id) == $news->id ? 'selected' : '' }}>
                                    {{ $news->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('news_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection