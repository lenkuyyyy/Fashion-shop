<?php

namespace App\Http\View\Composers;

use App\Models\Slide;
use Illuminate\View\View;

class SlideComposer
{
    public function compose(View $view)
    {
        $slides = Slide::where('status', true)
                       ->orderBy('order', 'asc')
                       ->get();
        
        $view->with('slides', $slides);
    }
}