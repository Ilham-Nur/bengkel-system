@extends('layout.app')

@section('title', 'Detail Laporan Pekerjaan')

@section('content')
@php
    $totalKeluhan = $workOrder->complaintItems->count();
    $selesaiKeluhan = $report?->items?->count() ?? 0;
    $rawPercent = $totalKeluhan > 0 ? ($selesaiKeluhan / $totalKeluhan) * 100 : 0;
    $progressPercent = $selesaiKeluhan > 0 ? min(100, (int) (ceil($rawPercent / 25) * 25)) : 0;
@endphp

<h1 class="page-title">Detail Laporan Pekerjaan</h1>

<section class="panel" style="padding:1rem;">
    <h3 style="margin-top:0;">Informasi Work Order</h3>
    <div class="form-grid">
        <div><label>No WO</label><input class="input" value="{{ $workOrder->no_wo }}" readonly></div>
        <div><label>Tanggal WO</label><input class="input" value="{{ $workOrder->tanggal }}" readonly></div>
        <div><label>Customer</label><input class="input" value="{{ $workOrder->customer?->name ?? '-' }}" readonly></div>
        <div><label>Motor</label><input class="input" value="{{ $workOrder->jenis_motor }}" readonly></div>
    </div>

    <div class="progress-wrap" style="margin-top:.85rem;" title="{{ $selesaiKeluhan }} dari {{ $totalKeluhan }} keluhan selesai">
        <div class="progress-track"><div class="progress-fill" style="width: {{ $progressPercent }}%;"></div></div>
        <small>{{ $progressPercent }}% ({{ $selesaiKeluhan }}/{{ $totalKeluhan }}) selesai</small>
    </div>
</section>

<section class="panel" style="padding:1rem;">
    <h3 style="margin-top:0;">Detail Per Keluhan</h3>

    @foreach ($workOrder->complaintItems as $index => $complaint)
        @php
            $reportItem = $report?->items?->firstWhere('work_order_complaint_item_id', $complaint->id);
        @endphp
        <article class="complaint-card">
            <div>
                <h4>Keluhan #{{ $index + 1 }}</h4>
                <div class="kv"><span class="key">Keluhan</span><strong>{{ $complaint->keluhan_item }}</strong></div>
                <div class="kv"><span class="key">Rekomendasi</span><span>{{ $complaint->rekomendasi_perbaikan ?: '-' }}</span></div>

                <label style="margin-top:.65rem;">Foto Keluhan</label>
                <div class="photo-grid">
                    @forelse ($complaint->photos as $photo)
                        <figure class="photo-card">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto keluhan" class="preview-image" data-preview-src="{{ asset('storage/' . $photo->photo_path) }}" data-preview-caption="{{ $photo->photo_description ?: 'Foto keluhan' }}">
                            @if ($photo->photo_description)
                                <figcaption>{{ $photo->photo_description }}</figcaption>
                            @endif
                        </figure>
                    @empty
                        <small style="color:#64748b;">Belum ada foto keluhan.</small>
                    @endforelse
                </div>
            </div>

            <div>
                <h4>Hasil Service</h4>
                @if ($reportItem)
                    <div class="kv"><span class="key">Selesai Pada</span><strong>{{ optional($reportItem->service_finished_at)->format('d-m-Y H:i') ?: '-' }}</strong></div>
                    <div class="kv" style="display:block;">
                        <span class="key">Deskripsi Hasil</span>
                        <div style="margin-top:.35rem;">{{ $reportItem->service_description ?: '-' }}</div>
                    </div>

                    <label style="margin-top:.65rem;">Foto Hasil Service</label>
                    <div class="photo-grid">
                        @forelse ($reportItem->photos as $photo)
                            <figure class="photo-card">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto hasil service" class="preview-image" data-preview-src="{{ asset('storage/' . $photo->photo_path) }}" data-preview-caption="{{ $photo->photo_description ?: 'Foto hasil service' }}">
                                @if ($photo->photo_description)
                                    <figcaption>{{ $photo->photo_description }}</figcaption>
                                @endif
                            </figure>
                        @empty
                            <small style="color:#64748b;">Belum ada foto hasil service.</small>
                        @endforelse
                    </div>
                @else
                    <span class="status draft">Belum dikerjakan</span>
                @endif
            </div>
        </article>
    @endforeach

    <div class="form-action">
        <a href="{{ route('laporan.pdf', $workOrder) }}" target="_blank" class="btn btn-primary"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
        <a href="{{ route('laporan.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.preview-image').forEach((image) => {
        image.addEventListener('click', () => {
            Swal.fire({
                title: image.dataset.previewCaption || 'Preview Foto',
                imageUrl: image.dataset.previewSrc,
                imageAlt: image.alt || 'Preview foto',
                width: '92%',
                showCloseButton: true,
                showConfirmButton: false,
            });
        });
    });
</script>

<style>
    .complaint-card {
        border: var(--border);
        border-radius: 12px;
        padding: .9rem;
        margin-bottom: .85rem;
        display: grid;
        gap: .9rem;
        grid-template-columns: 1fr;
    }

    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(145px, 1fr));
        gap: .6rem;
        margin-top: .3rem;
    }

    .photo-card {
        margin: 0;
        border: var(--border);
        border-radius: 10px;
        padding: .35rem;
        background: #fff;
    }

    .photo-card img {
        width: 100%;
        max-height: 210px;
        object-fit: cover;
        border-radius: 8px;
        cursor: zoom-in;
    }

    .photo-card figcaption {
        margin-top: .35rem;
        font-size: .8rem;
        color: #64748b;
    }

    .progress-wrap { min-width: 150px; }
    .progress-track { width: 100%; height: 9px; background: #e2e8f0; border-radius: 999px; overflow: hidden; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, #2563eb, #10b981); border-radius: 999px; transition: width .25s ease; }
    .progress-wrap small { display:block; margin-top:.3rem; color:#64748b; font-size:.75rem; }

    @media (min-width: 900px) {
        .complaint-card { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush
