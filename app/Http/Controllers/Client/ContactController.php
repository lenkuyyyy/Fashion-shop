<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactNotification;

class ContactController extends Controller
{
    public function show()
    {
        return view('client.pages.contact');
    }

        public function send(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|min:5',
        ]);

        // Lưu vào DB
        \App\Models\Contact::create($data);

        // Gửi mail (nếu vẫn muốn)
        Mail::to('lesang0905000@gmail.com')->send(new ContactNotification($data));

        return back()->with('success', 'Bạn đã gửi liên hệ thành công!');
    }

}


