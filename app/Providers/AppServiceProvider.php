<?php

namespace App\Providers;

use App\Models\Cart;
use App\Services\SmsService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route; // Thêm dòng này
use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\SlideComposer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // $this->app->singleton(SmsService::class, function () {
        //     return new SmsService();
        // });
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        View::composer('*', function ($view) {
            $cartCount = 0;
            if (Auth::check()) {
                $cartCount = Cart::where('user_id', Auth::id())->count();
            }
            $view->with('cartCount', $cartCount);
        });

        View::composer('client.layouts.banner', SlideComposer::class);

        // Đăng ký route API
        Route::prefix('api')
             ->middleware('api')
             ->namespace('App\Http\Controllers')
             ->group(base_path('routes/api.php'));
    }
}