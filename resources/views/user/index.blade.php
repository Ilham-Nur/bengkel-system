@extends('layout.app')

@section('title', 'Data User')

@section('content')
@php
    $role = request('role', 'admin');
    $rows = [
        ['nama' => 'Super Admin', 'hp' => '0812-0000-0001', 'role' => 'admin'],
        ['nama' => 'Budi Santoso', 'hp' => '0812-3456-7890', 'role' => 'pelanggan'],
        ['nama' => 'Rina Wijaya', 'hp' => '0812-7777-9000', 'role' => 'pelanggan'],
    ];
@endphp

@if ($role !== 'admin')
    <section class="panel" style="margin-top:1rem;">
        <div class="panel-head">
            <strong>Akses Ditolak</strong>
        </div>
        <div style="padding:1rem; color:var(--muted);">
            Halaman user hanya dapat diakses oleh admin.
        </div>
    </section>
@else
    <h1 class="page-title">Data User (Admin + Pelanggan)</h1>

    <section class="panel">
        <div class="panel-head">
            <strong>List User</strong>
            <button class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah User</button>
        </div>

        <div class="table-wrap desktop-only">
            <table>
                <thead>
                    <tr>
                        <th>Nama User</th>
                        <th>No Telepon</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $item)
                        <tr>
                            <td>{{ $item['nama'] }}</td>
                            <td>{{ $item['hp'] }}</td>
                            <td><span class="status {{ $item['role'] === 'admin' ? 'process' : 'draft' }}">{{ ucfirst($item['role']) }}</span></td>
                            <td>
                                <button class="btn btn-light"><i class="bi bi-eye"></i> Detail</button>
                                <button class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn btn-danger btn-delete"><i class="bi bi-trash3"></i> Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-list">
            @foreach ($rows as $item)
                <article class="info-card">
                    <h4>{{ $item['nama'] }}</h4>
                    <div class="kv"><span class="key">No Telepon</span><strong>{{ $item['hp'] }}</strong></div>
                    <div class="kv"><span class="key">Role</span><span class="status {{ $item['role'] === 'admin' ? 'process' : 'draft' }}">{{ ucfirst($item['role']) }}</span></div>
                    <div style="display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.6rem;">
                        <button class="btn btn-light"><i class="bi bi-eye"></i> Detail</button>
                        <button class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</button>
                        <button class="btn btn-danger btn-delete"><i class="bi bi-trash3"></i> Delete</button>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach((button) => {
        button.addEventListener('click', () => {
            Swal.fire({
                title: 'Hapus user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            });
        });
    });
</script>
@endpush
