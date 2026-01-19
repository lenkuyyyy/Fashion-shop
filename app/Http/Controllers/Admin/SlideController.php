<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use App\Models\News;
use App\Http\Requests\SlideRequest; // Giả sử bạn có file này để validate
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function index(Request $request)
    {
        $query = Slide::query(); // Bắt đầu query, không dùng withTrashed ở đây

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->input('status') !== null && $request->input('status') !== '') {
            $query->where('status', $request->input('status')); // Query trực tiếp 0 hoặc 1
        }

        $slides = $query->orderBy('order', 'asc')->paginate(10);
        $topSlides = Slide::orderByDesc('views')->take(5)->get();

        return view('admin.slides.index', compact('slides', 'topSlides'));
    }

    public function create()
    {
        $newsList = News::where('status', true)->get();
        return view('admin.slides.create', compact('newsList'));
    }

    public function store(SlideRequest $request)
    {
        $data = $request->validated(); // Lấy dữ liệu đã được validate

        if ($request->hasFile('image')) {
            // Xử lý lưu file một cách tường minh
            $data['image'] = $request->file('image')->store('slides', 'public');
        }

        Slide::create($data);

        return redirect()->route('admin.slides.index')->with('success', 'Slide đã được thêm thành công.');
    }

    public function edit(Slide $slide)
    {
        $newsList = News::where('status', true)->get();
        return view('admin.slides.edit', compact('slide', 'newsList'));
    }

    public function update(SlideRequest $request, Slide $slide)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            if ($slide->image && Storage::disk('public')->exists($slide->image)) {
                Storage::disk('public')->delete($slide->image);
            }
            // Lưu ảnh mới
            $data['image'] = $request->file('image')->store('slides', 'public');
        }

        $slide->update($data);

        return redirect()->route('admin.slides.index')->with('success', 'Slide đã được cập nhật thành công.');
    }

    public function destroy(Slide $slide)
    {
        $slide->delete();
        return redirect()->route('admin.slides.index')->with('success', 'Slide đã được chuyển vào thùng rác.');
    }

    public function toggleStatus(Slide $slide)
    {
        // Logic đơn giản hơn nhờ boolean casting trong Model
        $slide->status = !$slide->status;
        $slide->save();
        return redirect()->route('admin.slides.index')->with('success', 'Trạng thái slide đã được cập nhật.');
    }

    public function trashed()
    {
        $trashedSlides = Slide::onlyTrashed()->paginate(10);
        return view('admin.slides.trashed', compact('trashedSlides'));
    }

    public function restore($id)
    {
        $slide = Slide::withTrashed()->findOrFail($id);
        $slide->restore();
        return redirect()->route('admin.slides.index')->with('success', 'Slide đã được khôi phục.');
    }
}