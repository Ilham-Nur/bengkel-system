@extends('layout.app')

@section('title', 'Data User')

@section('content')
    <h1 class="page-title">Data User</h1>

    <section class="panel" style="margin-bottom:1rem;">
        <div class="panel-head">
            <strong>{{ $editUser ? 'Edit User' : 'Tambah User' }}</strong>
        </div>

        <div style="padding:1rem;">
            <form method="POST" action="{{ $editUser ? route('user.update', $editUser) : route('user.store') }}">
                @csrf
                @if ($editUser)
                    @method('PUT')
                @endif

                <div class="form-grid">
                    <div>
                        <label for="name">Nama</label>
                        <input class="input" id="name" name="name" value="{{ old('name', $editUser?->name) }}" required>
                    </div>
                    <div>
                        <label for="username">Username</label>
                        <input class="input" id="username" name="username" value="{{ old('username', $editUser?->username) }}" required>
                    </div>
                    <div>
                        <label for="email">Email</label>
                        <input class="input" id="email" name="email" type="email" value="{{ old('email', $editUser?->email) }}" required>
                    </div>
                    <div>
                        <label for="no_hp">No HP</label>
                        <input class="input" id="no_hp" name="no_hp" value="{{ old('no_hp', $editUser?->no_hp) }}" placeholder="Contoh: 08123456789">
                    </div>
                    <div>
                        <label for="role">Role</label>
                        <select class="input" id="role" name="role" required>
                            <option value="admin" @selected(old('role', $editUser?->role) === 'admin')>Admin</option>
                            <option value="pelanggan" @selected(old('role', $editUser?->role) === 'pelanggan')>Pelanggan</option>
                        </select>
                    </div>
                    <div class="full">
                        <label for="password">{{ $editUser ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}</label>
                        <input class="input" id="password" name="password" type="password" {{ $editUser ? '' : 'required' }}>
                    </div>
                </div>

                <div class="form-action">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi {{ $editUser ? 'bi-save' : 'bi-person-plus' }}"></i>
                        {{ $editUser ? 'Update User' : 'Tambah User' }}
                    </button>
                    @if ($editUser)
                        <a href="{{ route('user.index') }}" class="btn btn-light"><i class="bi bi-x-circle"></i> Batal Edit</a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <strong>List User</strong>
        </div>

        <div class="filter-wrap">
            <form method="GET" action="{{ route('user.index') }}" class="search-inline">
                <div>
                    <label for="q">Pencarian</label>
                    <input class="input" id="q" name="q" value="{{ $filters['q'] }}" placeholder="Nama, username, email, no hp">
                </div>
                <input type="hidden" name="start_date" value="{{ $filters['start_date'] }}">
                <input type="hidden" name="end_date" value="{{ $filters['end_date'] }}">
                <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
                <div class="filter-actions-inline">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                    <button class="btn btn-light" type="button" data-open-filter><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('user.index') }}" class="btn btn-light"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                </div>
            </form>
        </div>

        <div class="filter-modal" data-filter-modal hidden>
            <div class="filter-modal-backdrop" data-close-filter></div>
            <div class="filter-modal-card">
                <div class="filter-modal-head">
                    <strong>Filter User</strong>
                    <button type="button" class="btn btn-light" data-close-filter><i class="bi bi-x-lg"></i></button>
                </div>
                <form method="GET" action="{{ route('user.index') }}" class="filter-grid">
                    <input type="hidden" name="q" value="{{ $filters['q'] }}">
                    <div>
                        <label for="start_date_modal">Dari Tanggal</label>
                        <input class="input" id="start_date_modal" name="start_date" type="date" value="{{ $filters['start_date'] }}">
                    </div>
                    <div>
                        <label for="end_date_modal">Sampai Tanggal</label>
                        <input class="input" id="end_date_modal" name="end_date" type="date" value="{{ $filters['end_date'] }}">
                    </div>
                    <div>
                        <label for="per_page_modal">Data / Halaman</label>
                        <select class="input" id="per_page_modal" name="per_page">
                            @foreach ([5, 10, 25] as $limit)
                                <option value="{{ $limit }}" @selected((int) $filters['per_page'] === $limit)>{{ $limit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Terapkan</button>
                        <a href="{{ route('user.index', ['q' => $filters['q']]) }}" class="btn btn-light">Reset Filter</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-wrap desktop-only">
            <table>
                <thead>
                    <tr>
                        <th>Nama User</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->username }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->no_hp ?: '-' }}</td>
                            <td><span class="status {{ $item->role === 'admin' ? 'process' : 'draft' }}">{{ ucfirst($item->role) }}</span></td>
                            <td>
                                <a href="{{ route('user.index', ['edit' => $item->id]) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                <form action="{{ route('user.destroy', $item) }}" method="POST" style="display:inline-block;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit"><i class="bi bi-trash3"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:var(--muted);">Belum ada data user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-list">
            @forelse ($users as $item)
                <article class="info-card">
                    <h4>{{ $item->name }}</h4>
                    <div class="kv"><span class="key">Username</span><strong>{{ $item->username }}</strong></div>
                    <div class="kv"><span class="key">Email</span><strong>{{ $item->email }}</strong></div>
                    <div class="kv"><span class="key">No HP</span><strong>{{ $item->no_hp ?: '-' }}</strong></div>
                    <div class="kv"><span class="key">Role</span><span class="status {{ $item->role === 'admin' ? 'process' : 'draft' }}">{{ ucfirst($item->role) }}</span></div>
                    <div style="display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.6rem;">
                        <a href="{{ route('user.index', ['edit' => $item->id]) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('user.destroy', $item) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit"><i class="bi bi-trash3"></i> Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <article class="info-card" style="color:var(--muted);">Belum ada data user.</article>
            @endforelse
        </div>

        <div class="pagination-wrap">
            {{ $users->links() }}
        </div>
    </section>
