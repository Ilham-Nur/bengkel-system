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
        <div style="display:flex; gap:.45rem; flex-wrap:wrap;">
            <button class="btn btn-light" type="button" id="open-kwitansi-filter"><i class="bi bi-funnel"></i> Filter</button>
            @if ($role === 'admin')
                <button class="btn btn-primary"><i class="bi bi-plus-circle"></i> Buat Invoice</button>
            @endif
        </div>
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
    .modal-filter-grid { display:grid; gap:.7rem; text-align:left; }
    .modal-filter-grid label { margin:0; }
    .pagination-wrap { padding: .9rem 1rem 1rem; }
</style>
<script>
    document.querySelectorAll('.btn-lunas').forEach((button) => {
        button.addEventListener('click', () => {
            Swal.fire('Berhasil', 'Invoice berhasil distamp lunas.', 'success');
        });
    });

    document.getElementById('open-kwitansi-filter')?.addEventListener('click', () => {
        Swal.fire({
            title: 'Filter Kwitansi',
            html: `
                <div class="modal-filter-grid">
                    <div>
                        <label for="modal-q">Pencarian</label>
                        <input class="input" id="modal-q" placeholder="No invoice, nama, plat" value="{{ e($filters['q']) }}">
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

                window.location.href = `{{ route('kwitansi.index') }}${params.toString() ? `?${params.toString()}` : ''}`;
            },
        }).then((result) => {
            if (result.isDenied) {
                window.location.href = '{{ route('kwitansi.index') }}';
            }
        });
    });
</script>
@endpush
