@extends('client.pages.page-layout')

@section('content')
    <div class="container pt-4">
        <div class="row">
            <!-- Sidebar: Bộ lọc sản phẩm -->
            <div class="col-lg-3">
                <div class="shop__sidebar">
                    {{-- Search --}}
                    <div class="shop__sidebar__search mb-4">
                        <form action="{{ route('products-client') }}" method="GET" class="d-flex">
                            @foreach (request()->query() as $key => $value)
                                @if ($key !== 'search')
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..."
                                value="{{ request()->search }}" class="form-control me-2 rounded-3 shadow-sm">
                            <button type="submit" class="btn btn-primary rounded-3 shadow-sm">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>

                    {{-- Filter Sections --}}
                    <div class="shop__sidebar__filters">
                        {{-- Categories --}}
                        <div class="card border-0 shadow-sm rounded-3 mb-3">
                            <div class="card-heading p-3 bg-light rounded-top-3" data-bs-toggle="collapse" data-bs-target="#categoriesCollapse" aria-expanded="false" aria-controls="categoriesCollapse">
                                <h6 class="text-dark fw-bold text-uppercase d-flex align-items-center">
                                    <i class="bi bi-list me-2"></i> Danh mục
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </h6>
                            </div>
                            <div class="collapse" id="categoriesCollapse">
                                <div class="card-body p-3">
                                    <div class="shop__sidebar__categories">
                                        <ul class="list-unstyled">
                                            @foreach($categories as $cat)
                                                <li class="{{ request()->route('slug') == $cat->slug ? 'active' : '' }}">
                                                    <a href="{{ route('products-client', array_merge(request()->route()->parameters(), request()->query(), ['slug' => $cat->slug])) }}" class="d-block py-2 px-3 rounded-2">
                                                        {{ $cat->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Branding --}}
                        <div class="card border-0 shadow-sm rounded-3 mb-3">
                            <div class="card-heading p-3 bg-light rounded-top-3" data-bs-toggle="collapse"
                                data-bs-target="#brandingCollapse" aria-expanded="false" aria-controls="brandingCollapse">
                                <h6 class="text-dark fw-bold text-uppercase d-flex align-items-center">
                                    <i class="bi bi-tags me-2"></i> Thương hiệu
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </h6>
                            </div>
                            <div class="collapse" id="brandingCollapse">
                                <div class="card-body p-3">
                                    <div class="shop__sidebar__brand">
                                        <ul class="list-unstyled">
                                            @foreach ($brands as $brand)
                                                <li class="{{ request()->brand == $brand->id ? 'active' : '' }}">
                                                    <a href="{{ route('products-client', array_merge(request()->route()->parameters(), request()->query(), ['brand' => $brand->id])) }}" class="d-block py-2 px-3 rounded-2">
                                                        {{ $brand->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Filter Price --}}
                        <div class="card border-0 shadow-sm rounded-3 mb-3">
                            <div class="card-heading p-3 bg-light rounded-top-3" data-bs-toggle="collapse"
                                data-bs-target="#priceCollapse" aria-expanded="false" aria-controls="priceCollapse">
                                <h6 class="text-dark fw-bold text-uppercase d-flex align-items-center">
                                    <i class="bi bi-currency-dollar me-2"></i> Lọc theo giá
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </h6>
                            </div>
                            <div class="collapse" id="priceCollapse">
                                <div class="card-body p-3">
                                    <div class="shop__sidebar__price">
                                     @php
                                        $priceRanges = [
                                            ['min' => 0, 'max' => 200000, 'label' => '0 - 200.000 đ'],
                                            ['min' => 200000, 'max' => 400000, 'label' => '200.000 - 400.000 đ'],
                                            ['min' => 400000, 'max' => 600000, 'label' => '400.000 - 600.000 đ'],
                                            ['min' => 600000, 'max' => 800000, 'label' => '600.000 - 800.000 đ'],
                                            ['min' => 800000, 'max' => 1000000, 'label' => '800.000 - 1.000.000 đ'],
                                            ['min' => 1000000, 'max' => null, 'label' => '1.000.000 đ +'],
                                        ];
                                    @endphp

                                    @foreach ($priceRanges as $range)
                                        <li class="{{ request()->price_min == $range['min'] && request()->price_max == $range['max'] ? 'active' : '' }}">
                                            <a href="{{ route('products-client', array_merge(request()->route()->parameters(), request()->query(), ['price_min' => $range['min'], 'price_max' => $range['max']])) }}">
                                                {{ $range['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Size --}}
                        <div class="card border-0 shadow-sm rounded-3 mb-3">
                            <div class="card-heading p-3 bg-light rounded-top-3" data-bs-toggle="collapse"
                                data-bs-target="#sizeCollapse" aria-expanded="false" aria-controls="sizeCollapse">
                                <h6 class="text-dark fw-bold text-uppercase d-flex align-items-center">
                                    <i class="bi bi-aspect-ratio me-2"></i> Kích cỡ
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </h6>
                            </div>
                            <div class="collapse" id="sizeCollapse">
                                <div class="card-body p-3">
                                    <div class="shop__sidebar__size">
                                        @foreach ($sizes as $size)
                                            <label for="size-{{ $size }}"
                                                class="btn btn-outline-dark rounded-3 m-1 {{ request()->size == $size ? 'active' : '' }}">
                                                {{ $size }}
                                                <input type="radio" name="size" id="size-{{ $size }}"
                                                onchange="location.href='{{ route('products-client', array_merge(request()->route()->parameters(), request()->query(), ['size' => $size])) }}'"
                                                @if(request()->size == $size) checked @endif>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Colors --}}
                        <div class="card border-0 shadow-sm rounded-3 mb-3">
                            <div class="card-heading p-3 bg-light rounded-top-3" data-bs-toggle="collapse"
                                data-bs-target="#colorsCollapse" aria-expanded="false" aria-controls="colorsCollapse">
                                <h6 class="text-dark fw-bold text-uppercase d-flex align-items-center">
                                    <i class="bi bi-palette me-2"></i> Màu sắc
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </h6>
                            </div>
                            <div class="collapse" id="colorsCollapse">
                                <div class="card-body p-3">
                                    <div class="shop__sidebar__color">
                                        <ul class="list-unstyled">
                                            @foreach (collect($colors)->unique(function ($item) {
                                                        return strtolower($item);
                                                    }) as $color)
                                                <li class="mb-2 {{ strtolower(request()->color) == strtolower($color) ? 'active' : '' }}">
                                                    <a href="{{ route('products-client', array_merge(request()->route()->parameters(), request()->query(), ['color' => $color])) }}"
                                                       class="d-flex align-items-center py-1 px-2 rounded-2 {{ strtolower(request()->color) == strtolower($color) ? 'bg-primary text-white' : '' }}">
                                                        <span class="me-2" style="display:inline-block;width:20px;height:20px;border-radius:50%;background:{{ $color }};border:1px solid #ccc;"></span>
                                                        <span>{{ ucfirst($color) }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="col-lg-9">
                <!-- Sắp xếp -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="bi bi-list me-2"></i>Danh sách sản phẩm</h3>
                    <div class="btn-group align-items-center hover-zoom">
                        <a href="{{ request()->route('slug') ? route('products-client', request()->route('slug')) . '?sort=newest' : route('products-client', array_merge(request()->query(), ['sort' => 'newest'])) }}"
                            class="btn btn-outline-primary {{ request()->sort == 'newest' || !request()->sort ? 'active' : '' }}">
                            <i class="bi bi-stars"></i> Mới nhất trong tuần
                        </a>
                        
                        <a href="{{ request()->route('slug') ? route('products-client', request()->route('slug')) . '?sort=best_selling' : route('products-client', array_merge(request()->query(), ['sort' => 'best_selling'])) }}"
                            class="btn btn-outline-danger {{ request()->sort == 'best_selling' ? 'active' : '' }}">
                            <i class="bi bi-fire"></i> Đang bán chạy
                        </a>
                    </div>
                </div>

                <!-- Hiển thị thông tin bộ lọc hiện tại -->
                <div class="mb-4">
                    @php
                        $filters = [];
                        $isHeaderSearch = request()->has('header_search') && request()->header_search;
                    @endphp
                    @if ($isHeaderSearch)
                        <h5 class="mb-3">Kết quả tìm kiếm cho từ khoá '<span class="text-primary">{{ request()->header_search }}</span>'</h5>
                    @endif
                    @php
                        if (request()->has('category') && request()->category) {
                            $category = $categories->firstWhere('id', request()->category);
                            $filters[] = 'Danh mục: ' . ($category ? $category->name : 'Không xác định');
                        }
                        // Lọc theo slug
                        if(request()->route('slug')) {
                            $category = $categories->firstWhere('slug', request()->route('slug'));
                            $filters[] = 'Danh mục: ' . ($category ? $category->name : 'Không xác định');
                        }
                        // Lọc theo brand
                        if (request()->has('brand') && request()->brand) {
                            // Sidebar truyền brand là id
                            $brand = $brands->firstWhere('id', request()->brand);
                            $filters[] = 'Thương hiệu: ' . ($brand ? $brand->name : 'Không xác định');
                        }
                        // Lọc theo size
                        if (request()->has('size') && request()->size) {
                            $sizeLabel = is_array($sizes) && in_array(request()->size, $sizes) ? request()->size : (is_object($sizes) && $sizes->contains(request()->size) ? request()->size : request()->size);
                            $filters[] = 'Kích cỡ: ' . $sizeLabel;
                        }
                        if (request()->has('color') && request()->color) {
                            $colorLabel = ucfirst(e(request()->color));
                            $filters[] = 'Màu sắc: ' . $colorLabel;
                        }
                        if (request()->has('price_min') && request()->price_min !== null) {
                            $filters[] =
                                'Giá: ' .
                                number_format(request()->price_min) .
                                ' đ - ' .
                                (request()->price_max ? number_format(request()->price_max) . ' đ' : '+');
                        }
                        if ($searchTerm && !$isHeaderSearch) {
                            $filters[] = 'Tìm kiếm: ' . $searchTerm;
                        }
                    @endphp
                    @if (!empty($filters))
                        <p class="text-muted">Lọc theo: {{ implode(', ', $filters) }}</p>
                        <a href="{{ route('products-client') }}" class="btn btn-sm btn-outline-danger">Xóa bộ lọc</a>
                    @endif
                    @if ($noResults && $isHeaderSearch)
                        <div class="alert alert-warning mt-3">
                            Không tìm thấy sản phẩm nào phù hợp với từ khoá '<span class="fw-bold text-danger">{{ request()->header_search }}</span>'.
                            <a href="{{ route('products-client') }}" class="alert-link">Xóa tìm kiếm</a> để xem tất cả sản phẩm.
                        </div>
                    @elseif ($noResults)
                        <div class="alert alert-warning mt-3">
                            Không tìm thấy sản phẩm nào phù hợp. <a href="{{ route('products-client') }}"
                                class="alert-link">Xóa bộ lọc</a> để xem tất cả sản phẩm.
                        </div>
                    @endif
                </div>

                <!-- Danh sách sản phẩm -->
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border">
                                <div class="position-relative py-2">
                                    <div class="product-image-hover position-relative">
                                        <a href="{{ route('detail-product', ['id' => $product->id]) }}">
                                            <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                                class="card-img-top img-fluid px-2" alt="{{ $product->name }}"
                                                style="height: 250px; object-fit: cover;">
                                            <div class="hover-detail-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.4); color: #fff; opacity: 0; transition: opacity 0.3s;">
                                                <span class="fs-5 fw-bold">Xem chi tiết sản phẩm</span>
                                            </div>
                                        </a>
                                    </div>
                                    <style>
                                        .product-image-hover {
                                            position: relative;
                                            overflow: hidden;
                                        }
                                        .product-image-hover .hover-detail-overlay {
                                            pointer-events: none;
                                        }
                                        .product-image-hover:hover .hover-detail-overlay {
                                            opacity: 1 !important;
                                            pointer-events: auto;
                                        }
                                    </style>
                                    @php
                                        /* lấy data sản phẩm để truyền vào view,
                                     sau đó dùng JS để xử lý thêm vào localStorage để lưu wishlist cho user chưa đăng nhập */
                                        $productData = [
                                            'id' => $product->id,
                                            'status' => $product->status,
                                        ];
                                    @endphp
                                    {{-- Hiển thị nút yêu thích theo trạng thái người dùng hiện tại --}}
                                    @if (Auth::check())
                                        <form action="{{ route('wishlist.store') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button class="btn btn-danger position-absolute top-0 end-0 m-2"
                                                type="submit"><i class="bi bi-heart"></i></button>
                                        </form>
                                    @else
                                        {{-- Hiển thị nút yêu thích cho khách chưa đăng nhập --}}
                                        <button class="btn btn-danger position-absolute top-0 end-0 m-2 add-to-wishlist"
                                            data-product='@json($productData)'
                                            style="position: relative;">
                                            <i class="bi bi-heart"></i>
                                            <span class="wishlist-tooltip position-absolute top-100 end-0 translate-middle-x px-2 py-1 bg-dark text-white rounded-2 small" style="display:none; white-space:nowrap; z-index:10;">
                                                Yêu thích
                                            </span>
                                        </button>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                document.querySelectorAll('.add-to-wishlist').forEach(function(btn) {
                                                    btn.addEventListener('mouseenter', function() {
                                                        const tooltip = btn.querySelector('.wishlist-tooltip');
                                                        if (tooltip) tooltip.style.display = 'block';
                                                    });
                                                    btn.addEventListener('mouseleave', function() {
                                                        const tooltip = btn.querySelector('.wishlist-tooltip');
                                                        if (tooltip) tooltip.style.display = 'none';
                                                    });
                                                });
                                            });
                                        </script>
                                    @endif

                                    {{-- Hiển thị badge "New" nếu sản phẩm mới tạo trong 7 ngày gần nhất --}}
                                    @php
                                        $isNewProduct = \Carbon\Carbon::parse($product->created_at)->gt(now()->subDays(7));
                                    @endphp
                                    @if ($isNewProduct)
                                        <span class="badge bg-warning text-dark position-absolute d-flex align-items-center justify-content-center"
                                            style="top: 0.5rem; right: 3.2rem; height: 2.30rem; min-width: 2.5rem; z-index:1; font-size: 0.95rem;">
                                            New
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body text-center d-flex flex-column">
                                    <h5 class="card-title text-truncate">{{ $product->name }}</h5>
                                    <div class="d-flex flex-column align-items-center gap-2 ">
                                        <div class="text-warning">
                                   @php
                                        // Lấy trung bình rating chỉ của các đánh giá đã được duyệt
                                        $approvedReviews = $product->reviews->where('status', 'approved');
                                        $avgRating = $approvedReviews->count() > 0 ? $approvedReviews->avg('rating') : 0;
                                    @endphp
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $avgRating)
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                        </div>
                                        {{-- <div class="text-muted">
                                            <i class="bi bi-basket3"></i> {{ $product->sales_count ?? 0}} bán
                                        </div>
                                        <div class="text-muted">
                                            <i class="bi bi-heart"></i> {{ $product->likes_count ?? 0 }} yêu thích
                                        </div> --}}
                                    </div>
                                  
                                    @php
                                        $prices = $product->variants->pluck('price');
                                        $minPrice = $prices->min();
                                        $maxPrice = $prices->max();
                                    @endphp


                                    @if ($minPrice == $maxPrice)
                                        <p class="text-danger fw-bold mb-3">{{ number_format($minPrice) }}đ</p>
                                    @else
                                        <p class="text-danger fw-bold mb-3">{{ number_format($minPrice) }}đ - {{ number_format($maxPrice) }}đ</p>
                                    @endif
                                    <div class="d-flex gap-2 mt-auto justify-content-center">
                                        <a href="{{ route('detail-product', ['id' => $product->id]) }}"
                                            class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i> Chi tiết sản phẩm
                                        </a>
                                        {{-- <a href="" class="btn btn-outline-danger"><i class="bi bi-cart"></i>
                                            Thêm</a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted">Không có sản phẩm nào.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    @if ($products->hasPages())
                        {{ $products->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal fade" id="wishlistModal" tabindex="-1" aria-labelledby="wishlistModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header bg-success text-white rounded-top-4">
                        <h5 class="modal-title fw-bold" id="wishlistModalLabel">
                            <i class="bi bi-heart-fill me-2"></i> Thông báo
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <i class="bi bi-check-circle-fill text-success display-4 mb-3"></i>
                        <p class="mb-0 fs-5">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div class="modal fade" id="wishlistErrorModal" tabindex="-1" aria-labelledby="wishlistErrorModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header bg-danger text-white rounded-top-4">
                        <h5 class="modal-title fw-bold" id="wishlistErrorModalLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Lỗi
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <i class="bi bi-x-circle-fill text-danger display-4 mb-3"></i>
                        <p class="mb-0 fs-5">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom CSS -->
    <style>
        /* General card styling for product cards */
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            border-radius: 18px;
        }

        .card:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            transform: translateY(-8px) scale(1.03);
        }

        .card-img-top {
            border-radius: 14px;
            transition: transform 0.4s cubic-bezier(.4,2,.3,1);
        }
        .card:hover .card-img-top {
            transform: scale(1.08);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 0.5rem;
        }

        .text-danger.fw-bold {
            font-size: 1 rem;
            letter-spacing: 0.5px;
        }

        .btn-outline-primary {
            border-radius: 25px;
            font-weight: 500;
            padding: 0.45rem 1.2rem;
            transition: background 0.2s, color 0.2s;
        }
        .btn-outline-primary:hover {
            background: #007bff;
            color: #fff;
        }

        /* Sidebar filter đẹp hơn */
        .shop__sidebar {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
            padding: 18px 14px 18px 14px;
        }
        .shop__sidebar__filters .card {
            border-radius: 12px;
            margin-bottom: 18px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.04);
        }
        .shop__sidebar__filters .card-heading {
            font-size: 1rem;
            font-weight: 700;
            background: #f5f7fa;
            border-bottom: 1px solid #e9ecef;
            border-radius: 12px 12px 0 0;
            padding: 13px 18px;
        }
        .shop__sidebar__filters .card-body {
            padding: 14px 18px;
        }

        /* Filter item active */
        .shop__sidebar__categories ul li.active a,
        .shop__sidebar__price ul li.active a,
        .shop__sidebar__brand ul li.active a {
            background: linear-gradient(90deg, #e7f1ff 60%, #f5faff 100%);
            color: #007bff;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0,123,255,0.06);
        }

        /* Filter item hover */
        .shop__sidebar__categories ul li a:hover,
        .shop__sidebar__price ul li a:hover,
        .shop__sidebar__brand ul li a:hover {
            background: #f0f6ff;
            color: #0056b3;
        }

        /* Filter size đẹp hơn */
        .shop__sidebar__size label {
            border-radius: 20px;
            padding: 7px 18px;
            font-size: 15px;
            margin: 0 6px 8px 0;
            background: #f8f9fa;
            border: 1.5px solid #dee2e6;
            transition: all 0.2s;
        }
        .shop__sidebar__size label.active,
        .shop__sidebar__size label:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        /* Filter color tròn, bóng đẹp */
        .shop__sidebar__color ul li a span:first-child {
            border: 1.5px solid #e0e0e0;
            box-shadow: 0 1px 6px rgba(0,0,0,0.08);
            transition: border 0.2s, box-shadow 0.2s;
        }
        .shop__sidebar__color ul li a.bg-primary span:first-child {
            border: 2px solid #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.10);
        }

        /* Badge "New" nổi bật */
        .badge.bg-warning {
            font-size: 0.95rem;
            padding: 0.4em 1.1em;
            /* border-radius: 1.2em; */
            box-shadow: 0 1px 6px rgba(255,193,7,0.13);
        }

        /* Responsive: 2 sản phẩm/row trên tablet */
        @media (max-width: 991.98px) {
            .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        /* Responsive: 1 sản phẩm/row trên mobile */
        @media (max-width: 575.98px) {
            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .shop__sidebar {
                margin-bottom: 24px;
            }
        }
    </style>

    <script src="{{ asset('assets/js/cart.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            // Hàm mở collapse nếu có tham số URL phù hợp
            function initializeCollapse(collapseId, param) {
                const collapseElement AVIATORdocument.getElementById(collapseId);
                if (!collapseElement) return;

                const collapseInstance = new bootstrap.Collapse(collapseElement, {
                    toggle: false
                });

                // Kiểm tra nếu có tham số trong URL thì mở collapse
                if (urlParams.has(param)) {
                    collapseElement.classList.add('show'); // Đặt trạng thái mở ban đầu
                }
            }

            // Khởi tạo các collapse dựa trên tham số URL
            initializeCollapse('categoriesCollapse', 'category');
            initializeCollapse('brandingCollapse', 'brand');
            initializeCollapse('priceCollapse', 'price_min');
            initializeCollapse('priceCollapse', 'price_max');
            initializeCollapse('sizeCollapse', 'size');
            initializeCollapse('colorsCollapse', 'color');
            initializeCollapse('tagsCollapse', 'tag');

            // Ngăn sự kiện click từ item bên trong lan truyền lên tiêu đề
            document.querySelectorAll('.collapse .card-body').forEach(body => {
                body.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });

            // Theo dõi trạng thái collapse và chỉ cho phép mở/đóng khi nhấp vào tiêu đề
            document.querySelectorAll('.collapse').forEach(collapse => {
                const heading = collapse.closest('.card').querySelector('.card-heading');
                let isManualToggle = false;

                // Đánh dấu khi người dùng nhấp vào tiêu đề
                heading.addEventListener('click', function() {
                    isManualToggle = true;
                    const collapseInstance = bootstrap.Collapse.getInstance(collapse) ||
                        new bootstrap.Collapse(collapse, {
                            toggle: false
                        });
                    if (collapse.classList.contains('show')) {
                        collapseInstance.hide();
                    } else {
                        collapseInstance.show();
                    }
                });

                // Ngăn tự động mở nếu không phải nhấp vào tiêu đề
                collapse.addEventListener('show.bs.collapse', function(event) {
                    if (!isManualToggle) {
                        event.preventDefault();
                    }
                    isManualToggle = false;
                });

                // Ngăn tự động đóng nếu không phải nhấp vào tiêu đề
                collapse.addEventListener('hide.bs.collapse', function(event) {
                    if (!isManualToggle) {
                        event.preventDefault();
                    }
                    isManualToggle = false;
                });
            });

            // Xử lý click vào các liên kết bên trong collapse
            document.querySelectorAll(
                '.shop__sidebar__categories ul li a, .shop__sidebar__brand ul li a, .shop__sidebar__price ul li a'
            ).forEach(link => {
                link.addEventListener('click', function(event) {
                    event.stopPropagation(); // Ngăn lan truyền để không ảnh hưởng collapse
                });
            });
        });
    </script>
@endsection

@section('scripts')
    @if (!Auth::check())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll('.add-to-wishlist').forEach(button => {
                    button.addEventListener('click', function() {
                        const wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
                        const product = JSON.parse(this.dataset.product);
                        const productId = product.id;

                        fetch(`/wishlist/check/product/${productId}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error("Không tìm thấy sản phẩm hoặc lỗi máy chủ.");
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (!data.status || data.status !== 'active') {
                                    alert(
                                        "❌ Sản phẩm này hiện không còn kinh doanh và không thể thêm vào wishlist."
                                    );
                                    window.location.href = "{{ route('home') }}";
                                    return;
                                }

                                const product = {
                                    id: parseInt(productId),
                                    status: data.status
                                };

                                if (!wishlist.find(item => item.id === product.id)) {
                                    wishlist.push(product);
                                    localStorage.setItem("wishlist", JSON.stringify(wishlist));
                                    alert("✅ Đã thêm vào danh sách yêu thích!");
                                } else {
                                    alert("📌 Sản phẩm đã có trong wishlist.");
                                }

                                location.reload();
                            })
                            .catch(error => {
                                console.error("❌ Lỗi kiểm tra trạng thái sản phẩm:", error);
                                alert("⚠️ Không thể kiểm tra trạng thái sản phẩm lúc này.");
                            });
                    });
                });
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('wishlistModal'));
                modal.show();
                setTimeout(() => {
                    modal.hide();
                }, 3000);
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('wishlistErrorModal'));
                modal.show();
                setTimeout(() => {
                    modal.hide();
                }, 4000);
            });
        </script>
    @endif
@endsection