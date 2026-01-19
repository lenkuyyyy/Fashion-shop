<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Http\Requests\NewsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status'); // 'active', 'inactive', hoặc ''

        // Bắt đầu query
        $query = News::query();

        // Lọc theo từ khóa tìm kiếm
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($statusFilter !== null && $statusFilter !== '') {
            $statusValue = $statusFilter === 'active' ? 1 : 0;
            $query->where('status', $statusValue);
        }

        $news = $query->latest('published_at')->paginate(10);
        $topNews = News::orderBy('views', 'desc')->take(5)->get();

        return view('admin.news.index', compact('news', 'topNews'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(NewsRequest $request)
    {
        // Lấy dữ liệu đã được xác thực và xử lý từ NewsRequest
        $data = $request->validated();

        // Xử lý upload file ảnh nếu có
        if ($request->hasFile('image')) {
            // Laravel tự tạo tên file duy nhất và lưu vào storage/app/public/news
            $path = $request->file('image')->store('news', 'public');
            $data['image'] = $path;
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Bài viết đã được thêm thành công.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(NewsRequest $request, News $news)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }
            // Tải lên ảnh mới và lấy đường dẫn
            $data['image'] = $request->file('image')->store('news', 'public');
        }
        // *** Quan trọng: Nếu không có file mới, không cần làm gì cả.
        // Dữ liệu 'image' cũ sẽ được giữ nguyên.

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Bài viết đã được cập nhật thành công.');
    }

    public function destroy(News $news)
    {
        $news->delete(); // Xóa mềm
        return redirect()->route('admin.news.index')->with('success', 'Bài viết đã được chuyển vào thùng rác.');
    }

    public function toggleStatus(News $news)
    {
        // Logic đơn giản hơn nhờ có 'boolean' cast trong Model
        $news->status = !$news->status;
        $news->save();
        return redirect()->route('admin.news.index')->with('success', 'Trạng thái bài viết đã được cập nhật.');
    }

    public function trashed()
    {
        $trashedNews = News::onlyTrashed()->latest()->paginate(10);
        return view('admin.news.trashed', compact('trashedNews'));
    }

    public function restore($id)
    {
        $news = News::onlyTrashed()->findOrFail($id);
        $news->restore();
        return redirect()->route('admin.news.trashed')->with('success', 'Bài viết đã được khôi phục thành công.');
    }
}