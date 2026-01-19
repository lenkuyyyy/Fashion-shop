<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
   
    const SHIPPING_FEE = 0;
     public function store(Request $request)
    {
        // Lấy dữ liệu từ request
        $name = $request->input('name');
        $phone = $request->input('phone');
        $provinceId = $request->input('province_id');
        $districtId = $request->input('district_id');
        $wardCode = $request->input('ward_code');
        $address = $request->input('address');
        // Lấy phí ship từ request hoặc dùng hằng số làm mặc định
        $shippingFee = $request->input('shipping_fee');
        // Tính tổng tiền đơn hàng (bao gồm phí ship)
        // Lấy tổng giỏ hàng (đảm bảo là số)
        $subTotal = 0;
        if (class_exists(\Cart::class)) {
            // Nếu dùng package Cart
            $subTotal = (float) str_replace(',', '', \Cart::subtotal());
        }
        $total = $subTotal + (float) $shippingFee;
        // Tạo đơn hàng mới
        $order = new Order();
        $order->name = $name;
        $order->phone = $phone;
        $order->province_id = $provinceId;
        $order->district_id = $districtId;
        $order->ward_code = $wardCode;
        $order->address = $address;
        $order->shipping_fee = $request->shipping_fee;
        $order->total = $total;
        $order->save();
        // Trả về JSON kết quả
        return response()->json([
            'success' => true,
            'redirect' => route('home')
        ]);
    }
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thanh toán.');
        }

        $rawIds = explode(',', $request->query('cart_item_ids', ''));
        $validIds = array_filter($rawIds, function ($id) {
            return is_numeric($id) && intval($id) > 0;
        });

        if (empty($validIds)) {
            return redirect()->route('cart.index')->with('warning', 'Danh sách sản phẩm không hợp lệ.');
        }

        $cartItems = Cart::with(['productVariant.product'])
            ->where('user_id', Auth::id())
            ->whereIn('id', $validIds)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('warning', 'Không tìm thấy sản phẩm nào phù hợp.');
        }

        if (count($validIds) !== $cartItems->count()) {
            session()->flash('warning', 'Một số sản phẩm bạn chọn đã ngừng bán hoặc không còn tồn tại.');
        }

        session()->forget('applied_coupons');

        $subtotal = $cartItems->sum(function ($item) {
            return $item->productVariant->price * $item->quantity;
        });

        $shippingAddresses = ShippingAddress::selectRaw('MIN(id) as id, name, phone_number, address, ward, district, city, province_id, district_id, ward_code')
            ->where('user_id', Auth::id())
            ->groupBy('name', 'phone_number', 'address', 'ward', 'district', 'city', 'province_id', 'district_id', 'ward_code')
            ->get()
            ->map(function ($address) {
                $address->full_address = implode(', ', array_filter([
                    $address->address,
                    $address->ward,
                    $address->district,
                    $address->city
                ]));
                return $address;
            });

        return view('client.pages.checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingFee' => 0, // Ban đầu là 0
            'user' => (object) ['shippingAddresses' => $shippingAddresses],
        ]);
    }

    public function getShippingFee(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $token = env('GHN_API_TOKEN');
        $shopId = env('GHN_SHOP_ID');

        try {
            $response = $client->post(env('GHN_API_URL') . '/v2/shipping-order/fee', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Token' => $token,
                    'ShopId' => $shopId,
                ],
                'json' => [
                    "service_type_id" => 2,
                    "from_district_id" => 1450,
                    "from_ward_code" => "21010",
                    "to_district_id" => (int) $request->district_id,
                    "to_ward_code" => $request->ward_code,
                    "height" => 15,
                    "length" => 15,
                    "weight" => 1000,
                    "width" => 15,
                    "insurance_value" => 0,
                    "coupon" => null
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'fee' => $data['data']['total'] ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAvailableCoupons(Request $request)
    {
        $cartItemIds = array_filter(explode(',', $request->query('cart_item_ids')), 'is_numeric');
        if (empty($cartItemIds)) {
            return response()->json(['error' => 'Giỏ hàng không hợp lệ'], 400);
        }

        $cartItems = Cart::whereIn('id', $cartItemIds)->where('user_id', Auth::id())->get();
        $subtotal = $cartItems->sum(fn ($item) => $item->productVariant->price * $item->quantity);

        $coupons = Coupon::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->where(function ($query) use ($subtotal) {
                $query->whereNull('min_order_value')
                    ->orWhere('min_order_value', '<=', $subtotal);
            })
            ->get()
            ->filter(function ($coupon) {
                $userUsage = Order::where('user_id', Auth::id())->whereJsonContains('extra_info->coupon_codes', $coupon->code)->count();
                return $coupon->user_usage_limit === null || $userUsage < $coupon->user_usage_limit;
            })
            ->map(function ($coupon) {
                $type = in_array($coupon->discount_type, ['percent', 'fixed']) ? 'order' : 'shipping';
                return [
                    'code' => $coupon->code,
                    'type' => $type,
                    'description' => $this->getCouponDescription($coupon)
                ];
            })
            ->values(); // Thêm dòng này

        return response()->json($coupons);
    }

    public function applyCoupons(Request $request)
    {
        $request->validate([
            'coupon_codes' => 'nullable|array',
            'cart_item_ids' => 'required|string',
            'shipping_fee' => 'nullable|numeric'
        ]);
        $couponCodes = $request->input('coupon_codes', []);
        $cartItemIds = array_filter(explode(',', $request->input('cart_item_ids')), 'is_numeric');
        // Đảm bảo phí ship là số nguyên dương
        $shippingFee = max(0, (int) $request->input('shipping_fee', self::SHIPPING_FEE));

        $cartItems = Cart::whereIn('id', $cartItemIds)->where('user_id', Auth::id())->get();
        $subtotal = $cartItems->sum(fn ($item) => $item->productVariant->price * $item->quantity);

        $appliedCouponsData = [];
        $orderDiscount = 0;
        $shippingDiscount = 0;
        $appliedOrderCode = null;
        $appliedShippingCode = null;

        if (!empty($couponCodes)) {
            $validCoupons = Coupon::whereIn('code', $couponCodes)->where('status', 'active')->where('end_date', '>=', now())->get()->keyBy('code');

            foreach ($couponCodes as $code) {
                $coupon = $validCoupons[$code] ?? null;
                if (!$coupon) continue;

                $couponType = in_array($coupon->discount_type, ['percent', 'fixed']) ? 'order' : 'shipping';

                if ($couponType === 'order' && !$appliedOrderCode) {
                    $orderDiscount = $this->calculateDiscount($coupon, $subtotal, $cartItems);
                    $appliedOrderCode = $coupon->code;
                    $appliedCouponsData[] = [
                        'code' => $coupon->code,
                        'description' => $this->getCouponDescription($coupon),
                        'type' => $couponType
                    ];
                } elseif ($couponType === 'shipping' && !$appliedShippingCode) {
                    // Sửa lại: giảm đúng số tiền phí ship thực tế khi là free_shipping
                    if ($coupon->discount_type === 'free_shipping') {
                        $shippingDiscount = $shippingFee;
                    } else {
                        $shippingDiscount = min($coupon->discount_value, $shippingFee);
                    }
                    $appliedShippingCode = $coupon->code;
                    $appliedCouponsData[] = [
                        'code' => $coupon->code,
                        'description' => $this->getCouponDescription($coupon),
                        'type' => $couponType
                    ];
                }
            }
        }

        $finalTotal = $subtotal - $orderDiscount + ($shippingFee - $shippingDiscount);
        if ($finalTotal < 0) $finalTotal = 0;

        session(['applied_coupons' => [
            'order_code' => $appliedOrderCode,
            'shipping_code' => $appliedShippingCode,
            'order_discount' => $orderDiscount,
            'shipping_discount' => $shippingDiscount,
            'shipping_fee' => $shippingFee, // Lưu lại phí ship thực tế
        ]]);

        return response()->json([
            'success' => true,
            'applied_coupons' => $appliedCouponsData,
            'order_discount' => $orderDiscount,
            'shipping_discount' => $shippingDiscount,
            'total' => $finalTotal,
        ]);
    }

    protected function calculateDiscount($coupon, $subtotal, $cartItems)
    {
        $discount = 0;
        $applicableCategories = $coupon->applicable_categories ? json_decode($coupon->applicable_categories, true) : [];
        $applicableProducts = $coupon->applicable_products ? json_decode($coupon->applicable_products, true) : [];
        $applicableSubtotal = 0;

        if (empty($applicableCategories) && empty($applicableProducts)) {
            $applicableSubtotal = $subtotal;
        } else {
            foreach ($cartItems as $item) {
                $product = $item->productVariant->product;
                if (
                    (empty($applicableCategories) || in_array($product->category_id, $applicableCategories)) ||
                    (empty($applicableProducts) || in_array($product->id, $applicableProducts))
                ) {
                    $applicableSubtotal += $item->productVariant->price * $item->quantity;
                }
            }
        }

        if ($coupon->discount_type === 'percent') {
            $discount = ($applicableSubtotal * $coupon->discount_value) / 100;
            if ($coupon->max_discount !== null && $discount > $coupon->max_discount) {
                $discount = $coupon->max_discount;
            }
        } else { // 'fixed'
            $discount = min($coupon->discount_value, $applicableSubtotal);
        }

        return $discount;
    }

    private function getCouponDescription(Coupon $coupon): string
    {
        switch ($coupon->discount_type) {
            case 'percent':
                $desc = "Giảm {$coupon->discount_value}% cho đơn hàng";
                if ($coupon->max_discount) $desc .= ", tối đa " . number_format($coupon->max_discount) . "đ";
                return $desc;
            case 'fixed':
                return "Giảm " . number_format($coupon->discount_value) . "đ cho đơn hàng";
            case 'free_shipping':
                return "Miễn phí vận chuyển";
            case 'fixed_shipping':
                return "Giảm " . number_format($coupon->discount_value) . "đ phí vận chuyển";
            default: return "Mã giảm giá";
        }
    }

    public function submit(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để thanh toán.'], 401);
        }

        try {
            $validated = $request->validate([
                'paymentMethod' => 'required|in:cod,momo,card',
                'name' => [
                    'nullable',
                    'exclude_if:shipping_address_id,!null',
                    'required_without:shipping_address_id',
                    'string', 'max:100', 'min:2', 'regex:/^[\p{L}\s]+$/u'
                ],
                'phone_number' => [
                    'nullable',
                    'exclude_if:shipping_address_id,!null',
                    'required_without:shipping_address_id',
                    'string', 'max:20', 'regex:/^(0|\+84)[0-9]{9,10}$/'
                ],
                'address' => [
                    'nullable',
                    'exclude_if:shipping_address_id,!null',
                    'required_without:shipping_address_id',
                    'string', 'max:255', 'regex:/^[\p{L}\p{N}\s,.-]+$/u'
                ],
                'province_id' => [
                    'nullable',
                    'exclude_if:shipping_address_id,!null',
                    'required_without:shipping_address_id',
                    'integer'
                ],
                'district_id' => [
                    'nullable',
                    'exclude_if:shipping_address_id,!null',
                    'required_without:shipping_address_id',
                    'integer'
                ],
                'ward_code' => [
                    'nullable',
                    'exclude_if:shipping_address_id,!null',
                    'required_without:shipping_address_id',
                    'string', 'max:100'
                ],
                'shipping_address_id' => 'nullable|exists:shipping_addresses,id,user_id,' . Auth::id(),
                'cart_item_ids' => 'required|string',
            ], [
                'name.required_without' => 'Vui lòng nhập họ và tên nếu không chọn địa chỉ có sẵn.',
                'name.regex' => 'Họ và tên chỉ được chứa chữ cái và khoảng trắng.',
                'name.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
                'name.max' => 'Họ và tên không được vượt quá 100 ký tự.',
                'phone_number.required_without' => 'Vui lòng nhập số điện thoại nếu không chọn địa chỉ có sẵn.',
                'phone_number.regex' => 'Số điện thoại không hợp lệ (bắt đầu bằng 0 hoặc +84, 9-10 số).',
                'phone_number.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
                'address.required_without' => 'Vui lòng nhập địa chỉ cụ thể nếu không chọn địa chỉ có sẵn.',
                'address.regex' => 'Địa chỉ chỉ được chứa chữ, số, dấu phẩy, dấu chấm, dấu gạch ngang.',
                'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
                'ward.regex' => 'Xã/Phường chỉ được chứa chữ, số và khoảng trắng.',
                'ward.max' => 'Xã/Phường không được vượt quá 100 ký tự.',
                'district.regex' => 'Quận/Huyện chỉ được chứa chữ, số và khoảng trắng.',
                'district.max' => 'Quận/Huyện không được vượt quá 100 ký tự.',
                'city.regex' => 'Tỉnh/Thành phố chỉ được chứa chữ, số và khoảng trắng.',
                'city.max' => 'Tỉnh/Thành phố không được vượt quá 100 ký tự.',
                'cart_item_ids.required' => 'Danh sách sản phẩm không hợp lệ.',
                'province_id.required_without' => 'Vui lòng chọn tỉnh/thành phố nếu không chọn địa chỉ có sẵn.',
                'district_id.required_without' => 'Vui lòng chọn quận/huyện nếu không chọn địa chỉ có sẵn.',
                'ward_code.required_without' => 'Vui lòng chọn xã/phường nếu không chọn địa chỉ có sẵn.',
                'ward_code.max' => 'Mã xã/phường không được vượt quá 100 ký tự.',
            ]);
                    if (!$request->filled('shipping_address_id')) {
            $client = new \GuzzleHttp\Client();
            $token = env('GHN_API_TOKEN');
            $ghnUrl = env('GHN_API_URL');

            // Fetch province name
            $provinceRes = $client->get($ghnUrl . '/master-data/province', ['headers' => ['Token' => $token]]);
            $provinces = json_decode($provinceRes->getBody(), true)['data'];
            $province = collect($provinces)->firstWhere('ProvinceID', (int) $request->province_id);
            $city = $province['ProvinceName'] ?? null;

            // Fetch district name
            $districtRes = $client->get($ghnUrl . '/master-data/district', [
                'headers' => ['Token' => $token],
                'query' => ['province_id' => (int) $request->province_id]
            ]);
            $districts = json_decode($districtRes->getBody(), true)['data'];
            $districtData = collect($districts)->firstWhere('DistrictID', (int) $request->district_id);
            $district = $districtData['DistrictName'] ?? null;

            // Fetch ward name
            $wardRes = $client->get($ghnUrl . '/master-data/ward', [
                'headers' => ['Token' => $token],
                'query' => ['district_id' => (int) $request->district_id]
            ]);
            $wards = json_decode($wardRes->getBody(), true)['data'];
            $wardData = collect($wards)->firstWhere('WardCode', $request->ward_code);
            $ward = $wardData['WardName'] ?? null;
        }
            $cartItemIds = array_filter(explode(',', $request->input('cart_item_ids')), fn ($id) => is_numeric($id) && intval($id) > 0);
            if (empty($cartItemIds)) {
                return response()->json(['success' => false, 'message' => 'Danh sách sản phẩm không hợp lệ.'], 422);
            }

            $cartItems = Cart::with(['productVariant.product'])->where('user_id', Auth::id())->whereIn('id', $cartItemIds)->get();
            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm để thanh toán.'], 422);
            }

            foreach ($cartItems as $item) {
                if ($item->productVariant->stock_quantity < $item->quantity) {
                    return response()->json(['success' => false, 'message' => "Sản phẩm {$item->productVariant->product->name} không đủ hàng."], 400);
                }
            }

            DB::beginTransaction();

            if ($request->filled('shipping_address_id')) {
                $shippingAddress = ShippingAddress::findOrFail($request->shipping_address_id);
            } else {
                $shippingAddress = ShippingAddress::firstOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'name' => $request->name,
                        'phone_number' => $request->phone_number,
                        'address' => $request->address,
                        'ward' => $ward,
                        'district' => $district,
                        'city' => $city
                    ],
                    ['is_default' => false]
                );
            }

            // Lấy lại phí ship thực tế từ session nếu có
            $couponData = session('applied_coupons', []);
            $orderDiscount = $couponData['order_discount'] ?? 0;
            $shippingDiscount = $couponData['shipping_discount'] ?? 0;
            $orderCode = $couponData['order_code'] ?? null;
            $shippingCode = $couponData['shipping_code'] ?? null;
            $shippingFee = $request->input('shipping_fee', 0);
            // cập nhật phí ship $shipping_fee và bảng orders
            if ($shippingFee < 0) {
                $shippingFee = 0; // Đảm bảo phí ship không âm
            }
            if ($shippingDiscount > $shippingFee) {
                $shippingDiscount = $shippingFee; // Đảm bảo giảm giá không vượt quá phí ship
            }
            if ($orderDiscount < 0) {
                $orderDiscount = 0; // Đảm bảo giảm giá không âm
            }
            if ($shippingDiscount < 0) {
                $shippingDiscount = 0; // Đảm bảo giảm giá không âm
            }
            $subtotal = $cartItems->sum(fn ($item) => $item->productVariant->price * $item->quantity);
            $totalPrice = $subtotal - $orderDiscount + ($shippingFee - $shippingDiscount);
            if ($totalPrice < 0) $totalPrice = 0;

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_code' => 'ORD-' . strtoupper(Str::random(8)),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $request->paymentMethod === 'card' ? 'online' : ($request->paymentMethod === 'momo' ? 'online' : 'cod'),
                'payment_status' => 'pending',
                'note' => $request->note ?? null,
                'shipping_address_id' => $shippingAddress->id,
                'shipping_fee' => $shippingFee,  // Đảm bảo dòng này có mặt!
                'order_discount' => $orderDiscount,  // Số tiền giảm cho đơn hàng
                'shipping_discount' => $shippingDiscount,  // Số tiền giảm cho phí ship
                'vnp_txn_ref' => $request->paymentMethod === 'card' ? time() . Str::random(4) : null,
                'coupon_id' => Coupon::where('code', $orderCode)->value('id'),
                'extra_info' => json_encode(['coupon_codes' => array_filter([$orderCode, $shippingCode])]),
            ]);

            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'import_price' => $item->productVariant->import_price,
                    'price' => $item->productVariant->price,
                    'subtotal' => $item->productVariant->price * $item->quantity,
                ]);
            }

            if ($orderCode) {
                Coupon::where('code', $orderCode)->increment('used_count');
            }
            if ($shippingCode) {
                Coupon::where('code', $shippingCode)->increment('used_count');
            }

            if ($request->paymentMethod === 'cod') {
                foreach ($cartItems as $item) {
                    $item->productVariant->decrement('stock_quantity', $item->quantity);
                }
                Cart::whereIn('id', $cartItemIds)->delete();
            } else {
                session(['pending_cart_item_ids' => $cartItemIds, 'pending_order_id' => $order->id]);
            }

            if ($request->paymentMethod === 'card') {
                $vnp_TmnCode = env('VNPAY_TMN_CODE', 'WOQ9FKH5');
                $vnp_HashSecret = env('VNPAY_HASH_SECRET', '8LFGJRBPXUPDP2M2EP394Y12EO1OD4TM');
                $vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
                $vnp_Returnurl = env('VNPAY_RETURN_URL', route('vnpay.return'));

                $vnp_TxnRef = $order->vnp_txn_ref;
                $vnp_OrderInfo = "Thanh toan don hang #" . $order->order_code;
                $vnp_OrderType = "billpayment";
                $vnp_Amount = $totalPrice * 100;
                $vnp_Locale = "vn";
                $vnp_IpAddr = $request->ip();

                $inputData = [
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_TxnRef" => $vnp_TxnRef,
                    "vnp_Version" => "2.1.0"
                ];
                if ($request->has('bank_code') && !empty($request->input('bank_code'))) {
                    $inputData['vnp_BankCode'] = $request->input('bank_code');
                }

                ksort($inputData);
                $hashData = "";
                foreach ($inputData as $key => $value) {
                    if ($key !== 'vnp_SecureHash') {
                        $hashData .= urlencode($key) . '=' . urlencode($value) . '&';
                    }
                }
                $vnp_SecureHash = hash_hmac('sha512', rtrim($hashData, '&'), $vnp_HashSecret);
                $vnpayUrl = $vnp_Url . '?' . http_build_query($inputData) . '&vnp_SecureHash=' . $vnp_SecureHash;
            }

            session()->forget('applied_coupons');
            DB::commit();
            Log::info('Checkout completed successfully', ['order_id' => $order->id]);

            if ($request->paymentMethod === 'card') {
                return response()->json(['success' => true, 'vnpay_url' => $vnpayUrl, 'message' => 'Đang chuyển hướng đến cổng thanh toán VNPay.']);
            } elseif ($request->paymentMethod === 'momo') {
                session(['pending_order_id' => $order->id]);
                return response()->json(['success' => true, 'redirect' => route('pay'), 'message' => 'Đơn hàng đã được tạo. Vui lòng thanh toán để hoàn tất.']);
            } else {
                session()->flash('order_success', 'Đơn hàng đã được tạo thành công!');
                Log::info('Checkout redirect', ['redirect' => route('orders.index')]); // Thêm dòng này để kiểm tra
                return response()->json([
                    'success' => true,
                    'redirect' => route('orders.index'),
                    'message' => 'Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.'
                ]);
               
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng kiểm tra lại thông tin nhập.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Checkout error: ' . $e->getMessage(), ['exception' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage()], 500);
        }
    }

    public function retryPayment(Request $request, Order $order)
    {
        if (in_array($order->payment_method, ['online', 'bank_transfer']) && in_array($order->payment_status, ['pending', 'failed']) && in_array($order->status, ['pending', 'cancelled'])) {
            $order->update(['vnp_txn_ref' => 'RETRY' . time() . $order->id]);
            session(['pending_order_id' => $order->id]);

            $vnp_TmnCode = env('VNPAY_TMN_CODE', 'WOQ9FKH5');
            $vnp_HashSecret = env('VNPAY_HASH_SECRET', '8LFGJRBPXUPDP2M2EP394Y12EO1OD4TM');
            $vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
            $vnp_Returnurl = env('VNPAY_RETURN_URL', route('vnpay.return'));

            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $order->total_price * 100,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $request->ip(),
                "vnp_Locale" => "vn",
                "vnp_OrderInfo" => "Thanh toán lại đơn hàng #" . $order->order_code,
                "vnp_OrderType" => "billpayment",
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $order->vnp_txn_ref,
                "vnp_BankCode" => "NCB"
            ];
            ksort($inputData);
            $hashData = "";
            foreach ($inputData as $key => $value) if ($key !== 'vnp_SecureHash') $hashData .= urlencode($key) . '=' . urlencode($value) . '&';
            $vnp_SecureHash = hash_hmac('sha512', rtrim($hashData, '&'), $vnp_HashSecret);
            $vnpayUrl = $vnp_Url . '?' . http_build_query($inputData) . '&vnp_SecureHash=' . $vnp_SecureHash;

            return redirect($vnpayUrl);
        }
        return redirect()->back()->with('error', 'Không thể thanh toán lại đơn hàng này.');
    }
}