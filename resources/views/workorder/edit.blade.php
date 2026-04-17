@extends('layout.app')

@section('title', 'Edit Work Order')

@section('content')
<h1 class="page-title">Edit Work Order</h1>
<section class="form-page form-page-wide">
    @php
        $initialComplaintItems = old('complaint_items');

        if (! is_array($initialComplaintItems)) {
            $initialComplaintItems = $workOrder->complaintItems->map(function ($item) {
                return [
                    'keluhan_item' => $item->keluhan_item,
                    'rekomendasi_perbaikan' => $item->rekomendasi_perbaikan,
                    'sparepart' => $item->sparepart,
                    'estimasi_biaya' => $item->estimasi_biaya,
                    'existing_photos' => $item->photos->map(function ($photo) {
                        return [
                            'path' => $photo->photo_path,
                            'description' => $photo->photo_description,
                            'url' => asset('storage/'.$photo->photo_path),
                        ];
                    })->values()->all(),
                ];
            })->values()->all();
        }
    @endphp

    @if ($errors->any())
        <div class="flash flash-error">
            <strong>Validasi gagal.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('workorder.update', $workOrder) }}" method="POST" enctype="multipart/form-data" id="workOrderFormEdit" data-confirm-submit="Simpan perubahan work order?">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div>
                <label for="no_wo">No. WO</label>
                <input class="input" id="no_wo" value="{{ $workOrder->no_wo }}" readonly>
            </div>
            <div>
                <label for="user_id">Pelanggan</label>
                <select id="user_id" class="input" name="user_id" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($customers as $customer)
                        <option
                            value="{{ $customer->id }}"
                            data-meta="{{ $customer->email ?: $customer->username }}"
                            @selected((int) old('user_id', $workOrder->user_id) === $customer->id)
                        >
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="customer_meta">Info Pelanggan</label>
                <input class="input" id="customer_meta" readonly placeholder="Email/username pelanggan">
            </div>
            <div>
                <label for="tanggal">Tanggal</label>
                <input class="input" id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $workOrder->tanggal) }}" required>
            </div>
            <div>
                <label for="jenis_motor">Jenis Motor</label>
                <input class="input" id="jenis_motor" name="jenis_motor" value="{{ old('jenis_motor', $workOrder->jenis_motor) }}" required>
            </div>
            <div>
                <label for="plat_nomor">Plat Nomor</label>
                <input class="input text-uppercase" id="plat_nomor" name="plat_nomor" value="{{ old('plat_nomor', $workOrder->plat_nomor) }}" required>
            </div>
            <div>
                <label for="km_motor">KM Motor</label>
                <input class="input" id="km_motor" name="km_motor" type="number" min="0" value="{{ old('km_motor', $workOrder->km_motor) }}" required>
            </div>
        </div>

        <hr class="divider">
        <div class="keluhan-header">
            <h3>Item Keluhan</h3>
            <button type="button" class="btn btn-light" id="addComplaintItem"><i class="bi bi-plus-circle"></i> Tambah Item Keluhan</button>
        </div>
        <div id="complaintItemsWrap"></div>

        <div class="summary-box">
            <span>Total Keluhan Biaya (otomatis)</span>
            <strong id="grandTotal">Rp 0</strong>
        </div>

        <div class="form-action sticky-action-mobile">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
            <a href="{{ route('workorder.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
    const complaintWrap = document.getElementById('complaintItemsWrap');
    const addComplaintBtn = document.getElementById('addComplaintItem');
    const customerSelect = document.getElementById('user_id');
    const customerMeta = document.getElementById('customer_meta');
    const grandTotal = document.getElementById('grandTotal');
    const formEdit = document.getElementById('workOrderFormEdit');
    const existingData = @json($initialComplaintItems);

    let complaintIndex = 0;

    const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(Number(value || 0));

    const updateGrandTotal = () => {
        const estimasiInputs = complaintWrap.querySelectorAll('.estimasi-biaya');
        const total = [...estimasiInputs].reduce((sum, input) => sum + Number(input.value || 0), 0);
        grandTotal.textContent = formatCurrency(total);
    };

    const attachPhotoRowListener = (container) => {
        const addPhotoBtn = container.querySelector('.btn-add-photo');
        const photoWrap = container.querySelector('.photo-list');
        const itemNo = container.dataset.itemNo;
        let photoIndex = 0;

        addPhotoBtn.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'photo-row';
            row.innerHTML = `
                <input type="file" class="input" name="complaint_items[${itemNo}][photos][${photoIndex}]" accept="image/*">
                <input type="text" class="input" name="complaint_items[${itemNo}][photo_descriptions][${photoIndex}]" placeholder="Deskripsi foto baru (opsional)">
                <button type="button" class="btn btn-danger btn-mini remove-photo"><i class="bi bi-trash3"></i></button>
            `;

            row.querySelector('.remove-photo').addEventListener('click', () => row.remove());
            photoWrap.appendChild(row);
            photoIndex += 1;
        });
    };

    const renderExistingPhotos = (itemNo, existingPhotos = []) => {
        if (!existingPhotos.length) return '';

        return `
            <div class="existing-photo-grid">
                ${existingPhotos.map((photo, idx) => `
                    <label class="existing-photo-card">
                        <img src="${photo.url}" alt="Foto komponen">
                        <input type="hidden" name="complaint_items[${itemNo}][existing_photo_paths][${idx}]" value="${photo.path}">
                        <input type="text" class="input" name="complaint_items[${itemNo}][existing_photo_descriptions][${idx}]" value="${photo.description || ''}" placeholder="Deskripsi foto lama">
                        <span class="photo-help">Foto lama tetap tersimpan jika tidak dihapus item ini.</span>
                    </label>
                `).join('')}
            </div>
        `;
    };

    const addComplaintItem = (data = null) => {
        const item = document.createElement('article');
        item.className = 'complaint-card';
        item.dataset.itemNo = complaintIndex;
        item.innerHTML = `
            <div class="card-head">
                <h4>Keluhan #${complaintIndex + 1}</h4>
                <button type="button" class="btn btn-danger btn-mini remove-item"><i class="bi bi-x-circle"></i></button>
            </div>
            <label>Keluhan Item</label>
            <textarea name="complaint_items[${complaintIndex}][keluhan_item]" required>${data?.keluhan_item ?? ''}</textarea>

            <div class="form-grid">
                <div>
                    <label>Rekomendasi Perbaikan</label>
                    <textarea name="complaint_items[${complaintIndex}][rekomendasi_perbaikan]">${data?.rekomendasi_perbaikan ?? ''}</textarea>
                </div>
                <div>
                    <label>Sparepart</label>
                    <textarea name="complaint_items[${complaintIndex}][sparepart]">${data?.sparepart ?? ''}</textarea>
                </div>
            </div>

            <label>Estimasi Biaya</label>
            <input type="number" min="0" class="input estimasi-biaya" name="complaint_items[${complaintIndex}][estimasi_biaya]" value="${data?.estimasi_biaya ?? ''}" required>

            <div class="photo-header">
                <strong>Foto Komponen Rusak</strong>
                <button type="button" class="btn btn-light btn-mini btn-add-photo"><i class="bi bi-image"></i> Tambah Foto Baru</button>
            </div>
            ${renderExistingPhotos(complaintIndex, data?.existing_photos || [])}
            <div class="photo-list"></div>
        `;

        item.querySelector('.remove-item').addEventListener('click', () => {
            item.remove();
            updateGrandTotal();
        });
        item.querySelector('.estimasi-biaya').addEventListener('input', updateGrandTotal);
        complaintWrap.appendChild(item);
        attachPhotoRowListener(item);
        complaintIndex += 1;
    };

    customerSelect.addEventListener('change', () => {
        customerMeta.value = customerSelect.options[customerSelect.selectedIndex]?.dataset.meta || '';
    });

    addComplaintBtn.addEventListener('click', () => addComplaintItem());

    if (Array.isArray(existingData) && existingData.length) {
        existingData.forEach((item) => addComplaintItem(item));
    } else {
        addComplaintItem();
    }

    formEdit.addEventListener('submit', (event) => {
        event.preventDefault();
        Swal.fire({
            title: formEdit.dataset.confirmSubmit || 'Simpan perubahan?',
            text: 'Pastikan data sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                formEdit.submit();
            }
        });
    });

    customerSelect.dispatchEvent(new Event('change'));
    updateGrandTotal();
