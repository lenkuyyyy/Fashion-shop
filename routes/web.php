

<?php

use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\CustomerNotificationController;
use App\Http\Controllers\Client\Auth\LoginController;
use App\Http\Controllers\Client\Auth\RegisterController;
use App\Http\Controllers\Client\Auth\VerifyController;
use App\Http\Controllers\Client\ClientNotificationController;
use App\Http\Controllers\Client\AccountController;
use App\Http\Controllers\Client\Auth\Mail\ResetPasswordController;
use App\Http\Controllers\Client\Auth\Mail\ForgotPasswordController;
use App\Http\Controllers\Client\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Http\Controllers\Client\ProductClientController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Client\Auth\SocialAuthController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\WishlistController;
use App\Http\Controllers\Client\ReturnRequestController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\VNPayController; // VNPay payment controller
// Route cho Admin
Route::middleware(['auth', 'restrict.admin'])->group(function () {
    // Dashboard admin - nhóm route để thống kê
    Route::get('/admin', [StatisticsController::class, 'index'])->name('statistical');
    Route::get('/admin/statistics/filter-revenue', [StatisticsController::class, 'filterRevenue']);
    Route::get('/admin/statistics/orders-per-day', [StatisticsController::class, 'getOrdersPerDay']);
    Route::get('/admin/statistics/top-products', [StatisticsController::class, 'getTopSellingProducts']);
    Route::get('/admin/statistics/order-status', [StatisticsController::class, 'orderStatusByDate']);
    Route::get('/admin/statistics/low-stock', [StatisticsController::class, 'lowStockVariants'])->name('admin.statistics.low-stock');
    Route::get('/admin/statistics/pending-reviews', [StatisticsController::class, 'getPendingReviews']);
    Route::get('/admin/statistics/latest-return-requests', [StatisticsController::class, 'getLatestReturnRequests']);
    Route::get('/admin/statistics/latest-notifications', [StatisticsController::class, 'getLatestNotifications']);
    Route::get('/admin/orders/cancel-requests/today', [StatisticsController::class, 'getPendingCancelRequests'])->name('admin.cancel-requests.today');

    // Nhóm route admin với prefix và name
    Route::prefix('admin')->name('admin.')->group(function () {
        // Sản phẩm (Products)
        Route::resource('products', AdminProductController::class);
        Route::post('/products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
        Route::post('/products/{id}/addVariants', [AdminProductController::class, 'addVariants'])->name('products.addVariants');

        // Đơn hàng (Orders)
        Route::resource('orders', OrderController::class)
            ->except(['store', 'create', 'edit', 'destroy']);
        // Cập nhật trạng thái trả hàng
        Route::patch('/return-requests/{id}', [OrderController::class, 'updateReturnStatus'])
            ->name('return-requests.update');
        // Hủy đơn hàng
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/cancel/reject', [OrderController::class, 'rejectCancel'])->name('orders.cancel.reject');
        Route::post('/admin/orders/{order}/refunded', [OrderController::class, 'markRefunded'])->name('orders.refunded');
        // Xác nhận hoặc từ chối yêu cầu huỷ đơn
        Route::post('orders/cancel-request/{order}', [OrderController::class, 'handleCancelRequest'])
            ->name('admin.orders.handleCancelRequest');

        // Quan ly contacts
        Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class)
                    ->only(['index', 'show', 'destroy']);

        // Danh mục (Categories)
        Route::get('/categories/trashed', [CategoryController::class, 'trashed'])->name('categories.trashed');
        Route::resource('categories', CategoryController::class);
        Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
        Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.forceDelete');

        // Thương hiệu (Brands)
        Route::resource('brands', BrandController::class)->except(['show']);
        Route::patch('/brands/{id}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggleStatus');
        Route::get('/brands/trashed', [BrandController::class, 'trashed'])->name('brands.trashed');
        Route::post('/brands/{id}/restore', [BrandController::class, 'restore'])->name('brands.restore');
        // News Routes
        Route::get('/news', [NewsController::class, 'index'])->name('news.index');
        Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/news', [NewsController::class, 'store'])->name('news.store');
        Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
        Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');
        Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
        Route::patch('/news/{news}/toggle-status', [NewsController::class, 'toggleStatus'])->name('news.toggleStatus');
        Route::get('/news/trashed', [NewsController::class, 'trashed'])->name('news.trashed');
        Route::post('/news/{id}/restore', [NewsController::class, 'restore'])->name('news.restore');
            // Slide Routes
            Route::get('/slides', [SlideController::class, 'index'])->name('slides.index');
            Route::get('/slides/create', [SlideController::class, 'create'])->name('slides.create');
            Route::post('/slides', [SlideController::class, 'store'])->name('slides.store');
            Route::get('/slides/{slide}/edit', [SlideController::class, 'edit'])->name('slides.edit');
            Route::put('/slides/{slide}', [SlideController::class, 'update'])->name('slides.update');
            Route::delete('/slides/{slide}', [SlideController::class, 'destroy'])->name('slides.destroy');
            Route::patch('/slides/{slide}/toggle-status', [SlideController::class, 'toggleStatus'])->name('slides.toggleStatus');
            Route::get('/slides/trashed', [SlideController::class, 'trashed'])->name('slides.trashed');
            Route::post('/slides/{id}/restore', [SlideController::class, 'restore'])->name('slides.restore');

        // Voucher (Coupons)
        Route::get('coupons/trashed', [CouponController::class, 'trashed'])->name('coupons.trashed');
        Route::post('coupons/{id}/restore', [CouponController::class, 'restore'])->name('coupons.restore');
        Route::resource('coupons', CouponController::class)->except(['show']);
        //banner (slides)
        Route::get('/banners', [BannerController::class, 'index'])->name('admin.banners.index');
        Route::post('/banners', [BannerController::class, 'update'])->name('admin.banners.update');

    });

    // Người dùng (Users)
    Route::resource('/users', UserController::class)->names('admin.users');
    Route::get('/admin/users/banned', [UserController::class, 'banned'])->name('admin.users.banned');
    Route::patch('/users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('admin.users.forceDelete');

    // Đơn hàng (Orders)
    Route::get('/orders', function () {
        return view('admin.orders.orders');
    })->name('orders');

    // Đánh giá (Reviews)
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews/{id}/approve', [ReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    // Thông báo admin
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/{id}/read', [AdminNotificationController::class, 'markAsRead'])->name('admin.notifications.markAsRead');
    Route::get('/notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])->name('admin.notifications.markAllRead');

    // Admin gửi thông báo cho khách hàng
    Route::get('/customer-notifications', [CustomerNotificationController::class, 'index'])->name('customer-notifications');
    Route::get('/customer-notifications/create', [CustomerNotificationController::class, 'create'])->name('admin.customer-notifications.create');
    Route::post('/customer-notifications', [CustomerNotificationController::class, 'store'])->name('admin.customer-notifications.store');
});

// Trang chủ Khách hàng (Client)
Route::get('/', [ProductClientController::class, 'getHomeSections'])->name('home');

// Route riêng cho Khách hàng (Client) đã đăng nhập
Route::middleware('auth')->prefix('client')->group(function () {
    Route::post('/wishlist/store', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// Route hiển thị giỏ hàng
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Các chức năng thêm/xóa/cập nhật giỏ hàng và áp mã giảm giá - cần đăng nhập
Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/remove-selected', [CartController::class, 'removeSelected'])->name('cart.removeSelected');
    Route::post('/cart/add-ajax', [CartController::class, 'addAjax'])->name('cart.addAjax');
    // Route kiểm tra tồn kho giỏ hàng
    Route::post('/cart/check-stock', [CartController::class, 'checkStock'])->name('cart.checkStock');

    // Route hiển thị và xử lý thanh toán

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/submit', [CheckoutController::class, 'submit'])->name('checkout.submit');

    // [XÓA BỎ] - Route cũ để áp dụng một mã
    // Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon');

    // [THÊM MỚI] - Các route cho việc lấy và áp dụng voucher bằng AJAX
    Route::get('/checkout/get-coupons', [CheckoutController::class, 'getAvailableCoupons'])->name('checkout.getAvailableCoupons');
    Route::post('/checkout/apply-coupons', [CheckoutController::class, 'applyCoupons'])->name('checkout.applyCoupons');

    Route::post('/checkout/retry/{order}', [CheckoutController::class, 'retryPayment'])->name('checkout.retry');
    // Thanh toán Momo
Route::get('/pay', [ClientOrderController::class, 'pay'])->name('pay');
Route::post('/momo_payment', [ClientOrderController::class, 'momo_payment'])->name('momo_payment');
Route::post('/momo_callback', [ClientOrderController::class, 'momoCallback'])->name('momo_callback');
// Thanh toán VNPay
Route::get('/vnpay/return', [VNPayController::class, 'paymentReturn'])->name('vnpay.return');
Route::post('/vnpay/ipn', [VNPayController::class, 'ipn'])->name('vnpay.ipn');
Route::get('/vnpay/test', [VNPayController::class, 'testPayment'])->name('vnpay.test');

Route::get('/order/success/{order}', [ClientOrderController::class, 'success'])->name('order.success');
Route::get('/order/failed', [ClientOrderController::class, 'failed'])->name('order.failed');


// Hủy đơn hàng và yêu cầu trả hàng
Route::post('/order/{orderId}/cancel-request', [ClientOrderController::class, 'createOrderCancelNotificationToAdmin'])->name('order.cancel.request');
Route::post('/order/{id}/received', [ClientOrderController::class, 'received'])->name('order.received');
Route::post('/orders/{id}/return-request', [ReturnRequestController::class, 'requestReturn'])->name('orders.requestReturn');
// Route cho tài khoản khách hàng
Route::get('/account', [AccountController::class, 'show'])->name('account.show');
Route::post('/account/client', [AccountController::class, 'update'])->name('account.update');
// Route cho trang đơn hàng của khách hàng
Route::get('/orders', [ClientOrderController::class, 'index'])->name('orders.index');
// lấy phí vận chuyển
Route::post('/checkout/shipping-fee', [CheckoutController::class, 'getShippingFee'])->name('checkout.getShippingFee');

});

// Thông báo khách hàng
Route::get('/client/notifications', [ClientNotificationController::class, 'index'])->name('client.notifications');
Route::post('/client/notifications/mark-all-read', [ClientNotificationController::class, 'markAllRead'])->name('client.notifications.markAllRead');
// Các trang tĩnh
Route::get('/about', function () {
    return view('client.pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('client.pages.contact');
})->name('contact');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::get('/subscribe', [ContactController::class, 'subscribe'])->name('newsletter.subscribe');

// Route hiển thị danh sách yêu thích
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/guest', [WishlistController::class, 'getGuestWishlist'])->name('wishlist.guest');
Route::post('/wishlist/sync', [WishlistController::class, 'sync'])->name('wishlist.sync');
Route::get('/wishlist/check/product/{id}', [WishlistController::class, 'check'])->name('wishlist.check');


// Chi tiết sản phẩm và review
Route::get('/products-client/{slug?}', [ProductClientController::class, 'index'])->name('products-client');
Route::get('/detail-product/{id}', [ProductController::class, 'show'])->name('detail-product');
Route::get('/detail-product/{id}/variants', [ProductController::class, 'getVariants'])->name('detail-product.variants');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
// Route cho Fashion Newsletters
Route::get('/fashion-newsletters', [\App\Http\Controllers\Client\ClientNewsController::class, 'fashionNewsletters'])->name('fashion-newsletters');

// Route cho chi tiết bài viết
Route::get('/news/{id}', [\App\Http\Controllers\Client\ClientNewsController::class, 'show'])->name('news.show');





// Route xác thực (Auth)
Route::middleware('web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'handleLogin'])->name('login.submit');

    Route::get('/logout', function () {
        Auth::logout();
        return redirect('/login')->with('success', 'Bạn đã đăng xuất');
    })->name('logout');

    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'handleRegister'])->name('register.submit');
});

// Xác thực email
Route::post('/register/otp/send', [RegisterController::class, 'sendOtp'])->name('register.otp.send');
Route::post('/register/otp/submit', [RegisterController::class, 'registerWithOtp'])->name('register.submit.otp');
Route::post('/verify/send', [VerifyController::class, 'send'])->name('verify.send');
Route::post('/verify/check', [VerifyController::class, 'check'])->name('verify.check');

// Đặt lại mật khẩu
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Đăng nhập Google (Socialite)
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('google.callback');

