<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $trashedCount = Coupon::onlyTrashed()->count();

        return view('admin.coupons.index', compact('coupons', 'trashedCount'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(StoreCouponRequest $request)
    {
        // Nếu là free_shipping, đặt giá trị là 0 để đảm bảo tính nhất quán
        $data = $request->validated();
        if ($data['discount_type'] === 'free_shipping') {
            $data['discount_value'] = 0;
        }

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Thêm voucher thành công!');
    }
    
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $data = $request->validated();
        if ($data['discount_type'] === 'free_shipping') {
            $data['discount_value'] = 0;
        }
        
        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật voucher thành công!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Voucher đã được chuyển vào thùng rác!');
    }

    public function trashed(Request $request)
    {
        $query = Coupon::onlyTrashed();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $coupons = $query->orderBy('deleted_at', 'desc')->paginate(10);

        return view('admin.coupons.trashed', compact('coupons'));
    }

    public function restore($id)
    {
        $coupon = Coupon::onlyTrashed()->findOrFail($id);
        $coupon->restore();

        return redirect()->route('admin.coupons.trashed')->with('success', 'Khôi phục voucher thành công!');
    }
}