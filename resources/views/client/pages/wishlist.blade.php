@extends('client.pages.page-layout')
@section('content')
    <div class="container">
        <h4 class="mb-4"><i class="bi bi-heart-fill text-danger me-2"></i>Sản phẩm yêu thích của bạn</h4>

        {{-- nếu người dùng đã đăng nhập -> hiển thị wishlist từ db --}}
        @auth
            @if ($wishlistItems->isEmpty())
                <p>Bạn chưa có sản phẩm yêu thích nào.</p>
                <p class="text-muted mb-2">Hãy thêm sản phẩm vào danh sách yêu thích để dễ dàng theo dõi và mua sắm sau này.</p>
            @else
                <div class="table-responsive mb-4 shadow-sm">
                    <!-- Table yêu thích -->
                    <table class="table table-hover align-middle bg-white rounded text-center">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">Hình ảnh</th>
                                <th scope="col">Danh mục</th>
                                <th scope="col">Thương hiệu</th>
                                <th scope="col">Tên sản phẩm</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wishlistItems as $item)
                                <tr>
                                    <td style="width: 100px;">
                                        <img src="{{ Storage::url($item->product->thumbnail) }}" alt="Product"
                                            class="img-thumbnail" style="max-width: 50px;">
                                    </td>
                                    <td style="vertical-align: middle">{{ $item->product->category->name }}</td>
                                    <td style="vertical-align: middle">{{ $item->product->brand->name }}</td>
                                    <td style="vertical-align: middle">
                                        <strong class="text-primary">
                                            {{ $item->product->name }}
                                        </strong>
                                    </td>
                                    <td style="vertical-align: middle">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <form method="POST" action="{{ route('wishlist.destroy', $item->id) }}">
                                                {{-- Xoá sản phẩm khỏi wishlist --}}
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm me-2" type="submit"
                                                    onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này khỏi danh sách yêu thích?')">
                                                    <i class="bi bi-x-circle"></i>
                                                    Xoá
                                                </button>
                                            </form>

                                            <a class="btn btn-outline-primary btn-sm"
                                                href="{{ route('detail-product', $item->product->id) }}">
                                                <i class="bi bi-eye"></i>
                                                Chi tiết
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endauth

        <!-- Phân trang -->
        @if (!empty($wishlistItems) && $wishlistItems->hasPages())
            {{ $wishlistItems->links() }}
        @endif
        {{-- Kết thúc phần hiển thị wishlist cho người dùng đã đăng nhập --}}

        {{-- Nếu người dùng chưa đăng nhập -> hiển thị wishlist từ localStorage (nếu có) --}}
        {{-- Phần hiển thị được xử lý bằng js --}}
        @guest
            <div id="wishlist-container">
                <p>Đang tải danh sách yêu thích...</p>
            </div>
        @endguest

    </div>

    <!-- modal thông báo thành công -->
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

    <!-- modal báo lỗi -->
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
    {{-- Đồng bộ wishlist từ localStorage lên server --}}
    {{-- Chỉ chạy khi người dùng đã đăng nhập --}}
    @if (Auth::check())
        <script>
            // 🟢 Đồng bộ wishlist từ localStorage lên server khi người dùng đăng nhập
            document.addEventListener("DOMContentLoaded", function() {
                const userId = {{ Auth::id() }}; // Lấy ID người dùng hiện tại
                const wishlist = JSON.parse(localStorage.getItem("wishlist") || "[]");
                const syncedKey = `wishlist_synced_user_${userId}`; // khóa riêng theo user

                // Nếu có wishlist và chưa đồng bộ cho người dùng hiện tại, tiến hành đồng bộ
                if (wishlist.length > 0 && !localStorage.getItem(syncedKey)) {
                    const productIds = wishlist.map(item => item.id); // chỉ lấy id

                    // Gửi yêu cầu đồng bộ wishlist lên server
                    fetch("{{ route('wishlist.sync') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                wishlist: productIds
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // ✅ Đồng bộ thành công: xoá wishlist local + đánh dấu đã sync theo user
                                localStorage.removeItem("wishlist");
                                localStorage.setItem(syncedKey, "true");
                                console.log("✅ Đồng bộ thành công:", data.message);
                                location.reload(); // Tải lại để cập nhật danh sách từ server
                            } else {
                                console.warn("⚠️ Đồng bộ thất bại:", data.message);
                            }
                        })
                        .catch(error => {
                            console.error("❌ Lỗi kết nối:", error);
                        });
                }

                // 🧹 Nếu đang dùng session của người dùng khác, xoá dấu `wishlist_synced` cũ
                // Dọn dẹp `wishlist_synced_user_...` không trùng với user hiện tại
                Object.keys(localStorage).forEach(key => {
                    if (key.startsWith("wishlist_synced_user_") && key !== syncedKey) {
                        localStorage.removeItem(key);
                    }
                });
            });
        </script>
    @endif

    {{-- cho người dùng chưa đăng nhập --}}
    @guest
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const container = document.getElementById("wishlist-container");
                if (!container) {
                    console.error("❌ Không tìm thấy phần tử #wishlist-container trong DOM.");
                    return;
                }

                // Lấy danh sách ID sản phẩm từ localStorage (chỉ lấy id thôi)
                const wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
                const ids = wishlist.map(item => item.id);

                if (ids.length === 0) {
                    container.innerHTML = "<p>Chưa có sản phẩm nào trong danh sách yêu thích.</p>";
                    return;
                }

                // Gửi POST request lên server để lấy thông tin đầy đủ của sản phẩm
                fetch("{{ route('wishlist.guest') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                "content")
                        },
                        body: JSON.stringify({
                            ids
                        })
                    })
                    .then(response => response.json())
                    .then(products => {
                        // Kiểm tra xem có sản phẩm nào không
                        if (!products || products.length === 0) {
                            container.innerHTML = "<p>Không có sản phẩm hợp lệ trong danh sách yêu thích.</p>";
                            return;
                        }

                        // 🔴 Kiểm tra nếu có sản phẩm ngưng bán
                        const inactiveProducts = products.filter(p => p.status !== 'active');
                        if (inactiveProducts.length > 0) {
                            const warning = `
                    <div class="alert alert-warning">
                        <strong>⚠️ Lưu ý:</strong> Có ${inactiveProducts.length} sản phẩm trong danh sách yêu thích của bạn đã ngưng kinh doanh.
                        Vui lòng xoá chúng nếu không còn cần thiết.
                    </div>
                `;
                            container.insertAdjacentHTML('beforebegin', warning);
                        }

                        let html = `
                <div class="table-responsive mb-4 shadow-sm">
                    <table class="table table-hover align-middle bg-white rounded text-center">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">Hình ảnh</th>
                                <th scope="col">Danh mục</th>
                                <th scope="col">Thương hiệu</th>
                                <th scope="col">Tên sản phẩm</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>`;

                        products.forEach(item => {
                            const isInactive = item.status !== 'active';

                            html += `
                    <tr style="${isInactive ? 'opacity: 0.5; position: relative;' : ''}">
                        <td style="width: 100px;">
                            <img src="/storage/${item.thumbnail}" alt="Product" class="img-thumbnail" style="max-width: 50px;">
                        </td>
                        <td style="vertical-align: middle">${item.category}</td>
                        <td style="vertical-align: middle">${item.brand}</td>
                        <td style="vertical-align: middle">
                            <strong class="text-primary">${item.name}</strong>
                            ${isInactive ? '<div class="text-danger small mt-1">Sản phẩm đã ngưng kinh doanh</div>' : ''}
                        </td>
                        <td style="vertical-align: middle">
                            <div class="d-flex align-items-center justify-content-center position-relative">
                                <!-- Nút Xoá luôn hoạt động -->
                                <button class="btn btn-danger btn-sm me-2" onclick="removeFromWishlist(${item.id})" style="${isInactive ? 'z-index: 20;' : ''}">
                                    <i class="bi bi-x-circle"></i> Xoá
                                </button>

                                <!-- Nút Chi tiết bị ẩn nếu không active -->
                                <a class="btn btn-outline-primary btn-sm" style="${isInactive ? 'display: none;' : ''}"
                                   href="/detail-product/${item.id}" >
                                    <i class="bi bi-eye"></i> Chi tiết
                                </a>
                            </div>
                        </td>
                    </tr>`;
                        });

                        html += `
                        </tbody>
                    </table>
                </div>`;
                        container.innerHTML = html;
                    })
                    .catch(error => {
                        console.error("❌ Lỗi khi lấy dữ liệu wishlist:", error);
                        container.innerHTML = "<p class='text-danger'>Không thể tải danh sách yêu thích.</p>";
                    });
            });

            // Hàm xoá sản phẩm khỏi wishlist
            // Chỉ chạy khi người dùng chưa đăng nhập
            function removeFromWishlist(productId) {
                let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];

                // Lọc ra các sản phẩm khác với ID muốn xoá
                wishlist = wishlist.filter(item => item.id !== productId);

                if (confirm("Bạn có chắc muốn xoá sản phẩm này khỏi danh sách yêu thích?")) {
                    // Cập nhật lại localStorage
                    localStorage.setItem("wishlist", JSON.stringify(wishlist));
                    alert("✅ Sản phẩm đã được xoá khỏi danh sách yêu thích.");
                    location.reload(); // Tải lại trang để cập nhật danh sách
                }
            }
        </script>
    @endguest

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('wishlistModal'));
                modal.show();

                // Auto close sau 3 giây
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

                // Tự đóng sau 4 giây
                setTimeout(() => {
                    modal.hide();
                }, 4000);
            });
        </script>
    @endif
@endsection
