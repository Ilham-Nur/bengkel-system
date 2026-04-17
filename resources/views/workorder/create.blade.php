@extends('layout.app')

@section('title', 'Form Work Order')

@section('content')
<h1 class="page-title">Tambah Work Order</h1>
<section class="form-page">
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

    <form action="{{ route('workorder.store') }}" method="POST" enctype="multipart/form-data" id="workOrderForm">
        @csrf
        <div class="form-grid">
            <div>
                <label for="no_wo">No. WO</label>
                <input class="input" id="no_wo" name="no_wo" value="{{ old('no_wo') }}" placeholder="Contoh: WO-2026-0004" required>
            </div>
            <div>
                <label for="user_id">Pelanggan</label>
                <select id="user_id" class="input" name="user_id" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($customers as $customer)
                        <option
                            value="{{ $customer->id }}"
                            data-meta="{{ $customer->email ?: $customer->username }}"
                            @selected((int) old('user_id') === $customer->id)
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
                <input class="input" id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', now()->toDateString()) }}" required>
            </div>
            <div>
                <label for="jenis_motor">Jenis Motor</label>
                <input class="input" id="jenis_motor" name="jenis_motor" value="{{ old('jenis_motor') }}" placeholder="Contoh: Honda Vario 160" required>
            </div>
            <div>
                <label for="plat_nomor">Plat Nomor</label>
                <input class="input text-uppercase" id="plat_nomor" name="plat_nomor" value="{{ old('plat_nomor') }}" placeholder="Contoh: B 1234 XYZ" required>
            </div>
            <div>
                <label for="km_motor">KM Motor</label>
                <input class="input" id="km_motor" name="km_motor" type="number" min="0" value="{{ old('km_motor') }}" placeholder="Contoh: 12000" required>
            </div>
        </div>

        <hr class="divider">
        <div class="keluhan-header">
            <h3>Item Keluhan</h3>
            <button type="button" class="btn btn-light" id="addComplaintItem">
                <i class="bi bi-plus-circle"></i> Tambah Item Keluhan
            </button>
        </div>
        <div id="complaintItemsWrap"></div>

        <div class="summary-box">
            <span>Total Keluhan Biaya (otomatis)</span>
            <strong id="grandTotal">Rp 0</strong>
        </div>

        <div class="form-action">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
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
                <input type="text" class="input" name="complaint_items[${itemNo}][photo_descriptions][${photoIndex}]" placeholder="Deskripsi foto (opsional)">
                <button type="button" class="btn btn-danger btn-mini remove-photo"><i class="bi bi-trash3"></i></button>
            `;

            row.querySelector('.remove-photo').addEventListener('click', () => row.remove());
            photoWrap.appendChild(row);
            photoIndex += 1;
        });
    };

    const addComplaintItem = () => {
        const item = document.createElement('article');
        item.className = 'complaint-card';
        item.dataset.itemNo = complaintIndex;
        item.innerHTML = `
            <div class="card-head">
                <h4>Keluhan #${complaintIndex + 1}</h4>
                <button type="button" class="btn btn-danger btn-mini remove-item"><i class="bi bi-x-circle"></i></button>
            </div>
            <label>Keluhan Item</label>
            <textarea name="complaint_items[${complaintIndex}][keluhan_item]" placeholder="Contoh: Mesin bergetar saat RPM tinggi" required></textarea>

            <div class="form-grid">
                <div>
                    <label>Rekomendasi Perbaikan</label>
                    <textarea name="complaint_items[${complaintIndex}][rekomendasi_perbaikan]" placeholder="Tulis saran perbaikan"></textarea>
                </div>
                <div>
                    <label>Sparepart</label>
                    <textarea name="complaint_items[${complaintIndex}][sparepart]" placeholder="Daftar sparepart"></textarea>
                </div>
            </div>

            <label>Estimasi Biaya</label>
            <input type="number" min="0" class="input estimasi-biaya" name="complaint_items[${complaintIndex}][estimasi_biaya]" placeholder="Contoh: 350000" required>

            <div class="photo-header">
                <strong>Foto Komponen Rusak</strong>
                <button type="button" class="btn btn-light btn-mini btn-add-photo"><i class="bi bi-image"></i> Tambah Foto</button>
            </div>
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

    addComplaintBtn.addEventListener('click', addComplaintItem);
    addComplaintItem();
    customerSelect.dispatchEvent(new Event('change'));
    updateGrandTotal();
</script>

<style>
    .divider { border: 0; border-top: 1px solid #e2e8f0; margin: 1rem 0; }
    .flash { border-radius: 10px; padding: .75rem .9rem; margin-bottom: .9rem; font-size: .9rem; }
    .flash ul { margin: .5rem 0 0 1rem; padding: 0; }
    .flash-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .keluhan-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .8rem;
        margin-bottom: .8rem;
        flex-wrap: wrap;
    }
    .keluhan-header h3 { margin: 0; font-size: 1rem; }
    .complaint-card {
        border: 1px solid #dbeafe;
        border-radius: 12px;
        background: #f8fbff;
        padding: .9rem;
        margin-bottom: .8rem;
    }
    .card-head { display: flex; align-items: center; justify-content: space-between; gap: .7rem; margin-bottom: .5rem; }
    .card-head h4 { margin: 0; font-size: .92rem; }
    .btn-mini { padding: .4rem .65rem; font-size: .78rem; }
    .photo-header {
        margin-top: .8rem;
        margin-bottom: .5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
    }
    .photo-list { display: grid; gap: .5rem; }
    .photo-row {
        display: grid;
        gap: .5rem;
        grid-template-columns: 1fr 1fr auto;
        align-items: center;
    }
    .summary-box {
        margin-top: 1rem;
        border-radius: 10px;
        padding: .75rem .9rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }
    @media (max-width: 767px) {
        .photo-row { grid-template-columns: 1fr; }
    }
</style>
@endpush
