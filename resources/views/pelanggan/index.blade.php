@extends('layout.app')

@section('title', 'Data Pelanggan')

@section('content')
@php
    $role = request('role', 'admin');
    $rows = [
        ['nama' => 'Budi Santoso', 'hp' => '0812-3456-7890'],
        ['nama' => 'Rina Wijaya', 'hp' => '0812-7777-9000'],
        ['nama' => 'Andi Pratama', 'hp' => '0856-1111-2020'],
    ];
@endphp

<h1 class="page-title">Data Pelanggan</h1>

<section class="panel">
    <div class="panel-head">
        <strong>List Pelanggan</strong>
        @if ($role === 'admin')
            <button class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah</button>
        @endif
    </div>

    <div class="table-wrap desktop-only">
        <table>
            <thead>
                <tr>
                    <th>Nama Customer</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $item)
                    <tr>
                        <td>{{ $item['nama'] }}</td>
                        <td>{{ $item['hp'] }}</td>
                        <td>
                            @if ($role === 'admin')
                                <button class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</button>
                                <button class="btn btn-danger btn-delete"><i class="bi bi-trash3"></i> Delete</button>
                            @else
                                <span style="color:var(--muted)">View Only</span>
                            @endif
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
                <div class="kv"><span class="key">No HP</span><strong>{{ $item['hp'] }}</strong></div>
                @if ($role === 'admin')
                    <div style="display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.6rem;">
                        <button class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</button>
                        <button class="btn btn-danger btn-delete"><i class="bi bi-trash3"></i> Delete</button>
                    </div>
                @endif
            </article>
        @endforeach
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach((button) => {
        button.addEventListener('click', () => {
            Swal.fire({
                title: 'Hapus data pelanggan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            });
        });
    });
</script>
@endpush
