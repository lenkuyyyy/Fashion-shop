@extends('client.pages.page-layout')

@section('content')
<div class="page-heading about-page-heading" id="top">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="inner-content">
                    <h2>{{ $news->title }}</h2>
                    <span>Đăng ngày: {{ $news->published_at->format('d/m/Y') }} &nbsp; &bull; &nbsp; Lượt xem: {{ $news->views }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="about-us">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="text-center mb-4">
                     {{-- ĐÃ SỬA --}}
                    <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="img-fluid rounded">
                </div>
                {{-- Sử dụng {!! !!} để xuất HTML, nl2br để giữ nguyên các xuống dòng --}}
                <p>{!! nl2br(e($news->content)) !!}</p>
                <hr>
                <div class="text-center">
                    <a href="{{ route('fashion-newsletters') }}" class="btn btn-dark">Quay lại danh sách tin tức</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection