<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function index(Request $request): View
    {
        $this->ensureAdmin();

        $users = User::query()->latest()->get();
        $editUser = null;

        if ($request->filled('edit')) {
            $editUser = User::findOrFail($request->integer('edit'));
        }

        return view('user.index', [
            'users' => $users,
            'editUser' => $editUser,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:'.User::ROLE_ADMIN.','.User::ROLE_PELANGGAN],
        ]);

        User::create($validated);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:'.User::ROLE_ADMIN.','.User::ROLE_PELANGGAN],
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        if ($user->is($request->user()) && ($validated['role'] ?? null) !== User::ROLE_ADMIN) {
            return back()->with('error', 'Akun admin yang sedang login tidak boleh diubah menjadi pelanggan.');
        }

        $user->update($validated);

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureAdmin();

        if ($user->is(request()->user())) {
            return back()->with('error', 'Anda tidak bisa menghapus akun yang sedang login.');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}
