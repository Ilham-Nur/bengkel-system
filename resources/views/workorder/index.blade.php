@extends('layout.app')

@section('title', 'Work Order')

@section('content')
@php
    $role = auth()->user()?->role ?? 'pelanggan';
@endphp

<h1 class="page-title">Work Order</h1>
<div class="role-box">Role aktif: <strong>{{ ucfirst($role) }}</strong></div>

<section class="panel">
    <div class="panel-head">
        <strong>List Work Order</strong>
        @if ($role === 'admin')
            <a href="{{ route('workorder.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Work Order</a>
        @endif
    </div>

    <div class="table-wrap desktop-only">
        <table>
            <thead>
                <tr>
                    <th>No WO</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Motor</th>
                    <th>Plat</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($workOrders as $item)
                    <tr>
                        <td>{{ $item->no_wo }}</td>
                        <td>{{ $item->customer?->name ?? '-' }}</td>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->jenis_motor }}</td>
                        <td>{{ $item->plat_nomor }}</td>
                        <td>Rp {{ number_format($item->total_keluhan_biaya, 0, ',', '.') }}</td>
                        <td>
                            @if ($role === 'admin')
                                <a href="{{ route('workorder.edit', $item) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                <form action="{{ route('workorder.destroy', $item) }}" method="POST" class="inline-form form-delete" data-confirm-title="Hapus Work Order?" data-confirm-text="Data akan dihapus permanen.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Delete</button>
                                </form>
                            @else
                                <span class="status process">Tercatat</span>
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
                <div class="kv"><span class="key">Pelanggan</span><strong>{{ $item->customer?->name ?? '-' }}</strong></div>
                <div class="kv"><span class="key">Tanggal</span><span>{{ $item->tanggal }}</span></div>
                <div class="kv"><span class="key">Motor</span><span>{{ $item->jenis_motor }}</span></div>
                <div class="kv"><span class="key">Plat</span><span>{{ $item->plat_nomor }}</span></div>
                <div class="kv"><span class="key">Total</span><strong>Rp {{ number_format($item->total_keluhan_biaya, 0, ',', '.') }}</strong></div>
                @if ($role === 'admin')
                    <div class="mobile-actions">
                        <a href="{{ route('workorder.edit', $item) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('workorder.destroy', $item) }}" method="POST" class="inline-form form-delete" data-confirm-title="Hapus Work Order?" data-confirm-text="Data akan dihapus permanen.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Delete</button>
                        </form>
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
<script>
    document.querySelectorAll('.form-delete').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            Swal.fire({
                title: form.dataset.confirmTitle || 'Yakin?',
                text: form.dataset.confirmText || 'Proses tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

<style>
    .inline-form { display:inline-flex; }
    .mobile-actions { display:flex; flex-wrap:wrap; gap:.45rem; margin-top:.65rem; }
    .pagination-wrap { padding: .9rem 1rem 1rem; }
    @media (max-width: 767px) {
        .btn { width: 100%; justify-content: center; }
        .mobile-actions .inline-form { width: 100%; }
    }
</style>
@endpush
