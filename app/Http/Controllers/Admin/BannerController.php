<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banner = Banner::first();
        return view('admin.banners.index', compact('banner'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'show_text' => 'required|in:0,1',
        ]);

        $banner = Banner::first();

        if (!$banner && !$request->hasFile('image_1') && !$request->hasFile('image_2') && !$request->hasFile('image_3')) {
            return redirect()->back()->withErrors(['image_1' => 'Phải upload ít nhất một ảnh banner.']);
        }

        $data = [
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'show_text' => $request->input('show_text') === '1',
        ];

        if (!$banner) {
            $data['image_path_1'] = $request->hasFile('image_1') ? $request->file('image_1')->store('banners', 'public') : 'default.jpg';
            $data['image_path_2'] = $request->hasFile('image_2') ? $request->file('image_2')->store('banners', 'public') : 'default.jpg';
            $data['image_path_3'] = $request->hasFile('image_3') ? $request->file('image_3')->store('banners', 'public') : 'default.jpg';
            $banner = Banner::create($data);
        } else {
            if ($request->hasFile('image_1')) {
                if ($banner->image_path_1 && $banner->image_path_1 !== 'default.jpg') {
                    Storage::delete('public/' . $banner->image_path_1);
                }
                $data['image_path_1'] = $request->file('image_1')->store('banners', 'public');
            }
            if ($request->hasFile('image_2')) {
                if ($banner->image_path_2 && $banner->image_path_2 !== 'default.jpg') {
                    Storage::delete('public/' . $banner->image_path_2);
                }
                $data['image_path_2'] = $request->file('image_2')->store('banners', 'public');
            }
            if ($request->hasFile('image_3')) {
                if ($banner->image_path_3 && $banner->image_path_3 !== 'default.jpg') {
                    Storage::delete('public/' . $banner->image_path_3);
                }
                $data['image_path_3'] = $request->file('image_3')->store('banners', 'public');
            }
            $banner->update($data);
        }

        return redirect()->route('admin.admin.banners.index')->with('success', 'Cập nhật banner thành công!');
    }
}