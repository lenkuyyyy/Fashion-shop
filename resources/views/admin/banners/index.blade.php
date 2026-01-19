@extends('admin.layouts.AdminLayouts')

@section('title-page')
    <h3>Quản lý banner</h3>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cập nhật banner</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.admin.banners.update') }}" method="POST" enctype="multipart/form-data" id="banner-form">
                        @csrf
                        <div class="form-group">
                            <label for="image_1">Ảnh banner 1</label>
                            <input type="file" class="form-control-file" id="image_1" name="image_1" accept="image/*">
                            @if($banner && $banner->image_path_1)
                                <img src="{{ asset('storage/' . $banner->image_path_1) }}" alt="Banner 1" class="img-thumbnail mt-2" style="max-width: 150px;">
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="image_2">Ảnh banner 2</label>
                            <input type="file" class="form-control-file" id="image_2" name="image_2" accept="image/*">
                            @if($banner && $banner->image_path_2)
                                <img src="{{ asset('storage/' . $banner->image_path_2) }}" alt="Banner 2" class="img-thumbnail mt-2" style="max-width: 150px;">
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="image_3">Ảnh banner 3</label>
                            <input type="file" class="form-control-file" id="image_3" name="image_3" accept="image/*">
                            @if($banner && $banner->image_path_3)
                                <img src="{{ asset('storage/' . $banner->image_path_3) }}" alt="Banner 3" class="img-thumbnail mt-2" style="max-width: 150px;">
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="title">Tiêu đề</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $banner->title ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label for="subtitle">Phụ đề</label>
                            <input type="text" class="form-control" id="subtitle" name="subtitle" value="{{ old('subtitle', $banner->subtitle ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label>Trạng thái chữ trên banner: <span id="text-status">{{ $banner && $banner->show_text ? 'Đang hiển thị' : 'Đang ẩn' }}</span></label>
                            <input type="hidden" id="show_text" name="show_text" value="{{ old('show_text', $banner->show_text ?? 0) }}">
                            <button type="button" id="toggle-text" class="btn {{ $banner && $banner->show_text ? 'btn-danger' : 'btn-success' }}">
                                {{ $banner && $banner->show_text ? 'Ẩn chữ' : 'Hiển thị chữ' }}
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Xem trước banner</h6>
                </div>
                <div class="card-body">
                    <div class="left-content">
                        <div class="thumb" style="height: 300px; position: relative;">
                            <div class="inner-content" id="preview-text" style="{{ $banner && $banner->show_text ? '' : 'display: none;' }}">
                                <h4 id="preview-title">{{ $banner->title ?? 'Chào mừng đến với chúng tôi' }}</h4>
                                <span id="preview-subtitle">{{ $banner->subtitle ?? 'Thời trang, phong cách &amp; thể hiện cá tính' }}</span>
                                <div class="main-border-button">
                                    <a href="#">Mua ngay!</a>
                                </div>
                            </div>
                            <div class="banner-slider">
                                <img id="preview-img1" src="{{ $banner && $banner->image_path_1 ? asset('storage/' . $banner->image_path_1) : asset('assets/images/left-banner-image.jpg') }}" alt="Banner 1" class="active">
                                <img id="preview-img2" src="{{ $banner && $banner->image_path_2 ? asset('storage/' . $banner->image_path_2) : asset('assets/images/left-banner-image.jpg') }}" alt="Banner 2">
                                <img id="preview-img3" src="{{ $banner && $banner->image_path_3 ? asset('storage/' . $banner->image_path_3) : asset('assets/images/left-banner-image.jpg') }}" alt="Banner 3">
                            </div>
                            <div class="slider-controls mt-3 text-center">
                                <button class="btn btn-outline-secondary btn-sm mx-2" id="prev-slide">Previous</button>
                                <button class="btn btn-outline-secondary btn-sm mx-2" id="next-slide">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.card { border-radius: 10px; }
.card-header { background-color: #f8f9fc; }
.img-thumbnail { border-radius: 5px; }
.alert-success, .alert-danger { border-radius: 5px; }
.left-content .thumb { position: relative; width: 100%; max-width: 100%; overflow: hidden; }
.left-content .banner-slider img {
    width: 100%;
    height: 300px;
    object-fit: contain;
    display: none;
}
.left-content .banner-slider img.active { display: block; }
.left-content .inner-content {
    position: absolute;
    left: 50px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
}
.left-content .inner-content h4 {
    color: #fff;
    margin-top: -10px;
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 15px;
}
.left-content .inner-content span {
    font-size: 14px;
    color: #fff;
    font-weight: 400;
    font-style: italic;
    display: block;
    margin-bottom: 20px;
}
#text-status { font-weight: bold; color: #007bff; }
.slider-controls { margin-top: 10px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle button cho ẩn/hiện chữ
    const toggleBtn = document.getElementById('toggle-text');
    const showTextInput = document.getElementById('show_text');
    const textStatus = document.getElementById('text-status');
    const previewText = document.getElementById('preview-text');

    toggleBtn.addEventListener('click', function () {
        const isShown = showTextInput.value === '1';
        showTextInput.value = isShown ? '0' : '1';
        toggleBtn.textContent = isShown ? 'Hiển thị chữ' : 'Ẩn chữ';
        toggleBtn.classList.toggle('btn-danger', !isShown);
        toggleBtn.classList.toggle('btn-success', isShown);
        textStatus.textContent = isShown ? 'Đang ẩn' : 'Đang hiển thị';
        previewText.style.display = isShown ? 'none' : 'block';
    });

    // Preview text realtime
    document.getElementById('title').addEventListener('input', function () {
        document.getElementById('preview-title').textContent = this.value || 'Chào mừng đến với chúng tôi';
    });
    document.getElementById('subtitle').addEventListener('input', function () {
        document.getElementById('preview-subtitle').innerHTML = this.value || 'Thời trang, phong cách &amp; thể hiện cá tính';
    });

    // Preview ảnh local
    function previewImage(inputId, previewId) {
        document.getElementById(inputId).addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    previewImage('image_1', 'preview-img1');
    previewImage('image_2', 'preview-img2');
    previewImage('image_3', 'preview-img3');

    // Slide tự động và điều khiển thủ công
    const previewImages = document.querySelectorAll('.banner-slider img');
    let currentPreview = 0;
    let autoSlideInterval;

    function showPreviewImage(index) {
        currentPreview = (index + previewImages.length) % previewImages.length;
        previewImages.forEach((img, i) => {
            img.classList.toggle('active', i === currentPreview);
        });
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            showPreviewImage(currentPreview + 1);
        }, 3000);
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }

    document.getElementById('prev-slide').addEventListener('click', () => {
        stopAutoSlide();
        showPreviewImage(currentPreview - 1);
        startAutoSlide();
    });

    document.getElementById('next-slide').addEventListener('click', () => {
        stopAutoSlide();
        showPreviewImage(currentPreview + 1);
        startAutoSlide();
    });

    showPreviewImage(currentPreview);
    startAutoSlide();
});
</script>
@endsection