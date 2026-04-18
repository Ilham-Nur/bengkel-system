@extends('layout.app')

@section('title', 'Kwitansi')

@section('content')
@php
    $role = auth()->user()?->role ?? 'pelanggan';
@endphp

<h1 class="page-title">Kwitansi</h1>

<section class="panel">
    <div class="panel-head">
        <strong>List Invoice</strong>
        @if ($role === 'admin')
            <a href="{{ route('kwitansi.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Buat Invoice</a>
        @endif
    </div>

    <div class="filter-wrap">
        <form method="GET" action="{{ route('kwitansi.index') }}" class="filter-grid">
            <div>
                <label for="q">Pencarian</label>
                <input class="input" id="q" name="q" value="{{ $filters['q'] }}" placeholder="No invoice, nama, plat, no wo">
            </div>
            <div>
                <label for="start_date">Dari Tanggal</label>
                <input class="input" id="start_date" name="start_date" type="date" value="{{ $filters['start_date'] }}">
            </div>
            <div>
                <label for="end_date">Sampai Tanggal</label>
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
                <a href="{{ route('kwitansi.index') }}" class="btn btn-light"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="table-wrap desktop-only">
        <table>
            <thead>
                <tr>
                    <th>No Invoice</th>
                    <th>No WO</th>
                    <th>Nama Customer</th>
                    <th>Tanggal</th>
                    <th>Plat Nomor</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $item)
                    <tr>
                        <td>{{ $item->no_invoice }}</td>
                        <td>{{ $item->workOrder?->no_wo ?? '-' }}</td>
                        <td>{{ $item->customer_name }}</td>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->plat_nomor }}</td>
                        <td>
                            @if ($item->is_paid)
                                <span class="status done">Lunas</span>
                            @else
                                <span class="status draft">Belum Lunas</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($item->total_kwitansi, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('kwitansi.pdf', $item) }}" target="_blank" class="btn btn-light"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                            @if ($role === 'admin')
                                <form action="{{ route('kwitansi.toggle-paid', $item) }}" method="POST" class="inline-form" style="display:inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn {{ $item->is_paid ? 'btn-warning' : 'btn-success' }}" type="submit">
                                        <i class="bi bi-patch-check"></i>
                                        {{ $item->is_paid ? 'Jadikan Belum Lunas' : 'Set Lunas' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; color:#64748b;">Belum ada data invoice.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-list">
        @forelse ($rows as $item)
            <article class="info-card">
                <h4>{{ $item->no_invoice }}</h4>
                <div class="kv"><span class="key">No WO</span><span>{{ $item->workOrder?->no_wo ?? '-' }}</span></div>
                <div class="kv"><span class="key">Customer</span><strong>{{ $item->customer_name }}</strong></div>
                <div class="kv"><span class="key">Tanggal</span><span>{{ $item->tanggal }}</span></div>
                <div class="kv"><span class="key">Plat</span><span>{{ $item->plat_nomor }}</span></div>
                <div class="kv"><span class="key">Status</span><span class="status {{ $item->is_paid ? 'done' : 'draft' }}">{{ $item->is_paid ? 'Lunas' : 'Belum Lunas' }}</span></div>
                <div class="kv"><span class="key">Total</span><strong>Rp {{ number_format($item->total_kwitansi, 0, ',', '.') }}</strong></div>
                <div style="display:flex; gap:.45rem; flex-wrap:wrap; margin-top:.65rem;">
                    <a href="{{ route('kwitansi.pdf', $item) }}" target="_blank" class="btn btn-light"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                    @if ($role === 'admin')
                        <form action="{{ route('kwitansi.toggle-paid', $item) }}" method="POST" class="inline-form">
                            @csrf
                            @method('PATCH')
                            <button class="btn {{ $item->is_paid ? 'btn-warning' : 'btn-success' }}" type="submit">
                                <i class="bi bi-patch-check"></i>
                                {{ $item->is_paid ? 'Jadikan Belum Lunas' : 'Set Lunas' }}
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <article class="info-card" style="text-align:center; color:#64748b;">Belum ada data invoice.</article>
        @endforelse
    </div>

    <div class="pagination-wrap">
        {{ $rows->links() }}
    </div>
</section>
@endsection

@push('scripts')
<style>
    .filter-wrap { padding: 1rem; border-bottom: var(--border); }
    .filter-grid { display:grid; gap:.8rem; grid-template-columns: repeat(1, minmax(0, 1fr)); }
    .filter-actions { display:flex; gap:.5rem; flex-wrap:wrap; align-items:end; }
    .pagination-wrap { padding: .9rem 1rem 1rem; }
    @media (min-width: 768px) {
        .filter-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
</style>
@endpush
