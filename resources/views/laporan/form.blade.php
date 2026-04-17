@extends('layout.app')

@section('title', 'Tambah Laporan Pekerjaan')

@section('content')
<h1 class="page-title">Tambah Laporan Pekerjaan</h1>

<section class="panel" style="padding:1rem;">
    <h3 style="margin-top:0;">Detail Work Order</h3>
    <div class="form-grid">
        <div>
            <label>No WO</label>
            <input class="input" value="{{ $workOrder->no_wo }}" readonly>
        </div>
        <div>
            <label>Tanggal WO</label>
            <input class="input" value="{{ $workOrder->tanggal }}" readonly>
        </div>
        <div>
            <label>Customer</label>
            <input class="input" value="{{ $workOrder->customer?->name ?? '-' }}" readonly>
        </div>
        <div>
            <label>Jenis Motor</label>
            <input class="input" value="{{ $workOrder->jenis_motor }}" readonly>
        </div>
        <div>
            <label>Plat Nomor</label>
            <input class="input" value="{{ $workOrder->plat_nomor }}" readonly>
        </div>
        <div>
            <label>KM Motor</label>
            <input class="input" value="{{ number_format($workOrder->km_motor, 0, ',', '.') }}" readonly>
        </div>
    </div>
</section>

<form action="{{ route('laporan.save', $workOrder) }}" method="POST" enctype="multipart/form-data" id="laporanForm" class="panel" style="padding:1rem;">
    @csrf

    <h3>Item Keluhan dari Work Order</h3>
    <p style="margin-top:0; color:#64748b;">Setiap item keluhan punya tanggal & jam selesai service masing-masing. Foto dapat diklik agar tampil lebih jelas.</p>

    @foreach ($workOrder->complaintItems as $index => $complaint)
        @php
            $reportItem = $report?->items?->firstWhere('work_order_complaint_item_id', $complaint->id);
        @endphp

        <article class="complaint-card">
            <input type="hidden" name="items[{{ $index }}][complaint_item_id]" value="{{ $complaint->id }}">

            <div class="complaint-col">
                <h4>Keluhan #{{ $index + 1 }}</h4>
                <div class="kv"><span class="key">Keluhan Item</span><strong>{{ $complaint->keluhan_item }}</strong></div>
                <div class="kv"><span class="key">Rekomendasi Perbaikan</span><span>{{ $complaint->rekomendasi_perbaikan ?: '-' }}</span></div>

                <label style="margin-top:.75rem;">Foto Keluhan</label>
                <div class="photo-grid">
                    @forelse ($complaint->photos as $photo)
                        <figure class="photo-card">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto keluhan" class="preview-image" data-preview-src="{{ asset('storage/' . $photo->photo_path) }}" data-preview-caption="{{ $photo->photo_description ?: 'Foto keluhan' }}">
                            @if ($photo->photo_description)
                                <figcaption>{{ $photo->photo_description }}</figcaption>
                            @endif
                        </figure>
                    @empty
                        <div style="color:#64748b; font-size:.86rem;">Belum ada foto keluhan.</div>
                    @endforelse
                </div>
            </div>

            <div class="complaint-col">
                <h4>Hasil Service</h4>

                <label for="service_finished_at_{{ $index }}">Tanggal & Jam Selesai (Keluhan #{{ $index + 1 }})</label>
                <input
                    class="input"
                    type="datetime-local"
                    id="service_finished_at_{{ $index }}"
                    name="items[{{ $index }}][service_finished_at]"
                    value="{{ old("items.$index.service_finished_at", optional($reportItem?->service_finished_at)->format('Y-m-d\\TH:i')) }}"
                    required
                >
                @error("items.$index.service_finished_at")
                    <small style="color:#b91c1c;">{{ $message }}</small>
                @enderror

                <label style="margin-top:.75rem;">Deskripsi Hasil Service</label>
                <textarea name="items[{{ $index }}][service_description]" placeholder="Contoh: Pembersihan karburator, ganti oli, setel ulang rem belakang">{{ old("items.$index.service_description", $reportItem?->service_description) }}</textarea>
                @error("items.$index.service_description")
                    <small style="color:#b91c1c;">{{ $message }}</small>
                @enderror

                <label style="margin-top:.75rem;">Foto Service (bisa lebih dari 1)</label>
                <div class="existing-service-photos">
                    @foreach ($reportItem?->photos ?? [] as $existingIndex => $photo)
                        <div class="existing-photo-row">
                            <input type="hidden" name="items[{{ $index }}][existing_photo_paths][{{ $existingIndex }}]" value="{{ $photo->photo_path }}">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto service lama" class="preview-image" data-preview-src="{{ asset('storage/' . $photo->photo_path) }}" data-preview-caption="{{ $photo->photo_description ?: 'Foto service' }}">
                            <input type="text" class="input" name="items[{{ $index }}][existing_photo_descriptions][{{ $existingIndex }}]" value="{{ old("items.$index.existing_photo_descriptions.$existingIndex", $photo->photo_description) }}" placeholder="Deskripsi foto service">
                        </div>
                    @endforeach
                </div>

                <div class="service-photo-inputs" data-item-index="{{ $index }}"></div>
                <div class="photo-action-row">
                    <button type="button" class="btn btn-light add-photo-btn" data-item-index="{{ $index }}" data-source="gallery"><i class="bi bi-images"></i> Ambil dari Galeri</button>
                    <button type="button" class="btn btn-primary add-photo-btn" data-item-index="{{ $index }}" data-source="camera"><i class="bi bi-camera"></i> Ambil dari Kamera</button>
                </div>
                <small class="helper-text">Di HP: tombol kamera akan membuka kamera langsung, tombol galeri untuk pilih foto yang sudah ada.</small>
            </div>
        </article>
    @endforeach

    <div class="form-action">
        <a href="{{ route('laporan.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Laporan</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('laporanForm');

    function addServicePhotoInput(itemIndex, source = 'gallery') {
        const container = document.querySelector(`.service-photo-inputs[data-item-index="${itemIndex}"]`);
        const nextIndex = container.querySelectorAll('.new-photo-row').length;
        const captureAttribute = source === 'camera' ? 'capture="environment"' : '';
        const sourceLabel = source === 'camera' ? 'Kamera HP' : 'Galeri';

        const wrapper = document.createElement('div');
        wrapper.className = 'new-photo-row';
        wrapper.innerHTML = `
            <div class="source-badge">Sumber: ${sourceLabel}</div>
            <input class="input" type="file" name="items[${itemIndex}][photos][${nextIndex}]" accept="image/*" ${captureAttribute}>
            <input class="input" type="text" name="items[${itemIndex}][photo_descriptions][${nextIndex}]" placeholder="Deskripsi foto service">
            <button type="button" class="btn btn-danger btn-remove-photo"><i class="bi bi-trash3"></i> Hapus</button>
        `;

        wrapper.querySelector('.btn-remove-photo').addEventListener('click', () => {
            wrapper.remove();
        });

        container.appendChild(wrapper);
    }

    document.querySelectorAll('.add-photo-btn').forEach((button) => {
        const itemIndex = button.dataset.itemIndex;
        const source = button.dataset.source || 'gallery';

        button.addEventListener('click', () => addServicePhotoInput(itemIndex, source));
    });

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

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        Swal.fire({
            title: 'Simpan laporan pekerjaan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>

<style>
    .complaint-card {
        border: var(--border);
        border-radius: 12px;
        padding: .9rem;
        margin-bottom: .9rem;
        display: grid;
        gap: .9rem;
        grid-template-columns: 1fr;
    }

    .complaint-col h4 { margin: 0 0 .6rem; }

    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: .6rem;
    }

    .photo-card {
        margin: 0;
        border: var(--border);
        border-radius: 10px;
        padding: .35rem;
        background: #fff;
    }

    .photo-card img,
    .existing-photo-row img {
        width: 100%;
        max-height: 220px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
        cursor: zoom-in;
    }

    .photo-card figcaption {
        margin-top: .35rem;
        font-size: .8rem;
        color: #64748b;
    }

    .existing-photo-row,
    .new-photo-row {
        display: grid;
        gap: .45rem;
        margin-bottom: .5rem;
        grid-template-columns: 1fr;
    }

    .photo-action-row {
        display:flex;
        flex-wrap:wrap;
        gap:.5rem;
        margin-top:.35rem;
    }

    .helper-text {
        display:block;
        color:#64748b;
        font-size:.78rem;
        margin-top:.35rem;
    }

    .source-badge {
        display:inline-block;
        background:#e2e8f0;
        color:#334155;
        border-radius:999px;
        padding:.2rem .55rem;
        font-size:.72rem;
        font-weight:600;
        width:fit-content;
    }

    @media (min-width: 900px) {
        .complaint-card { grid-template-columns: 1fr 1fr; }
        .new-photo-row { grid-template-columns: 1.15fr 1fr auto; align-items: center; }
        .existing-photo-row { grid-template-columns: 170px 1fr; align-items: center; }
    }
</style>
@endpush
