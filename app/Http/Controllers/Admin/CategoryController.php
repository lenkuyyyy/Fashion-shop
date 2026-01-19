<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\CategoryGroup;
class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::query();
        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }
        $categories = $query->orderBy('created_at', 'desc')->paginate(10);

        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.categories.categories', compact('categories', 'topCategories'));
    }

    public function create(): View
    {
        $groups = CategoryGroup::all();
        return view('admin.categories.create', compact('groups'));
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status,
            'group_id' => $request->group_id, 
            // đây đủ thông tin nhóm danh mục và có thể là null nếu không chọn nhóm
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được thêm thành công!');
    }

    public function show(Category $category): View
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        $groups = CategoryGroup::all();
        return view('admin.categories.edit', compact('category', 'groups'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status,
            'group_id' => $request->group_id,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Nếu còn sản phẩm liên kết với danh mục thì không cho xóa và thông báo ra màn hình
        if ($category->products()->exists()) {
            return redirect()->route('admin.categories.index')
            ->with('error', 'Không thể xóa danh mục vì vẫn còn sản phẩm liên kết!');
        }

        $category->delete(); // Xóa mềm
        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được xóa thành công!');
    }

    public function trashed(Request $request): View
    {
        $query = Category::onlyTrashed();
        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        $categories = $query->orderBy('deleted_at', 'desc')->paginate(10);

        return view('admin.categories.trashed', compact('categories'));
    }

    public function restore($id): RedirectResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('admin.categories.trashed')->with('success', 'Danh mục đã được khôi phục thành công!');
    }

    public function forceDelete($id): RedirectResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        if ($category->products()->exists()) {
            return redirect()->route('admin.categories.trashed')->with('error', 'Không thể xóa vĩnh viễn danh mục vì vẫn còn sản phẩm liên kết!');
        }

        $category->forceDelete();
        return redirect()->route('admin.categories.trashed')->with('success', 'Danh mục đã được xóa vĩnh viễn!');
    }
}
