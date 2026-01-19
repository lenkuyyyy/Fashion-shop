<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddVariantsRequest;
use App\Http\Requests\StoreProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Lấy danh sách danh mục từ cơ sở dữ liệu
        $categories = Category::all();
        $brands = Brand::all();

        // Định nghĩa mảng trạng thái với nhãn thân thiện
        $statuses = [
            'active' => 'Kích hoạt',
            'inactive' => 'Không kích hoạt',
            'out_of_stock' => 'Hết hàng',
        ];

        // Khởi tạo query với eager loading
        $query = Product::with(['category', 'brand']);

        // Biến kiểm tra xem có tìm kiếm hay không
        $hasSearch = false;

        // Lọc theo tên sản phẩm
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
            $hasSearch = true;
        }

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
            $hasSearch = true;
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
            $hasSearch = true;
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
            $hasSearch = true;
        }

        // Lấy danh sách sản phẩm với phân trang
        $products = $query->orderByDesc('id')->paginate(9);

        // Kiểm tra nếu có tìm kiếm nhưng không có kết quả
        $noResults = $hasSearch && $products->isEmpty();

        // 5 sản phẩm mới nhất
        $latestProducts = Product::with('variants')->orderByDesc('id')->take(5)->get();

        return view(
            'admin.products.products',
            compact('products', 'categories', 'brands', 'statuses', 'noResults', 'latestProducts')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $brands = Brand::all();
        $categories = Category::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
        DB::beginTransaction();
        if ($request->hasFile('thumbnail')) {
            // Lưu ảnh mới với tên có timestamp
            $file = $request->file('thumbnail');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/products', $fileName, 'public');
        } else {
            $filePath = null;
        }

        try {
            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'sku' => strtoupper(Str::random(8)),
                'slug' => Str::slug($request->name . '-' . Str::random(4)),
                'description' => $request->description,
                'short_description' => $request->short_description,
                'status' => $request->status,
                'thumbnail' => $filePath ?? null,
            ]);

            $variants = $request->variants; // Bây giờ đã là mảng PHP

            if (empty($variants)) {
                return redirect()
                    ->back()
                    ->with('error', 'Vui lòng nhập thông tin biến thể sản phẩm.');
            }

            foreach ($variants as $variant) {
                $product->variants()->create([
                    'color' => $variant['color'],
                    'size' => $variant['size'],
                    'price' => $variant['price'],
                    'import_price' => $variant['import_price'], // Thêm trường giá nhập
                    'stock_quantity' => $variant['quantity'],
                    'sku' => $variant['sku'],
                    'status' => 'active'
                ]);
            }

            DB::commit();
            return redirect()
                ->route('admin.products.show', $product->id)
                ->with('success', 'Sản phẩm đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $brands = Brand::all();
        $categories = Category::all();
        $product = Product::with('variants')->findOrFail($id); // add reletionship with reviews later
        return view('admin.products.show', compact('product', 'categories', 'brands'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);

            // Xử lý ảnh thumbnail
            if ($request->hasFile('thumbnail')) {
                // Xóa ảnh cũ nếu tồn tại
                if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                    Storage::disk('public')->delete($product->thumbnail);
                }

                // Lưu ảnh mới với tên có timestamp
                $file = $request->file('thumbnail');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/products', $fileName, 'public');
            } else {
                $filePath = $product->thumbnail;
            }

            // Cập nhật thông tin sản phẩm
            $product->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'brand_id' => $request->input('brand_id'),
                'category_id' => $request->input('category_id'),
                'status' => $request->input('status'),
                'thumbnail' => $filePath,
                'short_description' => $request->input('short_description'),
            ]);

            // Xử lý biến thể
            if ($request->has('variants')) {
                foreach ($request->input('variants') as $index => $variantData) {
                    $variant = ProductVariant::find($variantData['id']);
                    if (!$variant) {
                        Log::warning("Variant ID {$variantData['id']} not found for product ID {$id}");
                        continue;
                    }

                    // Nếu có ảnh mới
                    if ($request->hasFile("variants.$index.image")) {
                        // Xóa ảnh cũ nếu tồn tại
                        if ($variant->image && Storage::disk('public')->exists(str_replace('storage/', '', $variant->image))) {
                            Storage::disk('public')->delete(str_replace('storage/', '', $variant->image));
                        }

                        $file = $request->file("variants.$index.image");
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('uploads/variants', $fileName, 'public');
                        $variant->image = 'storage/' . $path;
                    }

                    $variant->color = $variantData['color'];
                    $variant->size = $variantData['size'];
                    $variant->price = $variantData['price'];
                    $variant->import_price = $variantData['import_price']; // Thêm trường giá nhập
                    $variant->stock_quantity = $variantData['stock_quantity'];
                    $variant->status = $variantData['status'];

                    if ($product->status === 'inactive') {
                        $variant->status = 'inactive';
                    }

                    $variant->save();
                }
            }

            DB::commit();
            return redirect()
                ->route('admin.products.show', $product->id)
                ->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update product failed: ' . $e->getMessage(), [
                'product_id' => $id,
                'request_data' => $request->all(),
            ]);
            return back()->with('error', 'Đã xảy ra lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        DB::beginTransaction();

        try {
            // 1. Cập nhật trạng thái sản phẩm
            $product = Product::findOrFail($id);
            $product->status = 'inactive';
            $product->save();

            // 2. Cập nhật tất cả các biến thể của sản phẩm đó
            ProductVariant::where('product_id', $id)
                ->update(['status' => 'inactive']);

            DB::commit();

            // 👉 Quay lại trang trước và flash thông báo
            return redirect()
                ->back()
                ->with(
                    'success',
                    'Sản phẩm ' . $product->name .
                        ' và các biến thể tương ứng đã được ngừng bán (inactive).'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->status = 'active';
            $product->save();

            // Khôi phục các biến thể
            ProductVariant::where('product_id', $id)->update(['status' => 'active']);

            return redirect()->back()->with('success', 'Sản phẩm và các biến thể đã được khôi phục.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi khôi phục: ' . $e->getMessage());
        }
    }

    public function addVariants(AddVariantsRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $variants = $request->variants;

            $added = 0;
            $skipped = [];

            if ($variants) {
                foreach ($variants as $variant) {
                    $exists = $product->variants()
                        ->where('color', $variant['color'])
                        ->where('size', $variant['size'])
                        ->exists();

                    if ($exists) {
                        $skipped[] = "{$variant['color']} - {$variant['size']}";
                        continue;
                    }

                    $product->variants()->create([
                        'color' => $variant['color'],
                        'size' => $variant['size'],
                        'import_price' => $variant['import_price'], // Thêm trường giá nhập
                        'price' => $variant['price'],
                        'stock_quantity' => $variant['quantity'],
                        'sku' => $variant['sku'],
                        'status' => $variant['status'] ?? 'active',
                    ]);

                    $added++;
                }
            }

            // Xây dựng thông báo
            $messages = [];
            if ($added > 0) {
                $messages[] = "Đã thêm $added biến thể mới.";
            }
            if (!empty($skipped)) {
                $messages[] = "Bỏ qua " . count($skipped) . " biến thể đã tồn tại: " . implode(', ', $skipped) . ".";
            }

            return redirect()->back()->with('success', implode(' ', $messages));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi thêm biến thể: ' . $e->getMessage());
        }
    }
}