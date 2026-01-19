@extends('client.pages.page-layout')

@section('content')
<div class="container">
    <div class="about-us">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="left-image">
                        <img src="{{ asset('assets/images/anh-bia-thoi-trang (2).jpg') }}" alt="Fashion Newsletters">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="right-content">
                        <h4>Các Bản Tin Thời Trang Của Chúng Tôi</h4>
                        <span>Cập nhật xu hướng thời trang mới nhất và ưu đãi đặc biệt từ HN-447.</span>
                        <div class="quote">
                            <i class="fa fa-quote-left"></i><p>Thời trang là cách bạn kể câu chuyện của mình mà không cần lời nói.</p>
                        </div>
                        <p>Các bản tin thời trang của chúng tôi mang đến những thông tin nóng hổi về xu hướng, mẹo phối đồ và các chương trình giảm giá độc quyền. Đừng bỏ lỡ cơ hội trở thành người dẫn đầu phong cách!</p>
                        <ul>
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-behance"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($topNews->isNotEmpty())
    <section class="our-team">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading">
                        <h2>Bản Tin Nổi Bật</h2>
                        <span>Những bản tin được xem nhiều nhất trên thị trường thời trang.</span>
                    </div>
                </div>
                @foreach($topNews as $news)
                    <div class="col-lg-4">
                        <div class="team-item">
                            <div class="thumb">
                                <a href="{{ route('news.show', $news->id) }}">
                                    {{-- ĐÃ SỬA --}}
                                    <img src="{{ $news->image_url }}" alt="{{ $news->title }}" style="aspect-ratio: 1/1; object-fit: cover;">
                                </a>
                            </div>
                            <div class="down-content">
                                <h4>{{ $news->title }}</h4>
                                <span>Lượt xem: {{ $news->views }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    <section class="our-services">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading">
                        <h2>Tất Cả Tin Tức</h2>
                        <span>Các bài viết mới nhất.</span>
                    </div>
                </div>
                @forelse($latestNews as $news)
                    <div class="col-lg-4">
                        <div class="service-item" style="padding: 20px;">
                            <a href="{{ route('news.show', $news->id) }}">
                                 {{-- ĐÃ SỬA --}}
                                <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="mb-3" style="width: 100%; aspect-ratio: 16/9; object-fit: cover;">
                            </a>
                            <h4>{{ $news->title }}</h4>
                            <p>{{ Str::limit($news->content, 100) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12">
                        <p class="text-center">Chưa có bài viết nào để hiển thị.</p>
                    </div>
                @endforelse
            </div>
            
            {{-- Phân trang --}}
            <div class="row mt-4">
                <div class="col-lg-12 d-flex justify-content-center">
                    {{ $latestNews->links() }}
                </div>
            </div>
        </div>
    </section>
    </div>
@endsection