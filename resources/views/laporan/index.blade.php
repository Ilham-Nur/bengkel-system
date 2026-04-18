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

    <div class="filter-wrap">
        <form method="GET" action="{{ route('laporan.index') }}" class="filter-grid">
            <div>
                <label for="q">Pencarian</label>
                <input class="input" id="q" name="q" value="{{ $filters['q'] }}" placeholder="No WO, customer, plat, motor">
            </div>
            <div>
                <label for="start_date">Dari Tanggal WO</label>
                <input class="input" id="start_date" name="start_date" type="date" value="{{ $filters['start_date'] }}">
            </div>
            <div>
                <label for="end_date">Sampai Tanggal WO</label>
                <input class="input" id="end_date" name="end_date" type="date" value="{{ $filters['end_date'] }}">
            </div>
            <div>
                <label for="per_page">Data / Halaman</label>
                <select class="input" id="per_page" name="per_page">
                    @foreach ([5, 10, 25] as $limit)
                        <option value="{{ $limit }}" @selected((int) $filters['per_page'] === $limit)>{{ $limit }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-actions full">
                <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Terapkan</button>
                <a href="{{ route('laporan.index') }}" class="btn btn-light"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
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
                    <th>Progress</th>
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
                        @php
                            $totalKeluhan = $item->complaintItems->count();
                            $selesaiKeluhan = $item->serviceReport?->items?->count() ?? 0;
                            $rawPercent = $totalKeluhan > 0 ? ($selesaiKeluhan / $totalKeluhan) * 100 : 0;
                            $progressPercent = $selesaiKeluhan > 0 ? min(100, (int) (ceil($rawPercent / 25) * 25)) : 0;
                        @endphp
                        <td>
                            <div class="progress-wrap" title="{{ $selesaiKeluhan }} dari {{ $totalKeluhan }} keluhan selesai">
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $progressPercent }}%;"></div>
                                </div>
                                <small>{{ $progressPercent }}% ({{ $selesaiKeluhan }}/{{ $totalKeluhan }})</small>
                            </div>
                        </td>
                        <td>
                            @if ($progressPercent === 100)
                                <span class="status done">Selesai</span>
                            @elseif ($selesaiKeluhan > 0)
                                <span class="status process">Proses</span>
                            @else
                                <span class="status draft">Belum dikerjakan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('laporan.show', $item) }}" class="btn btn-light"><i class="bi bi-eye"></i> Detail</a>
                            @if ($role === 'admin')
                                <a href="{{ route('laporan.form', $item) }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Laporan</a>
                                @if ($item->kwitansi)
                                    <a href="{{ route('kwitansi.index', ['q' => $item->kwitansi->no_invoice]) }}" class="btn btn-success"><i class="bi bi-receipt"></i> Sudah Jadi Kwitansi</a>
                                @else
                                    <a href="{{ route('kwitansi.create', ['work_order_id' => $item->id]) }}" class="btn btn-warning"><i class="bi bi-receipt"></i> Convert ke Kwitansi</a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; color:#64748b;">Belum ada data work order.</td>
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
                @php
                    $totalKeluhan = $item->complaintItems->count();
                    $selesaiKeluhan = $item->serviceReport?->items?->count() ?? 0;
                    $rawPercent = $totalKeluhan > 0 ? ($selesaiKeluhan / $totalKeluhan) * 100 : 0;
                    $progressPercent = $selesaiKeluhan > 0 ? min(100, (int) (ceil($rawPercent / 25) * 25)) : 0;
                @endphp
                <div class="progress-wrap" style="margin-top:.5rem;" title="{{ $selesaiKeluhan }} dari {{ $totalKeluhan }} keluhan selesai">
                    <div class="progress-track">
                        <div class="progress-fill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                    <small>{{ $progressPercent }}% ({{ $selesaiKeluhan }}/{{ $totalKeluhan }})</small>
                </div>
                <div style="margin-top:.6rem;">
                    @if ($progressPercent === 100)
                        <span class="status done">Selesai</span>
                    @elseif ($selesaiKeluhan > 0)
                        <span class="status process">Proses</span>
                    @else
                        <span class="status draft">Belum dikerjakan</span>
                    @endif
                </div>

                <div style="display:flex; gap:.45rem; flex-wrap:wrap; margin-top:.65rem;">
                    <a href="{{ route('laporan.show', $item) }}" class="btn btn-light"><i class="bi bi-eye"></i> Detail</a>
                    @if ($role === 'admin')
                        <a href="{{ route('laporan.form', $item) }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Laporan</a>
                        @if ($item->kwitansi)
                            <a href="{{ route('kwitansi.index', ['q' => $item->kwitansi->no_invoice]) }}" class="btn btn-success"><i class="bi bi-receipt"></i> Sudah Jadi Kwitansi</a>
                        @else
                            <a href="{{ route('kwitansi.create', ['work_order_id' => $item->id]) }}" class="btn btn-warning"><i class="bi bi-receipt"></i> Convert ke Kwitansi</a>
                        @endif
                    @endif
                </div>
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
    .filter-wrap { padding: 1rem; border-bottom: var(--border); }
    .filter-grid { display:grid; gap:.8rem; grid-template-columns: repeat(1, minmax(0, 1fr)); }
    .filter-actions { display:flex; gap:.5rem; flex-wrap:wrap; align-items:end; }
    .pagination-wrap { padding: .9rem 1rem 1rem; }
    .progress-wrap { min-width: 150px; }
    .progress-track { width: 100%; height: 9px; background: #e2e8f0; border-radius: 999px; overflow: hidden; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, #2563eb, #10b981); border-radius: 999px; transition: width .25s ease; }
    .progress-wrap small { display:block; margin-top:.3rem; color:#64748b; font-size:.75rem; }
    @media (min-width: 768px) {
        .filter-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
</style>
@endpush
