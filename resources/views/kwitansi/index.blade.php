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
            <button class="btn btn-primary"><i class="bi bi-plus-circle"></i> Buat Invoice</button>
        @endif
    </div>

    <div class="filter-wrap">
        <form method="GET" action="{{ route('kwitansi.index') }}" class="filter-grid">
            <div>
                <label for="q">Pencarian</label>
                <input class="input" id="q" name="q" value="{{ $filters['q'] }}" placeholder="No invoice, nama, plat">
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
                    <th>Nama Customer</th>
                    <th>Tanggal</th>
                    <th>Plat Nomor</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $item)
                    <tr>
                        <td>{{ $item['invoice'] }}</td>
                        <td>{{ $item['nama'] }}</td>
                        <td>{{ $item['tanggal'] }}</td>
                        <td>{{ $item['plat'] }}</td>
                        <td>
                            <button class="btn btn-light"><i class="bi bi-eye"></i> Detail</button>
                            @if ($role === 'admin')
                                <button class="btn btn-light"><i class="bi bi-printer"></i> Print</button>
                                <button class="btn btn-success btn-lunas"><i class="bi bi-patch-check"></i> Stamp Lunas</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color:#64748b;">Belum ada data invoice.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-list">
        @forelse ($rows as $item)
            <article class="info-card">
                <h4>{{ $item['invoice'] }}</h4>
                <div class="kv"><span class="key">Customer</span><strong>{{ $item['nama'] }}</strong></div>
                <div class="kv"><span class="key">Tanggal</span><span>{{ $item['tanggal'] }}</span></div>
                <div class="kv"><span class="key">Plat</span><span>{{ $item['plat'] }}</span></div>
                <div style="display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.6rem;">
                    <button class="btn btn-light"><i class="bi bi-eye"></i> Detail</button>
                    @if ($role === 'admin')
                        <button class="btn btn-light"><i class="bi bi-printer"></i> Print</button>
                        <button class="btn btn-success btn-lunas"><i class="bi bi-patch-check"></i> Stamp Lunas</button>
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
<script>
    document.querySelectorAll('.btn-lunas').forEach((button) => {
        button.addEventListener('click', () => {
            Swal.fire('Berhasil', 'Invoice berhasil distamp lunas.', 'success');
        });
    });
</script>
@endpush
