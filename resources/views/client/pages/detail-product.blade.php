@extends('client.pages.page-layout')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Product images (variant strip) -->
            <div class="col-md-6">
                <div class="mb-4">
                    <div class="main-image-container position-relative">
                        @php
                            $mainImage =
                                $selectedVariant->image ??
                                ($product->thumbnail ?? 'assets/images/single-product-01.jpg');
                        @endphp
                        <img src="{{ asset($mainImage) }}" class="img-fluid rounded main-product-image shadow-sm"
                            alt="Ảnh sản phẩm chính" style="max-height: 400px; object-fit: cover;">
                    </div>
                </div>

                <!-- Variant images strip -->
                <div class="variant-images-strip d-flex overflow-x-auto pb-3"
                    style="scroll-behavior: smooth; user-select: none;">
                    @php
                        $variantImages = [];
                        // Lấy danh sách các màu duy nhất và ảnh đại diện cho mỗi màu
                        foreach (
                            $product->variants
                                ->where('status', 'active')
                                ->where('stock_quantity', '>', 0)
                                ->groupBy('color')
                            as $color => $variants
                        ) {
                            // Lấy biến thể đầu tiên của màu đó
                            $variant = $variants->first();
                            $variantImages[$color] = [
                                'image' => $variant->image
                                    ? asset($variant->image)
                                    : asset('assets/images/single-product-01.jpg'),
                                'variant_id' => $variant->id,
                            ];
                        }
                    @endphp
                    @foreach ($variantImages as $color => $imageData)
                        <div class="position-relative me-2">
                            <img src="{{ $imageData['image'] }}"
                                class="variant-image rounded shadow-sm {{ $selectedVariant->color === $color ? 'border border-success' : '' }}"
                                data-variant-id="{{ $imageData['variant_id'] }}" data-color="{{ $color }}"
                                style="width: 100px; height: 100px; object-fit: cover; cursor: pointer; transition: transform 0.2s;"
                                alt="Ảnh biến thể {{ $color }}" onmouseover="this.style.transform='scale(1.1)'"
                                onmouseout="this.style.transform='scale(1)'">
                            <span class="badge bg-success position-absolute top-0 start-0"
                                style="font-size: 10px;">{{ $color }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Scroll buttons -->
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-outline-secondary btn-sm scroll-left" type="button">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm scroll-right" type="button">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Product details -->
            <div class="col-md-6">
                <h2>{{ $product->name }}</h2>
                <p class="text-muted">Mã sản phẩm: {{ $product->sku }}</p>
                <h4 class="text-danger fw-bold" id="variant-price">
                    {{ number_format($selectedVariant->price) }} đ
                </h4>

                <p class="mt-3">
                    {{ $product->description ?? ($product->short_description ?? 'Không có mô tả.') }}
                </p>

                <!-- Color selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Màu sắc:</label><br>
                    @foreach ($product->variants->groupBy('color') as $color => $variants)
                        @php
                            $isAvailable = $variants->contains(function ($variant) {
                                return $variant->status === 'active' && $variant->stock_quantity > 0;
                            });
                        @endphp
                        <button
                            class="btn btn-outline-{{ $selectedVariant->color === $color && $isAvailable ? 'success' : 'secondary' }} btn-sm me-2 variant-btn {{ !$isAvailable ? 'disabled' : '' }}"
                            data-color="{{ $color }}" data-price="{{ $variants->first()->price }}"
                            data-stock="{{ $variants->first()->stock_quantity }}"
                            {{ !$isAvailable ? 'disabled title="Không khả dụng hoặc hết hàng"' : '' }}>
                            {{ $color }}
                        </button>
                    @endforeach
                </div>

                <!-- Size selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kích thước:</label>
                    <select class="form-select w-auto" id="size-select">
                        @foreach ($product->variants->where('color', $selectedVariant->color) as $variant)
                            <option value="{{ $variant->id }}" data-price="{{ $variant->price }}"
                                data-stock="{{ $variant->stock_quantity }}"
                                {{ $selectedVariant->id === $variant->id ? 'selected' : '' }}
                                {{ $variant->status !== 'active' || $variant->stock_quantity <= 0 ? 'disabled' : '' }}>
                                {{ $variant->size }}
                                {{ $variant->status !== 'active' || $variant->stock_quantity <= 0 ? '' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Số lượng:</label>
                    <input type="number" class="form-control w-auto" value="1" min="1"
                        max="{{ $selectedVariant->stock_quantity }}" id="quantity-input">
                    <small class="text-muted" id="stock-text">Còn {{ $selectedVariant->stock_quantity }} sản phẩm</small>
                </div>

                <!-- Action buttons -->
                <div class="d-grid gap-2 d-md-block">
                    @if (Auth::check())
                        <button type="button" class="btn btn-outline-success btn-lg me-2 btn-add-cart" data-qty="1"
                            data-image="{{ asset($selectedVariant->image ?? $product->thumbnail) }}">
                            <i class="bi bi-cart-plus me-1"></i>Thêm vào giỏ
                        </button>
                    @else
                        <div class="alert alert-warning mt-3" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i> Bạn cần <a href="{{ route('login') }}">đăng
                                nhập</a> để mua sắm.
                        </div>
                    @endif

                    @if (Auth::check())
                        <form action="{{ route('wishlist.store') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-outline-danger btn-lg">
                                <i class="bi bi-heart-fill me-1"></i> Yêu thích
                            </button>
                        </form>
                    @else
                        <button class="btn btn-outline-danger btn-lg add-to-wishlist"
                            data-product='@json($productData)'>
                            <i class="bi bi-heart-fill me-1"></i>Yêu thích
                        </button>
                    @endif
                </div>

                <!-- Policies -->
                <div class="mt-4 border-top pt-3">
                    <p><i class="bi bi-truck me-2"></i> Giao hàng tiêu chuẩn 2-4 ngày</p>
                    <p><i class="bi bi-arrow-repeat me-2"></i> Đổi trả trong 30 ngày</p>
                </div>
            </div>
        </div>

        <!-- Reviews section -->
        <div class="row mt-5">
            <div class="col-lg-12">
                <h4><i class="bi bi-chat-left-text me-2"></i>Đánh giá khách hàng</h4>
                @if ($reviews->isEmpty())
                    <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                @else
                    <div class="reviews-container border rounded p-3 mt-2" style="max-height: 300px; overflow-y: auto;">
                        @foreach ($reviews as $review)
                            <div class="border rounded p-3 mb-3 bg-light">
                                <strong>{{ $review->user->name }}</strong>
                                <span class="text-muted small">- {{ $review->created_at->format('d/m/Y') }}</span>
                                <div>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-warning"></i>
                                    @endfor
                                </div>
                                <p class="mb-0">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- <!-- Review form -->
            <div class="col-lg-6">
                <h5><i class="bi bi-pencil-square me-2"></i>Viết đánh giá</h5>
                @auth
                    <form action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="mb-3">
                            <label class="form-label">Điểm đánh giá</label>
                            <select class="form-select w-auto" name="rating" required>
                                <option value="">Chọn số sao</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} sao</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nội dung</label>
                            <textarea class="form-control" rows="4" name="comment" placeholder="Nhận xét sản phẩm..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark"><i class="bi bi-send me-1"></i>Gửi bình luận</button>
                    </form>
                @else
                    <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để viết đánh giá.</p>
                @endauth
            </div> --}}
        </div>

        <!-- Related products -->
        <div class="mt-5">
            <h4 class="mb-4">
                <i class="bi bi-stars text-warning me-2"></i>Sản phẩm liên quan
            </h4>

            @if ($relatedProducts->isEmpty())
                <p>Không có sản phẩm liên quan.</p>
            @else
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        @php
                            // Ưu tiên dùng thumbnail sản phẩm, nếu không có thì lấy ảnh variant đầu tiên, nếu vẫn không có thì dùng ảnh mặc định
                            if (!empty($relatedProduct->thumbnail)) {
                                $relatedThumbnail = 'storage/' . $relatedProduct->thumbnail;
                            } elseif ($relatedProduct->variants->first()?->image) {
                                $relatedThumbnail = 'storage/' . $relatedProduct->variants->first()->image;
                            } else {
                                $relatedThumbnail = 'assets/images/single-product-01.jpg'; // fallback ảnh mặc định
                            }
                        @endphp

                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="{{ asset($relatedThumbnail) }}" class="card-img-top"
                                    alt="{{ $relatedProduct->name }}" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                                    <p class="text-primary fw-semibold">
                                        {{ $relatedProduct->getPriceRangeAttribute() }}
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <a href="{{ route('detail-product', $relatedProduct->id) }}"
                                        class="btn btn-outline-primary w-100 btn-sm">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Related categories -->
        <div class="mt-5">
            <h4 class="mb-3"><i class="bi bi-tags me-2"></i>Danh mục liên quan</h4>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('products-client') }}?category={{ $product->category->slug }}"
                    class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-tag"></i> {{ $product->category->name }}
                </a>
                <a href="{{ route('products-client') }}?brand={{ $product->brand->slug }}"
                    class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-tag"></i> {{ $product->brand->name }}
                </a>
            </div>
        </div>
    </div>

    <!-- Success modal -->
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

    <!-- Error modal -->
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
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const variantButtons = document.querySelectorAll('.variant-btn');
            const sizeSelect = document.getElementById('size-select');
            const priceElement = document.getElementById('variant-price');
            const quantityInput = document.getElementById('quantity-input');
            const stockText = document.getElementById('stock-text');
            const mainImage = document.querySelector('.main-product-image');
            const variantImages = document.querySelectorAll('.variant-image');
            const variantImagesStrip = document.querySelector('.variant-images-strip');
            const scrollLeftBtn = document.querySelector('.scroll-left');
            const scrollRightBtn = document.querySelector('.scroll-right');

            // Update variant details (price, stock, and quantity input)
            function updateVariantDetails(variantId, price, stock) {
                priceElement.textContent =
                    `${Math.floor(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')} đ`;
                quantityInput.max = stock;
                quantityInput.value = Math.min(quantityInput.value, stock || 1);
                stockText.textContent = `Còn ${stock} sản phẩm`;
            }

            // Handle color button click
            variantButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const scrollY = window.scrollY;

                    if (button.disabled) return;

                    const color = button.dataset.color;
                    const price = button.dataset.price;
                    const stock = button.dataset.stock;

                    variantButtons.forEach(btn => btn.classList.remove('btn-outline-success'));
                    variantButtons.forEach(btn => btn.classList.add('btn-outline-secondary'));
                    button.classList.remove('btn-outline-secondary');
                    button.classList.add('btn-outline-success');

                    fetch(
                            `/detail-product/{{ $product->id }}/variants?color=${encodeURIComponent(color)}`
                        )
                        .then(response => response.json())
                        .then(data => {
                            sizeSelect.innerHTML = '';
                            data.variants.forEach(variant => {
                                const option = document.createElement('option');
                                option.value = variant.id;
                                option.textContent = variant.status === 'active' &&
                                    variant.stock_quantity > 0 ?
                                    variant.size :
                                    `${variant.size} `;
                                option.dataset.price = variant.price;
                                option.dataset.stock = variant.stock_quantity;
                                if (variant.status !== 'active' || variant
                                    .stock_quantity <= 0) {
                                    option.disabled = true;
                                }
                                sizeSelect.appendChild(option);
                            });

                            const firstAvailableVariant = data.variants.find(v => v.status ===
                                'active' && v.stock_quantity > 0);
                            if (firstAvailableVariant) {
                                updateVariantDetails(firstAvailableVariant.id,
                                    firstAvailableVariant.price, firstAvailableVariant
                                    .stock_quantity);
                                sizeSelect.value = firstAvailableVariant.id;
                            } else {
                                updateVariantDetails(data.variants[0].id, data.variants[0]
                                    .price, 0);
                            }

                            const firstImage = document.querySelector(
                                `.variant-image[data-color="${color}"]`);
                            if (firstImage) {
                                mainImage.src = firstImage.src;
                                variantImages.forEach(img => img.classList.remove('border',
                                    'border-primary'));
                                firstImage.classList.add('border', 'border-primary');
                                firstImage.scrollIntoView({
                                    behavior: 'smooth',
                                    inline: 'start'
                                });
                            }

                            window.scrollTo(0, scrollY);
                        });
                });
            });

            // Handle size selection
            sizeSelect.addEventListener('change', () => {
                const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
                if (!selectedOption.disabled) {
                    const variantId = selectedOption.value;
                    const price = selectedOption.dataset.price;
                    const stock = selectedOption.dataset.stock;
                    updateVariantDetails(variantId, price, stock);
                }
            });

            // Handle variant image click
            variantImages.forEach(image => {
                image.addEventListener('click', () => {
                    mainImage.src = image.src;
                    variantImages.forEach(img => img.classList.remove('border', 'border-primary'));
                    image.classList.add('border', 'border-primary');
                });
            });

            // Handle scroll buttons
            scrollLeftBtn.addEventListener('click', () => {
                variantImagesStrip.scrollBy({
                    left: -150,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', () => {
                variantImagesStrip.scrollBy({
                    left: 150,
                    behavior: 'smooth'
                });
            });

            // Drag to scroll
            let isDown = false;
            let startX;
            let scrollLeft;

            variantImagesStrip.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - variantImagesStrip.offsetLeft;
                scrollLeft = variantImagesStrip.scrollLeft;
                variantImagesStrip.style.cursor = 'grabbing';
            });

            variantImagesStrip.addEventListener('mouseleave', () => {
                isDown = false;
                variantImagesStrip.style.cursor = 'grab';
            });

            variantImagesStrip.addEventListener('mouseup', () => {
                isDown = false;
                variantImagesStrip.style.cursor = 'grab';
            });

            variantImagesStrip.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - variantImagesStrip.offsetLeft;
                const walk = (x - startX) * 2;
                variantImagesStrip.scrollLeft = scrollLeft - walk;
            });

            // Touch support for mobile
            variantImagesStrip.addEventListener('touchstart', (e) => {
                isDown = true;
                startX = e.touches[0].pageX - variantImagesStrip.offsetLeft;
                scrollLeft = variantImagesStrip.scrollLeft;
            });

            variantImagesStrip.addEventListener('touchend', () => {
                isDown = false;
            });

            variantImagesStrip.addEventListener('touchmove', (e) => {
                if (!isDown) return;
                const x = e.touches[0].pageX - variantImagesStrip.offsetLeft;
                const walk = (x - startX) * 2;
                variantImagesStrip.scrollLeft = scrollLeft - walk;
            });
        });
    </script>

    @if (!Auth::check())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll('.add-to-wishlist').forEach(button => {
                    button.addEventListener('click', function() {
                        const wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
                        const productId = window.location.pathname.split("/").pop();

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
                }, 3000);
            });
        </script>
    @endif
@endsection
