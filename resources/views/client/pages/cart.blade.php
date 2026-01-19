@extends('client.pages.page-layout')
@section('content')

    <section class="section py-4">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cart-fill me-2"></i>Giỏ hàng của bạn</h5>
                </div>

                <div class="table-responsive bg-white">
                    @if (!Auth::check())
                        <!-- Người dùng chưa đăng nhập -->
                        <div class="p-4 text-center">
                            <h5>Chưa có sản phẩm nào</h5>
                            <p>Vui lòng <a href="{{ route('login') }}" class="text-primary">đăng nhập</a> để xem và mua sắm
                                sản phẩm.</p>
                        </div>
                    @elseif($cartItems->isEmpty())
                        <!-- Người dùng đã đăng nhập nhưng giỏ hàng trống -->
                        <div class="p-4 text-center">
                            <h5>Chưa có sản phẩm nào trong giỏ hàng</h5>
                            <p><a href="{{ route('home') }}" class="text-primary">Mua sắm ngay</a> để thêm sản phẩm vào giỏ.
                            </p>
                        </div>
                    @else
                        <!-- Hiển thị danh sách sản phẩm trong giỏ -->
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th><input type="checkbox" id="check-all"></th>
                                    <th>Hình ảnh</th>
                                    <th>Sản phẩm</th>
                                    <th>Phân loại</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $item)
                                    <tr id="row-{{ $item->id }}"
                                        class="{{ $item->productVariant->status != 'active' ? 'inactive-item' : '' }}">
                                        <td>
                                            <input type="checkbox" class="item-checkbox" data-id="{{ $item->id }}"
                                                data-price="{{ $item->productVariant->price }}"
                                                data-quantity="{{ $item->quantity }}">
                                        </td>
                                        <td>
                                            <img src="{{ asset($item->productVariant->image ?? 'path/to/default.jpg') }}"
                                                alt="Ảnh" width="60">
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $item->productVariant->product->name }}</div>
                                        </td>
                                        <td>
                                            {{ $item->productVariant->size }} / {{ $item->productVariant->color }}
                                        </td>
                                        <td class="text-primary fw-bold">
                                            {{ number_format($item->productVariant->price, 0, ',', '.') }} đ
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-outline-secondary btn-sm btn-minus"
                                                    data-cartid="{{ $item->id }}"
                                                    @if ($item->productVariant->status != 'active') disabled @endif>
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="text" id="quantity-{{ $item->id }}"
                                                    value="{{ $item->quantity }}" readonly
                                                    class="form-control text-center mx-1 quantity-input"
                                                    style="@if ($item->productVariant->status != 'active') cursor: not-allowed; @endif">
                                                <button class="btn btn-outline-secondary btn-sm btn-plus"
                                                    data-cartid="{{ $item->id }}"
                                                    data-stock="{{ $item->productVariant->stock_quantity }}"
                                                    @if ($item->productVariant->status != 'active') disabled @endif>
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="fw-bold item-total" id="item-total-{{ $item->id }}">
                                            {{ number_format($item->productVariant->price * $item->quantity, 0, ',', '.') }} đ
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-remove"
                                                data-cartid="{{ $item->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div
                            class="bg-white shadow-sm p-3 mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-danger btn-remove-selected mb-2 mb-md-0">
                                    Xóa sản phẩm đã chọn
                                </button>
                            </div>
                            <div class="text-end">
                                <span class="me-3 fs-5">
                                    Tổng cộng (<span id="selected-count">0</span> sản phẩm):

                                    <strong id="total" class="text-danger fs-5">VND0.00</strong>

                                </span>
                                <a href="{{ route('checkout') }}" class="btn btn-warning text-white btn-checkout">Tiến hành
                                    thanh toán</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <style>
        .quantity-input {
            width: 50px;
            height: 30px;
        }

        table th,
        table td {
            vertical-align: middle !important;
        }

        .inactive-item {
            opacity: 0.5;
        }

        /* Ngăn layout bị dịch khi hiển thị SweetAlert */
        body.swal2-shown {
            padding-right: 0 !important;
        }
        
        .swal2-container {
            padding-right: 0 !important;
        }
        
        /* Đảm bảo scrollbar không bị thay đổi */
        html {
            scrollbar-gutter: stable;
        }
    </style>

    <!-- Phần JS xử lý sự kiện  -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        const cartItems = @json($cartItemsForJs);

        // Hàm kiểm tra tồn kho realtime (chỉ enable/disable nút, không hiển thị cảnh báo đỏ)
        function checkStockRealtime() {
            const cartIds = [];
            const quantities = [];
            $('.item-checkbox').each(function() {
                const id = $(this).data('id');
                cartIds.push(id);
                quantities.push(parseInt($('#quantity-' + id).val()));
            });

            $.post("{{ route('cart.checkStock') }}", {
                _token: "{{ csrf_token() }}",
                cart_ids: cartIds,
                quantities: quantities
            }, function(res) {
                if (res.ok) {
                    $('.btn-checkout').prop('disabled', false);
                } else {
                    $('.btn-checkout').prop('disabled', true);
                }
            });
        }

        $(document).ready(function() {
            let quantityUpdateTimeout;

            // Gọi kiểm tra tồn kho khi trang load
            checkStockRealtime();

            // Cho phép nhập số lượng khi double click
            $(document).on('dblclick', '.quantity-input', function() {
                if ($(this).prop('readonly')) {
                    $(this).prop('readonly', false).focus().select();
                }
            });
            // Khi blur hoặc nhấn Enter thì cập nhật số lượng và chuyển về readonly
            $(document).on('blur', '.quantity-input', function() {
                const id = $(this).attr('id').replace('quantity-', '');
                let qty = parseInt($(this).val());
                const plusBtn = $('.btn-plus[data-cartid="' + id + '"]');
                const stock = plusBtn.data('stock');
                if (isNaN(qty) || qty < 1) qty = 1;
                if (typeof stock !== 'undefined' && qty > stock) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Thông báo',
                        text: `Chỉ còn ${stock} sản phẩm trong kho.`,
                        timer: 1500,
                        scrollbarPadding: false
                    });
                    qty = stock;
                }
                $(this).val(qty).prop('readonly', true);
                updateQuantityDebounced(id, qty);
                checkStockRealtime();
            });
            $(document).on('keydown', '.quantity-input', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $(this).blur();
                }
            });

            // Giảm số lượng
            $(document).on('click', '.btn-minus', function() {
                const id = $(this).data('cartid');
                const product = cartItems.find(p => p.id === id);
                if (product.status !== 'active') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sản phẩm ngừng hoạt động',
                        text: 'Không thể thay đổi!',
                        timer: 1500
                    });
                    return;
                }
                const input = $('#quantity-' + id);
                let qty = parseInt(input.val()) - 1;
                if (qty >= 1) {
                    input.val(qty);
                    updateQuantityDebounced(id, qty);
                    checkStockRealtime();
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Thông báo',
                        text: 'Số lượng tối thiểu là 1.',
                        timer: 1500
                    });
                }
            });

            // Tăng số lượng
            $(document).on('click', '.btn-plus', function() {
                const id = $(this).data('cartid');
                const product = cartItems.find(p => p.id === id);
                if (product.status !== 'active') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sản phẩm ngừng hoạt động',
                        text: 'Không thể thay đổi!',
                        timer: 1500
                    });
                    return;
                }
                const input = $('#quantity-' + id);
                let qty = parseInt(input.val()) + 1;
                const stock = $(this).data('stock');
                if (qty <= stock) {
                    input.val(qty);
                    updateQuantityDebounced(id, qty);
                    checkStockRealtime();
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Thông báo',
                        text: `Chỉ còn ${stock} sản phẩm trong kho.`,
                        timer: 1500
                    });
                }
            });

            // Xóa 1 sản phẩm
            $(document).on('click', '.btn-remove', function(e) {
                e.preventDefault();
                const id = $(this).data('cartid');
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: 'Bạn có chắc muốn xóa sản phẩm này?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('{{ route('cart.remove') }}', {
                            _token: '{{ csrf_token() }}',
                            cart_id: id
                        }, res => {
                            if (res.success) {
                                $('#row-' + id).remove();
                                const index = cartItems.findIndex(item => item.id === id);
                                if (index !== -1) cartItems.splice(index, 1);
                                updateSummary();
                                renderInactiveWarning();
                                if ($('.item-checkbox').length === 0) location.reload();
                                $('#cart-count').text(res.newCartCount).toggle(res
                                    .newCartCount != 0);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã xóa!',
                                    timer: 1000
                                });
                            }
                        });
                    }
                });
            });

            // Xóa các sản phẩm đã chọn
            $(document).on('click', '.btn-remove-selected', function(e) {
                e.preventDefault();
                const selected = $('.item-checkbox:checked').map(function() {
                    return $(this).data('id');
                }).get();
                if (selected.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Chưa chọn sản phẩm!',
                        timer: 1500
                    });
                    return;
                }
                Swal.fire({
                    title: 'Xóa các sản phẩm đã chọn?',
                    text: 'Bạn có chắc muốn xóa tất cả?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa hết',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('{{ route('cart.removeSelected') }}', {
                            _token: '{{ csrf_token() }}',
                            cart_ids: selected
                        }, res => {
                            if (res.success) {
                                selected.forEach(id => {
                                    $('#row-' + id).remove();
                                    const index = cartItems.findIndex(item => item
                                        .id === id);
                                    if (index !== -1) cartItems.splice(index, 1);
                                });
                                updateSummary();
                                renderInactiveWarning();
                                if ($('.item-checkbox').length === 0) location.reload();
                                $('#cart-count').text(res.newCartCount).toggle(res
                                    .newCartCount != 0);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã xóa các sản phẩm đã chọn!',
                                    timer: 1200
                                });
                            }
                        });
                    }
                });
            });

            // Chọn/Bỏ chọn tất cả
            $(document).on('change', '#check-all', function() {
                $('.item-checkbox').prop('checked', $(this).prop('checked'));
                updateSummary();
            });

            // Chọn/Bỏ chọn từng sản phẩm
            $(document).on('change', '.item-checkbox', function() {
                $('#check-all').prop('checked', $('.item-checkbox:checked').length === $('.item-checkbox')
                    .length);
                updateSummary();
            });

            // Kiểm tra trước khi thanh toán
            $('.btn-checkout').click(function(e) {
                e.preventDefault();

                let selectedIds = [];
                let selectedQuantities = [];

                $('.item-checkbox:checked').each(function() {
                    const id = $(this).data('id');
                    selectedIds.push(id);
                    selectedQuantities.push(parseInt($('#quantity-' + id).val()));
                });

                if (selectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Không thể thanh toán',
                        text: 'Vui lòng chọn ít nhất 1 sản phẩm để thanh toán.',
                        timer: 2000,
                        scrollbarPadding: false
                    });
                    return;
                }

                // Gọi API kiểm tra tồn kho lần cuối chỉ cho các sản phẩm được chọn
                $.post("{{ route('cart.checkStock') }}", {
                    _token: "{{ csrf_token() }}",
                    cart_ids: selectedIds,
                    quantities: selectedQuantities
                }, function(res) {
                    if (res.ok) {
                        // Chuyển hướng sang checkout nếu đủ hàng
                        const url = new URL(window.location.origin + '/checkout');
                        url.searchParams.set('cart_item_ids', selectedIds.join(','));
                        window.location.href = url.toString();
                    } else {
                        // Hiển thị cảnh báo chỉ khi nhấn thanh toán
                        let msg = '<ul>';
                        res.out_of_stock.forEach(function(item) {
                            msg += '<li>' + item.reason + '</li>';
                        });
                        msg += '</ul>';
                        Swal.fire({
                            icon: 'error',
                            title: 'Không thể thanh toán',
                            html: msg,
                            scrollbarPadding: false
                        });
                    }
                });
            });

            // Hàm render cảnh báo sản phẩm ngưng bán
            function renderInactiveWarning() {
                $('.alert-warning').remove();
                const inactiveProducts = cartItems.filter(p => p.status !== 'active');
                if (inactiveProducts.length > 0) {
                    const warning = `
                    <div class="alert alert-warning mt-3">
                        ⚠️ <strong>Chú ý:</strong> Có ${inactiveProducts.length} sản phẩm đã ngưng bán.
                        Vui lòng xóa chúng khỏi giỏ hàng nếu không còn cần thiết.
                    </div>
                `;
                    $('.table-responsive').before(warning);
                }
            }

            // Cập nhật số lượng sản phẩm có debounce
            function updateQuantityDebounced(id, qty) {
                clearTimeout(quantityUpdateTimeout);
                quantityUpdateTimeout = setTimeout(() => {
                    $.post('{{ route('cart.update') }}', {
                        _token: '{{ csrf_token() }}',
                        cart_id: id,
                        quantity: qty
                                    }, res => {
                    $('#item-total-' + id).text(res.itemTotal.toLocaleString('vi-VN') + ' đ');
                    updateSummary();
                });
                }, 800);
            }

            // Cập nhật số lượng ngay lập tức
            function updateQuantity(id, qty) {
                $.post('{{ route('cart.update') }}', {
                    _token: '{{ csrf_token() }}',
                    cart_id: id,
                    quantity: qty
                }, res => {
                    $('#item-total-' + id).text(res.itemTotal.toLocaleString('vi-VN') + ' đ');
                    updateSummary();
                });
            }

            // Tính tổng cộng sản phẩm đã chọn
            function updateSummary() {
                let total = 0,
                    count = 0;
                $('.item-checkbox:checked').each(function() {
                    const id = $(this).data('id');
                    const price = parseFloat($(this).data('price'));
                    const qty = parseInt($('#quantity-' + id).val());
                    const product = cartItems.find(p => p.id === id);
                    if (product && product.status === 'active') {
                        total += price * qty;
                        count++;
                    } else {
                        $(this).prop('checked', false);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sản phẩm ngừng hoạt động',
                            text: 'Sản phẩm này đã ngừng bán, không thể thanh toán!',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                });
                $('#total').text(total.toLocaleString('vi-VN') + ' đ');
                $('#selected-count').text(count);
            }

            // Gọi lại khi trang load
            updateSummary();
            renderInactiveWarning();
        });
    </script>
@endsection
