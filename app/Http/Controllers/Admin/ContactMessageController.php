<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $f = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:read,unread']]);
        $q = ContactMessage::query();
        if (! empty($f['q'])) {
            $q->where(fn ($q) => $q->where('name', 'like', '%'.$f['q'].'%')->orWhere('email', 'like', '%'.$f['q'].'%')->orWhere('subject', 'like', '%'.$f['q'].'%'));
        }if (! empty($f['status'])) {
            $f['status'] === 'unread' ? $q->whereNull('read_at') : $q->whereNotNull('read_at');
        }

        return view('admin.messages.index', ['title' => 'Contact Messages', 'messages' => $q->latest()->orderByDesc('id')->paginate(20)->withQueryString()]);
    }

    public function show(ContactMessage $message): View
    {
        return view('admin.messages.show', ['title' => 'Contact Message', 'message' => $message]);
    }

    public function update(Request $request, ContactMessage $message): RedirectResponse
    {
        $data = $request->validate(['is_read' => ['required', 'boolean']]);
        $message->update(['read_at' => $data['is_read'] ? now() : null]);

        return back()->with('status', 'Message status updated.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('status', 'Message deleted.');
    }
}
