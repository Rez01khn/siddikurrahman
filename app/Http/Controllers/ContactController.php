<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create($request->safe()->except('website'));

        return redirect()->to(route('home').'#contact-section')
            ->with('status', 'Thank you. Your message has been received.');
    }
}
