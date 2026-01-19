<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class ClientNewsController extends Controller
{
    public function fashionNewsletters()
    {
        // Lấy 3 bài viết có lượt xem cao nhất, đang hoạt động và đã được đăng
        $topNews = News::where('status', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('views')
            ->take(3)
            ->get();
        
        // SỬA Ở ĐÂY: Dùng paginate() thay vì get() để có thể phân trang
        $latestNews = News::where('status', true)
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(6); // Lấy 6 bài viết mỗi trang

        return view('client.pages.fashion-newsletters', compact('topNews', 'latestNews'));
    }

    public function show($id)
    {
        // Chỉ tìm bài viết đang hoạt động và đã được đăng
        $news = News::where('status', true)
            ->where('published_at', '<=', now())
            ->findOrFail($id);
            
        // Tăng lượt xem
        $news->increment('views');
        
        return view('client.pages.news-detail', compact('news'));
    }
}