@endsection

@push('scripts')
<style>
    .filter-wrap { padding: 1rem; border-bottom: var(--border); }
    .search-inline { display:grid; gap:.8rem; grid-template-columns: 1fr; }
    .filter-actions-inline { display:flex; gap:.5rem; flex-wrap:wrap; align-items:end; }
    .filter-modal { position: fixed; inset: 0; z-index: 40; display:flex; align-items:center; justify-content:center; padding: 1rem; }
    .filter-modal-backdrop { position:absolute; inset:0; background:rgba(15, 23, 42, .42); }
    .filter-modal-card { position:relative; width:min(560px, 100%); background:#fff; border-radius:14px; box-shadow: var(--shadow); padding:1rem; }
    .filter-modal-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:.8rem; }
    .filter-grid { display:grid; gap:.8rem; }
    .filter-actions { display:flex; gap:.5rem; flex-wrap:wrap; }
    .pagination-wrap { padding: .9rem 1rem 1rem; }
    @media (min-width: 768px) {
        .search-inline { grid-template-columns: minmax(0, 1fr) auto; align-items:end; }
    }
</style>
<script>
    const errorMessages = @json($errors->all());
    const successMessage = @json(session('success'));
    const errorMessage = @json(session('error'));

    if (errorMessages.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Validasi gagal',
            html: errorMessages.map((msg) => `<div>${msg}</div>`).join(''),
        });
    }

    if (successMessage) {
        Swal.fire('Berhasil', successMessage, 'success');
    }

    if (errorMessage) {
        Swal.fire('Gagal', errorMessage, 'error');
    }


    const modal = document.querySelector('[data-filter-modal]');
    document.querySelectorAll('[data-open-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            modal?.removeAttribute('hidden');
        });
    });
    document.querySelectorAll('[data-close-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            modal?.setAttribute('hidden', 'hidden');
        });
    });

    document.querySelectorAll('.delete-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            Swal.fire({
                title: 'Hapus user?',
                text: 'Data user akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
