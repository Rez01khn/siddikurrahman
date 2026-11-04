<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact', ['sections' => collect(), 'visibleAnchors' => ['top'], 'solidHeader' => true]);
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create($request->safe()->except('website'));

        return redirect()->route('contact')->with('status', 'Thank you. Your message has been received.');
    }
}
