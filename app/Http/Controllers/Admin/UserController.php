<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $f = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'role' => ['nullable', 'in:admin,user'], 'status' => ['nullable', 'in:active,inactive']]);
        $q = User::query();
        if (! empty($f['q'])) {
            $q->where(fn ($q) => $q->where('name', 'like', '%'.$f['q'].'%')->orWhere('email', 'like', '%'.$f['q'].'%'));
        }if (! empty($f['role'])) {
            $q->where('is_admin', $f['role'] === 'admin');
        }if (! empty($f['status'])) {
            $q->where('is_active', $f['status'] === 'active');
        }

        return view('admin.users.index', ['title' => 'User Management', 'users' => $q->orderBy('name')->orderBy('id')->paginate(20)->withQueryString()]);
    }

    public function create(): View
    {
        return $this->form(new User);
    }

    public function edit(User $user): View
    {
        return $this->form($user);
    }

    private function form(User $user): View
    {
        return view('admin.users.form', ['title' => 'User Management', 'account' => $user]);
    }

    public function store(SaveUserRequest $request): RedirectResponse
    {
        $u = new User;
        $u->forceFill($request->validated())->save();

        return redirect()->route('admin.users.index')->with('status', 'Account created.');
    }

    public function update(SaveUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        DB::transaction(function () use ($user, $data): void {
            $admins = User::where('is_admin', true)->orderBy('id')->lockForUpdate()->get();
            $current = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($current->is_admin && $current->is_active && (! $data['is_admin'] || ! $data['is_active']) && $admins->where('is_active', true)->count() <= 1) {
                throw ValidationException::withMessages(['account' => 'The last active administrator cannot be deactivated or demoted.']);
            }
            if (isset($data['password']) || ! $data['is_active']) {
                $data['remember_token'] = Str::random(60);
            }$current->forceFill($data)->save();
        });
        if ($request->user()->id === $user->id && (isset($data['password']) || ! $data['is_admin'] || ! $data['is_active'])) {
            return $this->signOut($request);
        }

        return redirect()->route('admin.users.index')->with('status', 'Account updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        DB::transaction(function () use ($user): void {
            $admins = User::where('is_admin', true)->orderBy('id')->lockForUpdate()->get();
            $current = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($current->is_admin && $current->is_active && $admins->where('is_active', true)->count() <= 1) {
                throw ValidationException::withMessages(['account' => 'The last active administrator cannot be deleted.']);
            }$current->delete();
        });
        if ($request->user()->id === $user->id) {
            return $this->signOut($request);
        }

        return redirect()->route('admin.users.index')->with('status', 'Account deleted.');
    }

    private function signOut(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'Your account changed. Please sign in again if eligible.');
    }
}
