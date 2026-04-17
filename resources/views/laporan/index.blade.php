@extends('layout.app')

@section('title', 'Laporan Pekerjaan / List Job')

@section('content')
@php
    $role = auth()->user()?->role ?? 'pelanggan';
@endphp

<h1 class="page-title">Laporan Pekerjaan / List Job</h1>
<div class="role-box">Role aktif: <strong>{{ ucfirst($role) }}</strong></div>

<section class="panel">
    <div class="panel-head">
        <strong>Daftar Work Order untuk Laporan</strong>
    </div>

    <div class="table-wrap desktop-only">
        <table>
            <thead>
                <tr>
                    <th>No WO</th>
                    <th>Nama Customer</th>
                    <th>Tanggal WO</th>
                    <th>Plat Nomor</th>
                    <th>Jenis Motor</th>
                    <th>Status Laporan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($workOrders as $item)
                    <tr>
                        <td>{{ $item->no_wo }}</td>
                        <td>{{ $item->customer?->name ?? '-' }}</td>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->plat_nomor }}</td>
                        <td>{{ $item->jenis_motor }}</td>
                        <td>
                            @if ($item->serviceReport)
                                <span class="status done">Sudah diisi</span>
                            @else
                                <span class="status draft">Belum diisi</span>
                            @endif
                        </td>
                        <td>
                            @if ($role === 'admin')
                                <a href="{{ route('laporan.form', $item) }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Laporan</a>
                            @else
                                <span class="status process">View only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:#64748b;">Belum ada data work order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-list">
        @forelse ($workOrders as $item)
            <article class="info-card">
                <h4>{{ $item->no_wo }}</h4>
                <div class="kv"><span class="key">Customer</span><strong>{{ $item->customer?->name ?? '-' }}</strong></div>
                <div class="kv"><span class="key">Tanggal WO</span><span>{{ $item->tanggal }}</span></div>
                <div class="kv"><span class="key">Plat</span><span>{{ $item->plat_nomor }}</span></div>
                <div class="kv"><span class="key">Motor</span><span>{{ $item->jenis_motor }}</span></div>
                <div style="margin-top:.6rem;">
                    @if ($item->serviceReport)
                        <span class="status done">Sudah diisi</span>
                    @else
                        <span class="status draft">Belum diisi</span>
                    @endif
                </div>

                @if ($role === 'admin')
                    <div style="display:flex; gap:.45rem; flex-wrap:wrap; margin-top:.65rem;">
                        <a href="{{ route('laporan.form', $item) }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Laporan</a>
                    </div>
                @endif
            </article>
        @empty
            <article class="info-card" style="text-align:center; color:#64748b;">Belum ada data work order.</article>
        @endforelse
    </div>

    <div class="pagination-wrap">
        {{ $workOrders->links() }}
    </div>
</section>
@endsection

@push('scripts')
<style>
    .pagination-wrap { padding: .9rem 1rem 1rem; }
</style>
@endpush
