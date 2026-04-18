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
            <button class="btn btn-light" type="button" id="open-user-filter"><i class="bi bi-funnel"></i> Filter</button>
        </div>

        <div class="table-wrap desktop-only">
            <table>
                <thead>
                    <tr>
                        <th>Nama User</th>
                        <th>Username</th>
                        <th>Email</th>
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
                            <td colspan="5" style="text-align:center; color:var(--muted);">Belum ada data user.</td>
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
    .pagination-wrap { padding: .9rem 1rem 1rem; }
    .modal-filter-grid { display:grid; gap:.7rem; text-align:left; }
    .modal-filter-grid label { margin:0; }
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

    document.getElementById('open-user-filter')?.addEventListener('click', () => {
        Swal.fire({
            title: 'Filter Data User',
            html: `
                <div class="modal-filter-grid">
                    <div>
                        <label for="modal-q">Pencarian</label>
                        <input class="input" id="modal-q" placeholder="Nama, username, email" value="{{ e($filters['q']) }}">
                    </div>
                    <div>
                        <label for="modal-start-date">Dari Tanggal</label>
                        <input class="input" id="modal-start-date" type="date" value="{{ $filters['start_date'] }}">
                    </div>
                    <div>
                        <label for="modal-end-date">Sampai Tanggal</label>
                        <input class="input" id="modal-end-date" type="date" value="{{ $filters['end_date'] }}">
                    </div>
                    <div>
                        <label for="modal-per-page">Data / Halaman</label>
                        <select class="input" id="modal-per-page">
                            <option value="5" {{ (int) $filters['per_page'] === 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ (int) $filters['per_page'] === 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ (int) $filters['per_page'] === 25 ? 'selected' : '' }}>25</option>
                        </select>
                    </div>
                </div>
            `,
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Terapkan',
            denyButtonText: 'Reset',
            cancelButtonText: 'Tutup',
            preConfirm: () => {
                const params = new URLSearchParams();
                const q = document.getElementById('modal-q')?.value.trim();
                const startDate = document.getElementById('modal-start-date')?.value;
                const endDate = document.getElementById('modal-end-date')?.value;
                const perPage = document.getElementById('modal-per-page')?.value;

                if (q) params.set('q', q);
                if (startDate) params.set('start_date', startDate);
                if (endDate) params.set('end_date', endDate);
                if (perPage) params.set('per_page', perPage);

                window.location.href = `{{ route('user.index') }}${params.toString() ? `?${params.toString()}` : ''}`;
            },
        }).then((result) => {
            if (result.isDenied) {
                window.location.href = '{{ route('user.index') }}';
            }
        });
    });
</script>
@endpush
