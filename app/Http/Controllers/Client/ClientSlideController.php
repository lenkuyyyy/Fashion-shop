<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;

class ClientSlideController extends Controller
{
    public function index()
    {
        // Sửa lại query để dùng boolean `true` thay vì chuỗi 'active'
        $slides = Slide::where('status', true)
            ->orderBy('order', 'asc')
            ->get();

        return view('client.layouts.partials.banner-left', compact('slides'));
    }
}