<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $theme = config('theme');

        // Mark 'About' active in navigation for this page
        if (isset($theme['navigation'])) {
            foreach ($theme['navigation'] as &$item) {
                $item['active'] = ($item['label'] === 'About');
            }
        }

        return view('about', [
            'theme' => $theme,
        ]);
    }
}

