<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Hiển thị giỏ hàng của người dùng
     */
    public function index()
    {
        $cartItems = collect([]);
        $subtotal = 0;
        $total = 0;

        if (Auth::check()) {
            $cartItems = Cart::with(['productVariant' => function($query){
                $query->select('id', 'product_id', 'price', 'stock_quantity', 'status', 'image', 'size', 'color');
            }, 'productVariant.product' => function($query){
                $query->select('id', 'name');
            }])
            ->where('user_id', Auth::id())
            ->get();

            $subtotal = $cartItems->sum(function($item) {
                return $item->productVariant->price * $item->quantity;
            });

            $total = $subtotal;
        }

        $cartItemsForJs = $cartItems->map(function($item) {
            return [
                'id' => $item->id,
                'status' => $item->productVariant->status ?? 'inactive'
            ];
        });
        return view('client.pages.cart', compact('cartItems', 'subtotal', 'total', 'cartItemsForJs'));
    }



    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.');
        }

        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::find($request->product_variant_id);
        if (!$variant) {
            return back()->with('error', 'Sản phẩm này không còn tồn tại!');
        }

        // Kiểm tra trạng thái active
        if ($variant->status !== 'active') {
            return back()->with('error', 'Sản phẩm này hiện không còn hoạt động!');
        }

        if ($variant->stock_quantity < $request->quantity) {
            return back()->with('error', 'Số lượng vượt quá tồn kho!');
        }

        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_variant_id', $variant->id)
                    ->first();

        $currentQty = $cart ? $cart->quantity : 0;
        $newQty = $currentQty + $request->quantity;

        if ($newQty > $variant->stock_quantity) {
            return back()->with('error', 'Tổng số lượng trong giỏ vượt quá tồn kho!');
        }

        Cart::updateOrCreate(
            ['user_id' => Auth::id(), 'product_variant_id' => $variant->id],
            ['quantity' => $newQty]
        );

        return back()->with('success', 'Đã thêm vào giỏ hàng');
    }



    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     */
    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::findOrFail($request->cart_id);

        if ($cart->user_id !== Auth::id()) {
            return response()->json(['error' => 'Không có quyền sửa giỏ hàng này!'], 403);
        }

        $variant = $cart->productVariant;
        if (!$variant) {
            return response()->json(['error' => 'Sản phẩm này không còn tồn tại!'], 404);
        }

        // Kiểm tra trạng thái active
        if ($variant->status !== 'active') {
            return response()->json(['error' => 'Sản phẩm này đã ngừng bán!'], 400);
        }

        if ($variant->stock_quantity < $request->quantity) {
            return response()->json(['error' => 'Không đủ hàng trong kho! Chỉ còn ' . $variant->stock_quantity . ' sản phẩm.'], 400);
        }

        $cart->quantity = $request->quantity;
        $cart->save();

        $itemTotal = $variant->price * $cart->quantity;

        $cartItems = Cart::with('productVariant.product')
                        ->where('user_id', Auth::id())
                        ->get();

        $subtotal = $cartItems->sum(function($item) {
            return $item->productVariant->price * $item->quantity;
        });

        $total = $subtotal;

        return response()->json([
            'message' => 'Cập nhật giỏ hàng thành công!',
            'itemTotal' => round($itemTotal, 2),
            'subtotal' => round($subtotal, 2),
            'total' => round($total, 2),
            'stock_quantity' => $variant->stock_quantity
        ]);
    }

    /**
     * Xóa 1 sản phẩm trong giỏ hàng
     */
    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
        ]);

        $cart = Cart::find($request->cart_id);

        if ($cart && $cart->user_id == Auth::id()) {
            $cart->delete();

            $newCount = Cart::where('user_id', Auth::id())->count();

            return response()->json([
                'success' => true,
                'newCartCount' => $newCount
            ]);
        }

        return response()->json(['error' => 'Không thể xóa sản phẩm này!'], 403);
    }

    /**
     * Xóa nhiều sản phẩm được chọn trong giỏ hàng
     */
    public function removeSelected(Request $request)
    {
        $request->validate([
            'cart_ids' => 'required|array'
        ]);

        // Chỉ xóa sản phẩm thuộc user hiện tại
        Cart::whereIn('id', $request->cart_ids)
            ->where('user_id', Auth::id())
            ->delete();

        // Đếm lại số sản phẩm còn lại trong giỏ
        $newCount = Cart::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'newCartCount' => $newCount
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ bằng Ajax
     */
    public function addAjax(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::find($request->product_variant_id);
        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm này không còn tồn tại!']);
        }

        // Kiểm tra trạng thái active
        if ($variant->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Sản phẩm này đã ngừng bán!']);
        }

        // Kiểm tra tồn kho
        if ($variant->stock_quantity < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Số lượng vượt quá tồn kho!']);
        }

        // Kiểm tra đã có trong giỏ chưa
        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_variant_id', $variant->id)
                    ->first();

        $currentQty = $cart ? $cart->quantity : 0;
        $newQty = $currentQty + $request->quantity;

        if ($newQty > $variant->stock_quantity) {
            return response()->json(['success' => false, 'message' => 'Tổng số lượng trong giỏ vượt quá tồn kho!']);
        }

        // Cập nhật giỏ hàng
        Cart::updateOrCreate(
            ['user_id' => Auth::id(), 'product_variant_id' => $variant->id],
            ['quantity' => $newQty]
        );

        $cartCount = Cart::where('user_id', Auth::id())->count(); // Đếm lại giỏ

        return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng!', 'cart_count' => $cartCount]);
    }

    /**
     * API kiểm tra tồn kho cho các sản phẩm trong giỏ hàng
     */
    public function checkStock(Request $request)
    {
        $cartIds = $request->input('cart_ids', []);
        $quantities = $request->input('quantities', []);

        $outOfStock = [];

        foreach ($cartIds as $i => $cartId) {
            $cart = Cart::with('productVariant')->where('user_id', Auth::id())->find($cartId);
            if (!$cart || !$cart->productVariant) {
                $outOfStock[] = [
                    'cart_id' => $cartId,
                    'reason' => 'Sản phẩm không tồn tại'
                ];
                continue;
            }
            if ($cart->productVariant->status !== 'active') {
                $outOfStock[] = [
                    'cart_id' => $cartId,
                    'reason' => 'Sản phẩm đã ngừng bán'
                ];
                continue;
            }
            $qty = isset($quantities[$i]) ? (int)$quantities[$i] : $cart->quantity;
            if ($cart->productVariant->stock_quantity < $qty) {
                $outOfStock[] = [
                    'cart_id' => $cartId,
                    'reason' => 'Chỉ còn ' . $cart->productVariant->stock_quantity . ' sản phẩm trong kho'
                ];
            }
        }

        return response()->json([
            'ok' => count($outOfStock) === 0,
            'out_of_stock' => $outOfStock
        ]);
    }
}
