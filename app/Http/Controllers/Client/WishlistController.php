<?php

namespace App\Http\Controllers\Client;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Product;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        if (Auth::check()) {
            $wishlistItems = Wishlist::with('product')
                ->where('user_id', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        } else {
            // Nếu người dùng chưa đăng nhập, lấy wishlist từ localStorage
            $wishlistItems = [];
        }

        // Lấy danh sách sản phẩm mới nhất để hiển thị
        $latestProducts = Product::with(['category', 'brand'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return view('client.pages.wishlist', compact('wishlistItems', 'latestProducts'));
    }

    // Trong WishlistController
    public function getGuestWishlist(Request $request)
    {
        // Lấy danh sách ID sản phẩm từ localStorage
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json([], 200); // trả mảng rỗng thay vì lỗi
        }

        // Lấy sản phẩm từ cơ sở dữ liệu dựa trên ID
        $products = Product::with(['category', 'brand'])
            ->whereIn('id', $ids)
            ->get()
            ->map(function ($product) {
                // Chỉ lấy các trường cần thiết để trả về
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'thumbnail' => $product->thumbnail,
                    'category' => $product->category->name,
                    'brand' => $product->brand->name,
                    'status' => $product->status,
                ];
            });

        // Trả về danh sách sản phẩm dưới dạng JSON
        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $productId = $request->input('product_id');

        // Kiểm tra đã tồn tại trong wishlist chưa
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Sản phẩm đã có trong danh sách yêu thích.');
        }

        // Kiểm tra xem sản phẩm có tồn tại và đang hoạt động không
        $product = Product::find($productId);
        if (!$product || $product->status !== 'active') {
            return back()->with('error', 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.');
        }

        // Nếu chưa có, thì thêm vào
        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
        ]);

        return back()->with('success', 'Đã thêm vào danh sách yêu thích.');
    }



    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wishlist $wishlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $wishlistItem = Wishlist::find($id);

        // Kiểm tra xem sản phẩm có trong wishlist của người dùng hiện tại không
        // Nếu không có hoặc không phải của người dùng hiện tại, trả về lỗi
        if (!$wishlistItem || $wishlistItem->user_id !== Auth::id()) {
            return back()->with('error', 'Không tìm thấy sản phẩm trong danh sách yêu thích.');
        }

        // Xoá sản phẩm khỏi wishlist
        // Chỉ xoá nếu sản phẩm thuộc về người dùng hiện tại
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $wishlistItem->product_id)
            ->delete();

        return back()->with('success', 'Đã xoá khỏi danh sách yêu thích.');
    }

    public function sync(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập.']);
        }

        $wishlist = $request->input('wishlist', []);

        if (!is_array($wishlist) || empty($wishlist)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu wishlist không hợp lệ.']);
        }

        $validProductIds = Product::whereIn('id', $wishlist)
            ->where('status', 'active') // Chỉ lấy sản phẩm đang hoạt động
            ->pluck('id')
            ->toArray();

        $insertData = [];
        foreach ($validProductIds as $productId) {
            $insertData[] = [
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'created_at' => now(),
            ];
        }

        Wishlist::insertOrIgnore($insertData);

        return response()->json([
            'success' => true,
            'message' => 'Đồng bộ wishlist thành công.',
            'inserted' => count($insertData)
        ]);
    }

    public function check($id)
    {
        // Kiểm tra xem sản phẩm có tồn tại không
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Không tìm thấy sản phẩm'], 404);
        }

        // Trả về trạng thái của sản phẩm dưới dạng JSON
        return response()->json([
            'id' => $product->id,
            'status' => $product->status
        ]);
    }
}