</script>

<style>
    .form-page-wide { max-width: 960px; }
    .divider { border: 0; border-top: 1px solid #e2e8f0; margin: 1rem 0; }
    .flash { border-radius: 10px; padding: .75rem .9rem; margin-bottom: .9rem; font-size: .9rem; }
    .flash ul { margin: .5rem 0 0 1rem; padding: 0; }
    .flash-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .keluhan-header { display:flex; align-items:center; justify-content:space-between; gap:.7rem; flex-wrap:wrap; margin-bottom:.8rem; }
    .keluhan-header h3 { margin:0; font-size:1rem; }
    .complaint-card { border:1px solid #dbeafe; border-radius:12px; background:#f8fbff; padding:.9rem; margin-bottom:.8rem; }
    .card-head { display:flex; justify-content:space-between; align-items:center; gap:.5rem; margin-bottom:.5rem; }
    .btn-mini { padding:.4rem .65rem; font-size:.78rem; }
    .photo-header { margin:.75rem 0 .45rem; display:flex; align-items:center; justify-content:space-between; gap:.5rem; }
    .photo-list { display:grid; gap:.5rem; }
    .photo-row { display:grid; gap:.5rem; grid-template-columns:1fr 1fr auto; align-items:center; }
    .existing-photo-grid { display:grid; gap:.6rem; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); margin-bottom:.55rem; }
    .existing-photo-card { border:1px solid #cbd5e1; border-radius:10px; padding:.5rem; background:#fff; display:grid; gap:.4rem; }
    .existing-photo-card img { width:100%; aspect-ratio:1 / 1; object-fit:contain; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; }
    .photo-help { color:#64748b; font-size:.75rem; }
    .summary-box { margin-top:1rem; border-radius:10px; padding:.75rem .9rem; display:flex; justify-content:space-between; background:#eff6ff; border:1px solid #bfdbfe; }

    @media (max-width: 767px) {
        .form-page { padding: .85rem; }
        .page-title { font-size: 1.05rem; }
        .photo-row { grid-template-columns: 1fr; }
        .sticky-action-mobile {
            position: sticky;
            bottom: 74px;
            background: #fff;
            padding: .6rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            z-index: 12;
        }
        .sticky-action-mobile .btn { width: 100%; justify-content: center; }
    }
</style>
@endpush
