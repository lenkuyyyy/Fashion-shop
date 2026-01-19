<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductClientController extends Controller
{
    
    public function index(Request $request, $slug = null)
    {
        // Lấy dữ liệu cho bộ lọc
        $categories = Category::where('status', 'active')->get();
        $brands = Brand::where('status', 'active')->get();
        $sizes = ProductVariant::select('size')->distinct()->pluck('size');
        $colors = ProductVariant::select('color')->distinct()->pluck('color');

        $headerSearch = $request->input('header_search');
        $searchTerm = null;
        $productsQuery = Product::with(['variants', 'reviews', 'brand']);

        if ($headerSearch) {
            $searchTerm = $headerSearch;
            $productsQuery->where(function ($query) use ($headerSearch) {
                $query->where('name', 'like', '%' . $headerSearch . '%')
                      ->orWhere('description', 'like', '%' . $headerSearch . '%')
                      ->orWhereHas('brand', function ($q) use ($headerSearch) {
                          $q->where('name', 'like', '%' . $headerSearch . '%');
                      });
            });
        } else {
            $productsQuery = Product::with(['variants', 'reviews']);

            // Áp dụng bộ lọc
            if ($slug) {
                $selectedCategory = Category::where('slug', $slug)->firstOrFail();
                $productsQuery->where('category_id', $selectedCategory->id);
            }
            if ($request->has('brand') && $request->brand) {
                $productsQuery->where('brand_id', $request->brand);
            }
            if ($request->has('size') && $request->size) {
                $productsQuery->whereHas('variants', function ($query) use ($request) {
                    $query->where('size', $request->size);
                });
            }
            if ($request->has('color') && $request->color) {
                $productsQuery->whereHas('variants', function ($query) use ($request) {
                    $query->where('color', $request->color);
                });
            }
            if ($request->has('price_min') || $request->has('price_max')) {
                $priceMin = $request->price_min ?? 0;
                $priceMax = $request->price_max;

                $productsQuery->whereHas('variants', function ($query) use ($priceMin, $priceMax) {
                    $query->where(function ($subQuery) use ($priceMin, $priceMax) {
                        $subQuery->where('price', '>=', $priceMin);

                        if ($priceMax !== null) {
                            $subQuery->where('price', '<=', $priceMax);
                        }
                    });
                });
            }




            // Tìm kiếm từ sidebar
            $searchTerm = trim($request->search);
            if ($searchTerm) {
                $productsQuery->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $searchTerm . '%');
                });
            }
        }
     
        // Sắp xếp sản phẩm
        $validSorts = ['newest', 'sales', 'likes', 'rating', 'best_selling'];
        $sort = $request->input('sort', 'newest');
        $sortWarning = null;

        if (!in_array($sort, $validSorts)) {
            if ($sort === 'discount') {
                $sortWarning = 'Sắp xếp theo giảm giá hiện không khả dụng.';
            }
            $sort = 'newest';
        }

        switch (request()->sort) {
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            case 'rating':
                $productsQuery->addSelect(['*', DB::raw('
                    (SELECT AVG(reviews.rating) 
                     FROM reviews 
                     WHERE reviews.product_id = products.id 
                     AND reviews.status = "approved") as avg_rating
                ')])
                    ->whereHas('reviews', function ($query) {
                        $query->where('status', 'approved');
                    })
                    ->orderByRaw('avg_rating DESC NULLS LAST');
                break;
            case 'sales':
                // Sắp xếp theo số lượt thích (likes_count) nhiều nhất
                $productsQuery->withCount('likes')->orderByDesc('likes_count');
                break;
            case 'best_selling':
                // Đếm số lượng bán thành công từ order_items + orders
                $productsQuery->withCount(['orderDetails as sold_count' => function ($query) {
                    $query->select(\DB::raw('SUM(quantity)'))
                        ->join('orders', 'order_details.order_id', '=', 'orders.id')
                        ->where('orders.status', 'success');
                }])->orderByDesc('sold_count');
                break;
            default:
                $productsQuery->orderBy('created_at', 'desc');
                break;
        }

        // Phân trang
        $products = $productsQuery->where('products.status', 'active')->paginate(9)->appends($request->query());

        $noResults = $products->isEmpty() && $request->hasAny(['category', 'brand', 'size', 'color', 'price_min', 'price_max', 'search', 'sort', 'header_search']);

        return view('client.pages.products-client', compact('products', 'categories', 'brands', 'sizes', 'colors', 'noResults', 'searchTerm', 'sort', 'sortWarning'));
    }
    /**
     * Lấy sản phẩm mới nhất cho từng nhóm: Nam, Nữ, Trẻ em để hiển thị ở trang chủ
     */
    public function getHomeSections()
    {
        // Lấy group_id cho từng nhóm
        $menGroupId = 1;
        $womenGroupId = 2;
        $kidsGroupId = 3;

        // Lấy các category_id theo group
        $menCategoryIds = Category::where('group_id', $menGroupId)->pluck('id');
        $womenCategoryIds = Category::where('group_id', $womenGroupId)->pluck('id');
        $kidsCategoryIds = Category::where('group_id', $kidsGroupId)->pluck('id');

        // Lấy sản phẩm mới nhất cho từng nhóm
        $menProducts = Product::with(['variants' => function($q){ $q->where('status', 'active'); }])
            ->whereIn('category_id', $menCategoryIds)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $womenProducts = Product::with(['variants' => function($q){ $q->where('status', 'active'); }])
            ->whereIn('category_id', $womenCategoryIds)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $kidsProducts = Product::with(['variants' => function($q){ $q->where('status', 'active'); }])
            ->whereIn('category_id', $kidsCategoryIds)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('client.layouts.index', compact('menProducts', 'womenProducts', 'kidsProducts'));
    }
}

