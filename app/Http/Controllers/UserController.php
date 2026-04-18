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

        $query = trim((string) $request->string('q', ''));
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = (int) $request->input('per_page', 10);

        if (! in_array($perPage, [5, 10, 25], true)) {
            $perPage = 10;
        }

        $users = User::query()
            ->when($query !== '', function ($builder) use ($query): void {
                $builder->where(function ($nested) use ($query): void {
                    $nested->where('name', 'like', "%{$query}%")
                        ->orWhere('username', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when(filled($startDate), fn ($builder) => $builder->whereDate('created_at', '>=', $startDate))
            ->when(filled($endDate), fn ($builder) => $builder->whereDate('created_at', '<=', $endDate))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
        $editUser = null;

        if ($request->filled('edit')) {
            $editUser = User::findOrFail($request->integer('edit'));
        }

        return view('user.index', [
            'users' => $users,
            'editUser' => $editUser,
            'filters' => [
                'q' => $query,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'per_page' => $perPage,
            ],
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
