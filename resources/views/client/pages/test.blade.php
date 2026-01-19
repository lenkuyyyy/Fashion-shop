@extends('client.pages.page-layout')

@section('content')
    <form method="POST" action="{{ route('checkout.submit') }}" id="checkout-form">
        @csrf
        <input type="hidden" name="cart_item_ids" value="{{ implode(',', $cartItems->pluck('id')->toArray()) }}">
        <input type="hidden" name="shipping_address_id" id="shipping-address-id">
        <div class="container">
            <div class="row justify-content-center align-items-start">
                <div class="col-lg-7 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="mb-3">
                                <a href="{{ route('cart.index') }}" class="text-decoration-none text-primary">
                                    <i class="bi bi-cart-fill me-2"></i>Quay lại giỏ hàng
                                </a>
                            </h5>

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
                                        <option value="{{ $address->id }}" data-name="{{ $address->name }}"
                                            data-phone="{{ $address->phone_number }}"
                                            data-address="{{ $address->address }}" data-ward="{{ $address->ward }}"
                                            data-district="{{ $address->district }}" data-city="{{ $address->city }}"
                                            data-full-address="{{ $address->full_address }}">
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
                                        placeholder="Họ và tên" value="{{ old('name') }}">
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
                                        <select name="province_id" id="province_id" class="form-control" required>
                                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <select name="district_id" id="district_id" class="form-control" required>
                                            <option value="">-- Chọn Quận/Huyện --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <select name="ward_code" id="ward_code" class="form-control" required>
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
                                <strong id="shipping_fee">{{ number_format($shippingFee, 0, ',', '.') }} ₫</strong>
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
                                        <label class="form-check-label" for="cod">Thanh toán khi nhận hàng</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="paymentMethod" value="card"
                                            id="card">
                                        <label class="form-check-label" for="card">Thanh toán online</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <p>Tổng giá trị hàng: <strong id="subtotal-display">{{ number_format($subtotal, 0, ',', '.') }} ₫</strong></p>
                                <p>Giảm giá: <strong id="order-discount-amount" data-value="0">0 ₫</strong></p>
                                <p>Phí vận chuyển: <strong id="shipping_fee_display">{{ number_format($shippingFee, 0, ',', '.') }} ₫</strong></p>
                                <p><strong>Tổng cộng: <span id="total-amount">{{ number_format($subtotal - 0, 0, ',', '.') }} ₫</span></strong></p>
                            </div>

                            <div class="d-grid">
                                <button id="place-order-btn" class="btn btn-primary">Đặt hàng</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>

    <div class="modal fade" id="coupon-modal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalLabel">Chọn Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="coupon-list-container">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status"></div>
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
            // [GIỮ NGUYÊN] - Khai báo biến cho các chức năng có sẵn
            const radios = document.querySelectorAll('input[name="paymentMethod"]');
            const submitBtn = document.getElementById('submit-btn');
            const details = {
                cod: document.getElementById("cod-details"),
                card: document.getElementById("card-details")
            };
            const form = document.getElementById('checkout-form');
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
            // Sửa: tham chiếu tới các select thực tế trong form
            const wardInput = document.getElementById('ward_code');
            const districtInput = document.getElementById('district_id');
            const cityInput = document.getElementById('province_id');

            // [TÍCH HỢP] - Khai báo biến cho logic mã giảm giá mới
            let appliedCoupons = {
                order: null,
                shipping: null
            };
            const subtotal = {{ $subtotal }};
            const shippingFee = {{ $shippingFee }};
            const cartItemIds = "{{ implode(',', $cartItems->pluck('id')->toArray()) }}";

            const couponModalEl = document.getElementById('coupon-modal');
            const couponModal = new bootstrap.Modal(couponModalEl);
            const couponListContainer = document.getElementById('coupon-list-container');
            const appliedCouponsList = document.getElementById('applied-coupons-list');
            const totalAmountEl = document.getElementById('total-amount');
            const orderDiscountRow = document.getElementById('order-discount-row') || { style: { display: 'none' } };
            const orderDiscountAmountEl = document.getElementById('order-discount-amount') || { textContent: '0' };
            const shippingDiscountRow = document.getElementById('shipping-discount-row') || { style: { display: 'none' } };
            const shippingDiscountAmountEl = document.getElementById('shipping-discount-amount') || { textContent: '0' };
            const submitOrderBtn = document.getElementById('place-order-btn');

            let currentShippingFee = shippingFee || 0;
            let isRecalculating = false;

            function updatePaymentButton() {
                const selected = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'cod';
                const totalText = totalAmountEl.textContent.trim();
                if (selected === 'cod') {
                    submitBtn.innerHTML = `<i class="bi bi-cart-check me-2"></i>Đặt hàng`;
                } else {
                    submitBtn.innerHTML = `<i class="bi bi-cart-check me-2"></i>Thanh toán (${totalText})`;
                }
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

            // Xử lý chọn địa chỉ có sẵn / thêm địa chỉ mới
            if (select) {
                select.addEventListener('change', function() {
                    const selected = select.options[select.selectedIndex];
                    const useNewAddress = !selected.value;

                    detailBox.classList.toggle('d-none', useNewAddress);
                    manualAddressInput.classList.toggle('d-none', !useNewAddress);

                    if (!useNewAddress) {
                        nameSpan.textContent = selected.dataset.name || '';
                        phoneSpan.textContent = selected.dataset.phone || '';
                        addressSpan.textContent = selected.dataset.fullAddress || '';
                        nameInput.value = selected.dataset.name || '';
                        phoneInput.value = selected.dataset.phone || '';
                        addressInput.value = selected.dataset.address || '';
                        // Gán value vào các select tương ứng (nếu có)
                        if (wardInput) wardInput.value = selected.dataset.ward || '';
                        if (districtInput) districtInput.value = selected.dataset.district || '';
                        if (cityInput) cityInput.value = selected.dataset.city || '';
                        if (shippingAddressIdInput) shippingAddressIdInput.value = selected.value;
                    } else {
                        nameInput.value = '';
                        phoneInput.value = '';
                        addressInput.value = '';
                        if (wardInput) wardInput.value = '';
                        if (districtInput) districtInput.value = '';
                        if (cityInput) cityInput.value = '';
                        if (shippingAddressIdInput) shippingAddressIdInput.value = '';
                    }
                });
                // Kích hoạt change lần đầu
                select.dispatchEvent(new Event('change'));
            }

            // Hàm tải danh sách voucher
            async function fetchAvailableCoupons() {
                couponListContainer.innerHTML =
                    `<div class="text-center p-3"><div class="spinner-border text-primary"></div></div>`;
                try {
                    const response = await fetch(`{{ route('checkout.getAvailableCoupons') }}?cart_item_ids=${cartItemIds}`);
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        couponListContainer.innerHTML =
                            `<p class="text-danger text-center">Không thể tải danh sách voucher. (Lỗi server)</p>`;
                        return;
                    }
                    const coupons = await response.json();
                    couponListContainer.innerHTML = '';
                    coupons.forEach(c => {
                        const el = document.createElement('div');
                        el.className = 'coupon-item p-2 border mb-2 d-flex justify-content-between align-items-center';
                        el.innerHTML = `<div><strong>${c.code}</strong><div class="small text-muted">${c.description || ''}</div></div><div><button class="btn btn-sm btn-primary apply-coupon" data-code="${c.code}">Áp dụng</button></div>`;
                        couponListContainer.appendChild(el);
                    });

                    document.querySelectorAll('.apply-coupon').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const code = e.target.dataset.code;
                            recalculateTotals([code]);
                        });
                    });
                } catch (err) {
                    couponListContainer.innerHTML = `<div class="text-danger p-3">Lỗi: ${err.message}</div>`;
                }
            }

            // Áp dụng / tính lại coupon (gọi backend)
            async function recalculateTotals(codes) {
                couponModal.hide();
                Swal.fire({
                    title: 'Đang cập nhật...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                try {
                    isRecalculating = true;
                    const response = await fetch('{{ route('checkout.applyCoupons') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            coupon_codes: codes,
                            cart_item_ids: cartItemIds,
                            shipping_fee: currentShippingFee // Truyền phí ship thực tế
                        })
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Lỗi khi áp dụng mã.');

                    // xử lý applied_coupons
                    appliedCoupons = { order: null, shipping: null };
                    data.applied_coupons.forEach(c => {
                        appliedCoupons[c.type] = c;
                    });

                    // Cập nhật UI
                    appliedCouponsList.innerHTML = '';
                    Object.values(appliedCoupons).forEach(coupon => {
                        if (coupon) {
                            const appliedEl = document.createElement('div');
                            appliedEl.className = 'alert alert-success py-2 px-3 mt-2 d-flex justify-content-between align-items-center';
                            appliedEl.innerHTML = `<span><i class="bi bi-check-circle-fill me-2"></i><strong>${coupon.code}</strong></span><button type="button" class="btn-close remove-coupon-btn" data-type="${coupon.type}"></button>`;
                            appliedCouponsList.appendChild(appliedEl);
                        }
                    });

                    appliedCouponsList.querySelectorAll('.remove-coupon-btn').forEach(btn => {
                        btn.addEventListener('click', (e) => handleRemoveCouponFromTag(e.target.dataset.type));
                    });

                    orderDiscountRow.style.display = (data.order_discount > 0) ? 'flex' : 'none';
                    orderDiscountAmountEl.textContent = `- ${data.order_discount.toLocaleString('vi-VN')} ₫`;

                    shippingDiscountRow.style.display = (data.shipping_discount > 0) ? 'flex' : 'none';
                    shippingDiscountAmountEl.textContent = `- ${data.shipping_discount.toLocaleString('vi-VN')} ₫`;

                    // Tổng do backend trả về (đã gồm phí ship)
                    totalAmountEl.textContent = `${data.total.toLocaleString('vi-VN')} ₫`;
                    Swal.close();
                } catch (error) {
                    Swal.fire('Lỗi', error.message, 'error');
                } finally {
                    isRecalculating = false;
                }
            }

            function handleRemoveCouponFromTag(type) {
                if (!type) return;
                const remaining = [];
                if (appliedCoupons.order && type !== 'order') remaining.push(appliedCoupons.order.code);
                if (appliedCoupons.shipping && type !== 'shipping') remaining.push(appliedCoupons.shipping.code);
                recalculateTotals(remaining);
            }

            // Khai báo và load tỉnh/quận/xã từ GHN
            const token = "{{ env('GHN_API_TOKEN') }}";
            const ghn_url = "{{ env('GHN_API_URL') }}";
            // Load tỉnh
            $.ajax({
                url: ghn_url + '/master-data/province',
                headers: { 'Token': token },
                success: function(res) {
                    res.data.forEach(function(p) {
                        $('#province_id').append(`<option value="${p.ProvinceID}">${p.ProvinceName}</option>`);
                    });
                }
            });

            // Khi chọn tỉnh -> load district
            $('#province_id').on('change', function() {
                $('#district_id').html('<option value="">-- Chọn Quận/Huyện --</option>');
                $('#ward_code').html('<option value="">-- Chọn Xã/Phường --</option>');
                $.ajax({
                    url: ghn_url + '/master-data/district',
                    headers: { 'Token': token },
                    method: 'GET',
                    data: { province_id: $(this).val() },
                    success: function(res) {
                        res.data.forEach(function(d) {
                            $('#district_id').append(`<option value="${d.DistrictID}">${d.DistrictName}</option>`);
                        });
                    }
                });
            });

            // Khi chọn district -> load ward
            $('#district_id').on('change', function() {
                $('#ward_code').html('<option value="">-- Chọn Xã/Phường --</option>');
                $.ajax({
                    url: ghn_url + '/master-data/ward',
                    headers: { 'Token': token },
                    method: 'GET',
                    data: { district_id: $(this).val() },
                    success: function(res) {
                        res.data.forEach(function(w) {
                            $('#ward_code').append(`<option value="${w.WardCode}">${w.WardName}</option>`);
                        });
                    }
                });
            });

            // Khi chọn phường/xã → gọi Laravel tính phí ship
            $('#ward_code').on('change', function() {
                $.post("{{ route('checkout.getShippingFee') }}", {
                    province_id: $('#province_id').val(),
                    district_id: $('#district_id').val(),
                    ward_code: $('#ward_code').val(),
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    if (res.success) {
                        $('#shipping_fee_display').text(res.fee.toLocaleString('vi-VN') + ' ₫');
                        currentShippingFee = res.fee;
                        if (appliedCoupons.shipping) {
                            // nếu có coupon áp dụng cho ship thì gọi backend để tính lại
                            const codes = [];
                            if (appliedCoupons.order) codes.push(appliedCoupons.order.code);
                            if (appliedCoupons.shipping) codes.push(appliedCoupons.shipping.code);
                            recalculateTotals(codes);
                        } else {
                            updateTotalAmount();
                        }
                    } else {
                        $('#shipping_fee_display').text('Không thể tính phí');
                    }
                });
            });

            // Gọi load coupon khi mở modal (nếu cần)
            couponModalEl.addEventListener('shown.bs.modal', fetchAvailableCoupons);

            // Submit đặt hàng
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (!document.getElementById('agree').checked) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Bạn chưa đồng ý điều khoản',
                            text: 'Vui lòng đồng ý trước khi đặt hàng'
                        });
                        return;
                    }

                    const fd = new FormData(form);
                    fd.append('shipping_fee', currentShippingFee);
                    // append applied coupon codes if any
                    const codes = [];
                    if (appliedCoupons.order) codes.push(appliedCoupons.order.code);
                    if (appliedCoupons.shipping) codes.push(appliedCoupons.shipping.code);
                    fd.append('coupon_codes', JSON.stringify(codes));

                    // gửi form
                    fetch('{{ route('checkout.submit') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: fd
                    }).then(r => r.json()).then(res => {
                        if (res.success) {
                            Swal.fire('Thành công', res.message, 'success').then(() => {
                                if (res.order_id) {
                                    window.location.href = '{{ route('orders.index') }}?id=' + res.order_id;
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire('Lỗi', res.message || 'Lỗi khi tạo đơn', 'error');
                        }
                    }).catch(err => {
                        Swal.fire('Lỗi', err.message, 'error');
                    });
                });
            }

            // Init
            updateTotalAmount();
            updatePaymentButton();
        });
    </script>
@endsection
