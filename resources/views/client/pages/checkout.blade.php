@extends('client.pages.page-layout')

@section('content')
    <form method="POST" action="{{ route('checkout.submit') }}" id="checkout-form">
        @csrf
        <input type="hidden" name="cart_item_ids" value="{{ implode(',', $cartItems->pluck('id')->toArray()) }}">
        <input type="hidden" name="shipping_address_id" id="shipping-address-id">
        <div class="container">
            <div class="row justify-content-center align-items-start">
                {{-- Cột hiển thị sản phẩm trong giỏ hàng --}}
                <div class="col-lg-7 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="mb-3">
                                <a href="{{ route('cart.index') }}" class="text-decoration-none text-primary">
                                    <i class="bi bi-cart-fill me-2"></i>Quay lại giỏ hàng
                                </a>
                            </h5>
                            <hr>
                            <p class="text-muted mb-3">
                                Bạn đã chọn <strong class="text-danger">{{ count($cartItems) }} sản phẩm</strong>
                            </p>
                            @foreach ($cartItems as $item)
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset($item->productVariant->image ?? 'images/default-product.jpg') }}"
                                                alt="Product" class="img-thumbnail" style="width: 60px;">
                                            <div class="ms-3">
                                                <h6 class="mb-1">{{ $item->productVariant->product->name }}</h6>
                                                <small class="text-muted">
                                                    Size: {{ $item->productVariant->size }},
                                                    Màu: {{ $item->productVariant->color }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-4">
                                            <span class="fw-semibold">x{{ $item->quantity }}</span>
                                            <span class="text-primary fw-bold">
                                                {{ number_format($item->productVariant->price * $item->quantity, 0, ',', '.') }}
                                                ₫
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Cột thanh toán và thông tin giao hàng --}}
                <div class="col-lg-5">
                    <div class="card bg-light border-0 shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="bi bi-credit-card-fill me-2 text-primary"></i>Chi tiết thanh
                                    toán</h5>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Chọn phương thức thanh toán:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="paymentMethod" value="cod"
                                            id="cod" checked>
                                        <label class="form-check-label" for="cod"><i
                                                class="bi bi-cash-stack me-1"></i>COD</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="paymentMethod" value="card"
                                            id="card">
                                        <label class="form-check-label" for="card"><i
                                                class="bi bi-credit-card-2-front me-1"></i>VNpay</label>
                                    </div>
                                </div>
                            </div>
                            <div id="cod-details" class="payment-method-details">
                                <div class="alert alert-success py-2 mb-3"><i class="bi bi-truck me-2"></i> Thanh toán khi
                                    nhận hàng (COD)</div>
                            </div>
                            <div id="card-details" class="payment-method-details" style="display: none;">
                                <div class="alert alert-info py-2 mb-3"><i class="bi bi-credit-card-2-front me-2"></i> Thanh
                                    toán bằng VNpay</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Voucher giảm giá</label>
                                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                                    data-bs-target="#coupon-modal">
                                    <i class="bi bi-ticket-percent-fill me-2"></i> Chọn Voucher
                                </button>
                                <div id="applied-coupons-list" class="mt-2"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Chọn địa chỉ giao hàng:</label>
                                <select class="form-select" id="address-select">
                                    <option value="">-- Thêm địa chỉ mới --</option>
                                    @foreach ($user->shippingAddresses ?? [] as $address)
                                        <option value="{{ $address->id }}"
                                            data-name="{{ $address->name }}"
                                            data-phone="{{ $address->phone_number }}"
                                            data-address="{{ $address->address }}"
                                            data-ward="{{ $address->ward }}"
                                            data-district="{{ $address->district }}"
                                            data-city="{{ $address->city }}"
                                            data-full-address="{{ $address->full_address }}"
                                            data-province-id="{{ $address->province_id ?? '' }}"
                                            data-district-id="{{ $address->district_id ?? '' }}"
                                            data-ward-code="{{ $address->ward_code ?? '' }}"
                                        >
                                            {{ $address->full_address }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="address-details" class="border p-3 rounded bg-light d-none">
                                <p class="mb-1"><strong>Người nhận:</strong> <span id="detail-name"></span></p>
                                <p class="mb-1"><strong>Số điện thoại:</strong> <span id="detail-phone"></span></p>
                                <p class="mb-0"><strong>Địa chỉ:</strong> <span id="detail-address"></span></p>
                            </div>
                            <div class="mb-4 mt-4" id="manual-address-input">
                                <label class="form-label fw-semibold">Thông tin giao hàng</label>
                                <div class="mb-2">
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Họ và tên người nhận" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2">
                                    <input type="text" name="phone_number"
                                        class="form-control @error('phone_number') is-invalid @enderror"
                                        placeholder="Số điện thoại" value="{{ old('phone_number') }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2">
                                    <input type="text" name="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="Địa chỉ cụ thể (Số nhà, tên đường...)" value="{{ old('address') }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                       <select name="province_id" id="province_id" class="form-control">
                                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                       <select name="district_id" id="district_id" class="form-control">
                                            <option value="">-- Chọn Quận/Huyện --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                       <select name="ward_code" id="ward_code" class="form-control">
                                            <option value="">-- Chọn Xã/Phường --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Tạm tính:</span>
                                <strong>{{ number_format($subtotal, 0, ',', '.') }} ₫</strong>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span>Phí vận chuyển:</span>
                                <input type="hidden" name="shipping_fee" id="shipping_fee_input">
                                <strong id="shipping_fee_display">{{ number_format($shippingFee, 0, ',', '.') }} ₫</strong>
                            </div>
                            <div id="order-discount-row" class="d-flex justify-content-between text-success"
                                style="display: none;">
                                <span>Giảm giá đơn hàng:</span>
                                <strong id="order-discount-amount"></strong>
                            </div>
                            <div id="shipping-discount-row" class="d-flex justify-content-between text-success"
                                style="display: none;">
                                <span>Giảm giá vận chuyển:</span>
                                <strong id="shipping-discount-amount"></strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fs-5 mt-2">
                                <span>Tổng cộng:</span>
                                <strong class="text-danger">
                                    <span id="total-amount">{{ number_format($subtotal - 0, 0, ',', '.') }} ₫</span>
                                </strong>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="agree" checked>
                                <label class="form-check-label" for="agree">
                                    Tôi đồng ý với <a href="#" class="text-decoration-underline">chính sách mua
                                        hàng</a>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-success w-100 mt-3" id="submit-btn">
                                <i class="bi bi-cart-check me-2"></i>Thanh toán
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal hiển thị danh sách Voucher --}}
    <div class="modal fade" id="coupon-modal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalLabel">Chọn Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="coupon-list-container">
                        <div class="text-center" id="coupon-loader">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                        <h6 class="text-primary fw-bold">Mã giảm giá đơn hàng</h6>
                        <div id="order-coupons-list" class="mb-4">
                        </div>
                        <hr>
                        <h6 class="text-primary fw-bold">Mã giảm giá vận chuyển</h6>
                        <div id="shipping-coupons-list">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Khai báo biến cho các chức năng
            const radios = document.querySelectorAll('input[name="paymentMethod"]');
            const submitBtn = document.getElementById('submit-btn');
            const details = {
                cod: document.getElementById("cod-details"),
                card: document.getElementById("card-details")
            };
            const form = document.getElementById('checkout-form');
            let appliedCoupons = { order: null, shipping: null };
            const subtotal = {{ $subtotal }};
            const shippingFee = {{ $shippingFee }};
            const cartItemIds = "{{ implode(',', $cartItems->pluck('id')->toArray()) }}";
            const couponModal = new bootstrap.Modal(document.getElementById('coupon-modal'));
            const couponListContainer = document.getElementById('coupon-list-container');
            const couponLoader = document.getElementById('coupon-loader');
            const orderCouponsList = document.getElementById('order-coupons-list');
            const shippingCouponsList = document.getElementById('shipping-coupons-list');
            const appliedCouponsList = document.getElementById('applied-coupons-list');
            const totalAmountEl = document.getElementById('total-amount');
            const orderDiscountRow = document.getElementById('order-discount-row');
            const orderDiscountAmountEl = document.getElementById('order-discount-amount');
            const shippingDiscountRow = document.getElementById('shipping-discount-row');
            const shippingDiscountAmountEl = document.getElementById('shipping-discount-amount');
            let currentShippingFee = shippingFee;
            let isRecalculating = false;
            let isShippingFeeLoading = false;

            // Cập nhật nút thanh toán
            function updatePaymentButton() {
                const selected = document.querySelector('input[name="paymentMethod"]:checked').value;
                const total = totalAmountEl.textContent.trim();
                if (selected === 'cod') {
                    submitBtn.innerHTML = `Đặt hàng`;
                } else {
                    submitBtn.innerHTML = `Thanh toán (${total})`;
                }
                Object.keys(details).forEach(key => {
                    if (details[key]) details[key].style.display = (key === selected) ? "block" : "none";
                });
            }
            radios.forEach(radio => radio.addEventListener("change", updatePaymentButton));
            updatePaymentButton();

            // Hàm lấy ID và tính phí ship
            function fetchIdsAndCalculateFee(cityName, districtName, wardName) {
                if (!cityName || !districtName || !wardName) {
                    console.error('Thiếu thông tin tỉnh, quận hoặc phường:', { cityName, districtName, wardName });
                    return;
                }
                
                console.log('Bắt đầu lấy ID tỉnh/quận/phường:', { cityName, districtName, wardName });

                $.ajax({
                    url: ghn_url + '/master-data/province',
                    headers: { 'Token': token },
                    success: function(res) {
                        console.log('Dữ liệu tỉnh:', res.data);
                        const p = res.data.find(p => p.ProvinceName.trim() === cityName.trim());
                        if (p) {
                            console.log('Tìm thấy tỉnh:', p);
                            $.ajax({
                                url: ghn_url + '/master-data/district',
                                headers: { 'Token': token },
                                data: { province_id: p.ProvinceID },
                                success: function(res) {
                                    console.log('Dữ liệu quận:', res.data);
                                    const d = res.data.find(d => d.DistrictName.trim() === districtName.trim());
                                    if (d) {
                                        console.log('Tìm thấy quận:', d);
                                        $.ajax({
                                            url: ghn_url + '/master-data/ward',
                                            headers: { 'Token': token },
                                            data: { district_id: d.DistrictID },
                                            success: function(res) {
                                                console.log('Dữ liệu phường:', res.data);
                                                const w = res.data.find(w => w.WardName.trim() === wardName.trim());
                                                if (w) {
                                                    console.log('Tìm thấy phường:', w);
                                                    $.post("{{ route('checkout.getShippingFee') }}", {
                                                        district_id: d.DistrictID,
                                                        ward_code: w.WardCode,
                                                        _token: '{{ csrf_token() }}'
                                                    }, function(res) {
                                                        console.log('Kết quả tính phí ship:', res);
                                                        if (res.success) {
                                                            $('#shipping_fee_display').text(res.fee.toLocaleString('vi-VN') + ' ₫');
                                                            $('#shipping_fee_input').val(res.fee);
                                                            currentShippingFee = res.fee;
                                                            const codes = [];
                                                            if (appliedCoupons.order) codes.push(appliedCoupons.order.code);
                                                            if (appliedCoupons.shipping) codes.push(appliedCoupons.shipping.code);
                                                            if (codes.length > 0) recalculateTotals(codes);
                                                            else updateTotalAmount();
                                                        } else {
                                                            console.error('Lỗi tính phí ship:', res.message);
                                                            $('#shipping_fee_display').text('Lỗi tính phí');
                                                        }
                                                    }).fail(function(jqXHR, textStatus, errorThrown) {
                                                        console.error('Lỗi gọi API tính phí ship:', textStatus, errorThrown);
                                                    });
                                                } else {
                                                    console.error('Không tìm thấy phường:', wardName);
                                                }
                                            },
                                            error: function(jqXHR, textStatus, errorThrown) {
                                                console.error('Lỗi tải dữ liệu phường:', textStatus, errorThrown);
                                            }
                                        });
                                    } else {
                                        console.error('Không tìm thấy quận:', districtName);
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.error('Lỗi tải dữ liệu quận:', textStatus, errorThrown);
                                }
                            });
                        } else {
                            console.error('Không tìm thấy tỉnh:', cityName);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Lỗi tải dữ liệu tỉnh:', textStatus, errorThrown);
                    }
                });
            }

            // Xử lý địa chỉ giao hàng
            const select = document.getElementById('address-select');
            const detailBox = document.getElementById('address-details');
            const manualAddressInput = document.getElementById('manual-address-input');
            const shippingAddressIdInput = document.getElementById('shipping-address-id');
            const nameSpan = document.getElementById('detail-name');
            const phoneSpan = document.getElementById('detail-phone');
            const addressSpan = document.getElementById('detail-address');
            const nameInput = document.querySelector('input[name="name"]');
            const phoneInput = document.querySelector('input[name="phone_number"]');
            const addressInput = document.querySelector('input[name="address"]');
            const wardInput = document.getElementById('ward_code');
            const districtInput = document.getElementById('district_id');
            const cityInput = document.getElementById('province_id');

            if (select) {
                select.addEventListener('change', async function() {
                    const selected = select.options[select.selectedIndex];
                    const useNewAddress = !selected.value;

                    console.log('Đã chọn địa chỉ:', {
                        value: selected.value,
                        name: selected.dataset.name,
                        phone: selected.dataset.phone,
                        fullAddress: selected.dataset.fullAddress,
                        provinceId: selected.dataset.provinceId,
                        districtId: selected.dataset.districtId,
                        wardCode: selected.dataset.wardCode
                    });

                    detailBox.classList.toggle('d-none', useNewAddress);
                    manualAddressInput.classList.toggle('d-none', !useNewAddress);

                    if (!useNewAddress) {
                        nameSpan.textContent = selected.dataset.name || '';
                        phoneSpan.textContent = selected.dataset.phone || '';
                        addressSpan.textContent = selected.dataset.fullAddress || '';
                        nameInput.value = selected.dataset.name || '';
                        phoneInput.value = selected.dataset.phone || '';
                        addressInput.value = selected.dataset.address || '';
                        shippingAddressIdInput.value = selected.value;

                        // Cập nhật các ô select
                        if (cityInput && selected.dataset.provinceId) {
                            cityInput.value = selected.dataset.provinceId;
                            console.log('Đặt province_id:', cityInput.value);
                            $(cityInput).trigger('change'); // Kích hoạt sự kiện change

                            // Đợi quận được tải
                            setTimeout(function() {
                                if (districtInput && selected.dataset.districtId) {
                                    districtInput.value = selected.dataset.districtId;
                                    console.log('Đặt district_id:', districtInput.value);
                                    $(districtInput).trigger('change'); // Kích hoạt sự kiện change

                                    // Đợi phường được tải
                                    setTimeout(function() {
                                        if (wardInput && selected.dataset.wardCode) {
                                            wardInput.value = selected.dataset.wardCode;
                                            console.log('Đặt ward_code:', wardInput.value);
                                            $(wardInput).trigger('change'); // Kích hoạt sự kiện change
                                        }
                                    }, 500);
                                }
                            }, 500);
                        }

                        // Tính phí ship dựa trên địa chỉ
                        fetchIdsAndCalculateFee(selected.dataset.city, selected.dataset.district, selected.dataset.ward);
                    } else {
                        console.log('Chọn nhập địa chỉ mới');
                        nameInput.value = '';
                        phoneInput.value = '';
                        addressInput.value = '';
                        if (wardInput) wardInput.value = '';
                        if (districtInput) districtInput.value = '';
                        if (cityInput) cityInput.value = '';
                        if (shippingAddressIdInput) shippingAddressIdInput.value = '';
                    }

                    // Thêm đoạn này:
                    const requiredFields = [nameInput, phoneInput, addressInput, cityInput, districtInput, wardInput];
                    if (!useNewAddress) {
                        // Địa chỉ có sẵn: bỏ required
                        requiredFields.forEach(input => input && input.removeAttribute('required'));
                    } else {
                        // Địa chỉ mới: thêm required
                        requiredFields.forEach(input => input && input.setAttribute('required', true));
                    }
                });
                select.dispatchEvent(new Event('change'));
            }

            // Load tỉnh/quận/xã từ GHN
            const token = "{{ env('GHN_API_TOKEN') }}";
            const ghn_url = "{{ env('GHN_API_URL') }}";
            $.ajax({
                url: ghn_url + '/master-data/province',
                headers: { 'Token': token },
                success: function(res) {
                    console.log('Tải danh sách tỉnh thành công:', res.data);
                    $('#province_id').html('<option value="">-- Chọn Tỉnh/Thành phố --</option>');
                    res.data.forEach(function(p) {
                        $('#province_id').append(`<option value="${p.ProvinceID}">${p.ProvinceName}</option>`);
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Lỗi tải danh sách tỉnh:', textStatus, errorThrown);
                    $('#province_id').html('<option value="">Lỗi tải tỉnh</option>');
                }
            });

            $('#province_id').on('change', function() {
                console.log('Đã chọn tỉnh:', $(this).val());
                $('#district_id').html('<option value="">-- Chọn Quận/Huyện --</option>');
                $('#ward_code').html('<option value="">-- Chọn Xã/Phường --</option>');
                if ($(this).val()) {
                    $.ajax({
                        url: ghn_url + '/master-data/district',
                        headers: { 'Token': token },
                        method: 'GET',
                        data: { province_id: $(this).val() },
                        success: function(res) {
                            console.log('Tải danh sách quận thành công:', res.data);
                            res.data.forEach(function(d) {
                                $('#district_id').append(`<option value="${d.DistrictID}">${d.DistrictName}</option>`);
                            });
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Lỗi tải danh sách quận:', textStatus, errorThrown);
                            $('#district_id').html('<option value="">Lỗi tải quận</option>');
                        }
                    });
                }
            });

            $('#district_id').on('change', function() {
                console.log('Đã chọn quận:', $(this).val());
                $('#ward_code').html('<option value="">-- Chọn Xã/Phường --</option>');
                if ($(this).val()) {
                    $.ajax({
                        url: ghn_url + '/master-data/ward',
                        headers: { 'Token': token },
                        method: 'GET',
                        data: { district_id: $(this).val() },
                        success: function(res) {
                            console.log('Tải danh sách phường thành công:', res.data);
                            res.data.forEach(function(w) {
                                $('#ward_code').append(`<option value="${w.WardCode}">${w.WardName}</option>`);
                            });
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Lỗi tải danh sách phường:', textStatus, errorThrown);
                            $('#ward_code').html('<option value="">Lỗi tải phường</option>');
                        }
                    });
                }
            });

            $('#ward_code').on('change', function() {
                console.log('Đã chọn phường:', $(this).val());
                $.post("{{ route('checkout.getShippingFee') }}", {
                    province_id: $('#province_id').val(),
                    district_id: $('#district_id').val(),
                    ward_code: $(this).val(),
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    console.log('Kết quả tính phí ship:', res);
                    if (res.success) {
                        $('#shipping_fee_display').text(res.fee.toLocaleString('vi-VN') + ' ₫');
                        currentShippingFee = res.fee;
                        if (appliedCoupons.shipping) {
                            recalculateTotals([
                                appliedCoupons.order ? appliedCoupons.order.code : null,
                                appliedCoupons.shipping.code
                            ].filter(Boolean));
                        } else {
                            updateTotalAmount();
                        }
                    } else {
                        console.error('Lỗi tính phí ship:', res.message);
                        $('#shipping_fee_display').text('Không thể tính phí');
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Lỗi gọi API tính phí ship:', textStatus, errorThrown);
                });
            });

            // Logic cho mã giảm giá
            document.querySelector('[data-bs-target="#coupon-modal"]').addEventListener('click', fetchAvailableCoupons);

            async function fetchAvailableCoupons() {
                couponLoader.style.display = 'block';
                orderCouponsList.innerHTML = '';
                shippingCouponsList.innerHTML = '';
                try {
                    const response = await fetch(`{{ route('checkout.getAvailableCoupons') }}?cart_item_ids=${cartItemIds}`);
                    const coupons = await response.json();
                    renderCouponsInModal(coupons);
                } catch (error) {
                    orderCouponsList.innerHTML = `<p class="text-danger text-center">Không thể tải danh sách voucher.</p>`;
                } finally {
                    couponLoader.style.display = 'none';
                }
            }

            function renderCouponsInModal(coupons) {
                const orderCoupons = coupons.filter(c => c.type === 'order');
                const shippingCoupons = coupons.filter(c => c.type === 'shipping');

                if (orderCoupons.length > 0) {
                    orderCoupons.forEach(coupon => {
                        orderCouponsList.appendChild(createCouponElement(coupon));
                    });
                } else {
                    orderCouponsList.innerHTML = `<p class="text-muted text-center">Không có voucher nào phù hợp.</p>`;
                }

                if (shippingCoupons.length > 0) {
                    shippingCoupons.forEach(coupon => {
                        shippingCouponsList.appendChild(createCouponElement(coupon));
                    });
                } else {
                    shippingCouponsList.innerHTML = `<p class="text-muted text-center">Không có voucher nào phù hợp.</p>`;
                }

                couponListContainer.querySelectorAll('.apply-coupon-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => handleApplyOrRemoveCoupon(e.target.dataset.code));
                });
            }

            function createCouponElement(coupon) {
                const isApplied = appliedCoupons[coupon.type]?.code === coupon.code;
                const isDisabled = !isApplied && appliedCoupons[coupon.type] !== null;
                const couponEl = document.createElement('div');
                couponEl.className = 'card mb-2';
                couponEl.innerHTML = `
                    <div class="card-body d-flex justify-content-between align-items-center p-2">
                        <div>
                            <h6 class="card-title mb-0 text-success">${coupon.code}</h6>
                            <small class="text-muted">${coupon.description}</small>
                        </div>
                        <button class="btn btn-sm btn-primary apply-coupon-btn" data-code="${coupon.code}" ${isDisabled ? 'disabled' : ''}>
                            ${isApplied ? 'Bỏ chọn' : 'Áp dụng'}
                        </button>
                    </div>`;
                return couponEl;
            }

            function handleApplyOrRemoveCoupon(code) {
                let codesToApply = new Set();
                if (appliedCoupons.order) codesToApply.add(appliedCoupons.order.code);
                if (appliedCoupons.shipping) codesToApply.add(appliedCoupons.shipping.code);

                if (codesToApply.has(code)) {
                    codesToApply.delete(code);
                } else {
                    codesToApply.add(code);
                }
                recalculateTotals(Array.from(codesToApply));
            }

            function handleRemoveCouponFromTag(type) {
                let codesToApply = new Set();
                if (appliedCoupons.order) codesToApply.add(appliedCoupons.order.code);
                if (appliedCoupons.shipping) codesToApply.add(appliedCoupons.shipping.code);

                if (appliedCoupons[type]) {
                    codesToApply.delete(appliedCoupons[type].code);
                }
                recalculateTotals(Array.from(codesToApply));
            }

            // ===================================================================
            // === BẮT ĐẦU PHẦN ĐÃ SỬA LỖI TOKEN ===
            // ===================================================================
            async function recalculateTotals(codes) {
                couponModal.hide();
                Swal.fire({ title: 'Đang cập nhật...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });

                try {
                    isRecalculating = true;
                    const response = await fetch('{{ route('checkout.applyCoupons') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            coupon_codes: codes,
                            cart_item_ids: cartItemIds,
                            shipping_fee: currentShippingFee,
                            _token: '{{ csrf_token() }}' // Gửi token trong body để đảm bảo xác thực
                        })
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Lỗi khi áp dụng mã.');

                    appliedCoupons = { order: null, shipping: null };
                    data.applied_coupons.forEach(c => { appliedCoupons[c.type] = c; });
                    
                    updateUI(data.order_discount, data.shipping_discount, data.total);
                    Swal.close();
                } catch (error) {
                    Swal.fire('Lỗi', error.message, 'error');
                } finally {
                    isRecalculating = false;
                }
            }
            // ===================================================================
            // === KẾT THÚC PHẦN ĐÃ SỬA LỖI TOKEN ===
            // ===================================================================

            function updateUI(orderDiscount, shippingDiscount, total) {
                appliedCouponsList.innerHTML = '';
                Object.values(appliedCoupons).forEach(coupon => {
                    if (coupon) {
                        const couponTypeText = coupon.type === 'order' ? 'Giảm giá đơn hàng' : 'Giảm giá vận chuyển';
                        
                        const appliedEl = document.createElement('div');
                        appliedEl.className = 'alert alert-success py-2 px-3 mt-2 d-flex justify-content-between align-items-center';
                        appliedEl.innerHTML = `
                            <span>
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>${coupon.code}</strong>
                                <small class="text-muted fst-italic ms-2">(${couponTypeText})</small>
                            </span>
                            <button type="button" class="btn-close remove-coupon-btn" data-type="${coupon.type}"></button>`;
                        appliedCouponsList.appendChild(appliedEl);
                    }
                });

                appliedCouponsList.querySelectorAll('.remove-coupon-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => handleRemoveCouponFromTag(e.target.dataset.type));
                });

                orderDiscountRow.style.display = (orderDiscount > 0) ? 'flex' : 'none';
                orderDiscountAmountEl.textContent = `- ${orderDiscount.toLocaleString('vi-VN')} ₫`;

                shippingDiscountRow.style.display = (shippingDiscount > 0) ? 'flex' : 'none';
                shippingDiscountAmountEl.textContent = `- ${shippingDiscount.toLocaleString('vi-VN')} ₫`;

                totalAmountEl.textContent = `${total.toLocaleString('vi-VN')} ₫`;
                updatePaymentButton();
            }

            function updateTotalAmount() {
                if (isRecalculating) return;
                let orderDiscount = parseInt(orderDiscountAmountEl.textContent.replace(/\D/g, '')) || 0;
                let shippingDiscount = parseInt(shippingDiscountAmountEl.textContent.replace(/\D/g, '')) || 0;
                let total = subtotal - orderDiscount + (currentShippingFee - shippingDiscount);
                if (total < 0) total = 0;
                totalAmountEl.textContent = `${total.toLocaleString('vi-VN')} ₫`;
                updatePaymentButton();
            }

            // Hàm bật/tắt loading phí ship
            function setShippingFeeLoading(loading) {
                isShippingFeeLoading = loading;
                submitBtn.disabled = loading;
                if (loading) {
                    Swal.fire({
                        title: 'Đang tính phí vận chuyển...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                } else {
                    Swal.close();
                }
            }

            if (form) {
                form.addEventListener('submit', function(e) {
                    if (isShippingFeeLoading) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'info',
                            title: 'Vui lòng chờ',
                            text: 'Đang cập nhật phí vận chuyển, vui lòng đợi...',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        return;
                    }
                    e.preventDefault();
                    if (!document.getElementById('agree').checked) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Bạn chưa đồng ý điều khoản',
                            text: 'Vui lòng đồng ý trước khi đặt hàng'
                        });
                        return;
                    }

                    submitBtn.disabled = true;
                    const formData = new FormData(form);
                    formData.append('shipping_fee', currentShippingFee);
                    submitOrder(formData).finally(() => {
                        submitBtn.disabled = false;
                    });
                });
            }

            async function submitOrder(formData) {
                try {
                    Swal.fire({
                        title: 'Đang xử lý...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const response = await fetch('{{ route('checkout.submit') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Lỗi khi gửi đơn hàng');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (data.vnpay_url) {
                            window.location.href = data.vnpay_url;
                        } else if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (data.order_id) {
                            window.location.href = '{{ route('orders.index') }}?id=' + data.order_id;
                        } else {
                            window.location.reload();
                        }
                    });
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: err.message || 'Đã xảy ra lỗi, vui lòng thử lại',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }

            updateTotalAmount();
            updatePaymentButton();
        });
    </script>
@endsection