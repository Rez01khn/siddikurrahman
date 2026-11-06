<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $loginInput = $request->input('email');

        $user = User::where(function ($query) use ($loginInput): void {
            $query->where('email', $loginInput)->orWhere('name', $loginInput);
        })->where('is_admin', true)->where('is_active', true)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match an administrator account.',
            ]);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'You have been signed out securely.');
    }
}
