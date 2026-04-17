@extends('layout.app')

@section('title', 'Work Order')

@section('content')
@php
    $role = request('role', 'admin');
    $rows = [
        ['no' => 'WO-2026-0001', 'nama' => 'Budi Santoso', 'plat' => 'B 1234 XYZ', 'tgl' => '2026-04-16', 'status' => 'process'],
        ['no' => 'WO-2026-0002', 'nama' => 'Rina Wijaya', 'plat' => 'D 8888 KM', 'tgl' => '2026-04-17', 'status' => 'draft'],
        ['no' => 'WO-2026-0003', 'nama' => 'Andi Pratama', 'plat' => 'F 7771 AQ', 'tgl' => '2026-04-17', 'status' => 'done'],
    ];
@endphp

<h1 class="page-title">Work Order</h1>
<div class="role-box">Role aktif: <strong>{{ ucfirst($role) }}</strong> (akses action berbeda antara Admin & Customer).</div>

<section class="panel">
    <div class="panel-head">
        <strong>List Work Order</strong>
        @if ($role === 'admin')
            <a href="{{ route('workorder.create', ['role' => $role]) }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Work Order</a>
        @endif
    </div>

    <div class="table-wrap desktop-only">
        <table>
            <thead>
                <tr>
                    <th>No Check</th>
                    <th>Nama Customer</th>
                    <th>Plat Nomor</th>
                    <th>Tanggal Check</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $item)
                    <tr>
                        <td>{{ $item['no'] }}</td>
                        <td>{{ $item['nama'] }}</td>
                        <td>{{ $item['plat'] }}</td>
                        <td>{{ $item['tgl'] }}</td>
                        <td><span class="status {{ $item['status'] }}">{{ ucfirst($item['status']) }}</span></td>
                        <td>
                            <a href="#" class="btn btn-light"><i class="bi bi-eye"></i> Detail</a>
                            @if ($role === 'admin')
                                <a href="{{ route('workorder.create', ['mode' => 'edit', 'role' => $role]) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                <button class="btn btn-danger btn-delete"><i class="bi bi-trash3"></i> Delete</button>
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
                <h4>{{ $item['no'] }}</h4>
                <div class="kv"><span class="key">Customer</span><strong>{{ $item['nama'] }}</strong></div>
                <div class="kv"><span class="key">Plat</span><span>{{ $item['plat'] }}</span></div>
                <div class="kv"><span class="key">Tanggal</span><span>{{ $item['tgl'] }}</span></div>
                <div class="kv"><span class="key">Status</span><span class="status {{ $item['status'] }}">{{ ucfirst($item['status']) }}</span></div>
                <div style="display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.6rem;">
                    <a href="#" class="btn btn-light"><i class="bi bi-eye"></i> Detail</a>
                    @if ($role === 'admin')
                        <a href="{{ route('workorder.create', ['mode' => 'edit', 'role' => $role]) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <button class="btn btn-danger btn-delete"><i class="bi bi-trash3"></i> Delete</button>
                    @endif
                </div>
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
                title: 'Hapus Work Order?',
                text: 'Data yang dihapus tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            });
        });
    });
</script>
@endpush
