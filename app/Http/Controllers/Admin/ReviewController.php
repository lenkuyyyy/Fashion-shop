<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function index(Request $request)
{
    $query = Review::with(['user', 'product.category']);

    if ($request->filled('rating')) {
        $query->where('rating', $request->rating);
    }

    if ($request->filled('status')) {
        $query->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
    }

    // Bộ lọc ngày cụ thể
    if ($request->filled('date')) {
        $query->whereDate('created_at', $request->date);
    }

    // Bộ lọc theo khoảng thời gian định nghĩa
    if ($request->filled('date_range')) {
        $now = Carbon::now();
        switch ($request->date_range) {
            case 'today':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'yesterday':
                $query->whereDate('created_at', $now->copy()->subDay()->toDateString());
                break;
            case 'last_7_days':
                $query->whereBetween('created_at', [$now->copy()->subDays(7), $now]);
                break;
            case 'last_30_days':
                $query->whereBetween('created_at', [$now->copy()->subDays(30), $now]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', $now->month)
                      ->whereYear('created_at', $now->year);
                break;
            case 'this_year':
                $query->whereYear('created_at', $now->year);
                break;
        }
    }

    $reviews = $query->latest()->paginate(5)->appends($request->query());

    return view('admin.others_menu.reviews', compact('reviews'));
}




    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'approved';
        $review->save();

        return redirect()->back()->with('success', 'Đã duyệt đánh giá.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Đã xoá đánh giá.');
    }
    // Store a new review from frontend form
    public function store(Request $request)
{
    // Thêm validation cho order_detail_id
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'order_detail_id' => 'required|exists:order_details,id|unique:reviews,order_detail_id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|max:1000',
    ]);

    Review::create([
        'user_id' => auth()->id(),
        'product_id' => $request->product_id,
        'order_detail_id' => $request->order_detail_id, // Thêm dòng này để lưu
        'rating' => $request->rating,
        'comment' => $request->comment,
        'status' => 'pending', 
    ]);

    return redirect()->route('orders.index')->with('success', 'Cảm ơn bạn đã gửi đánh giá!');
}
}
