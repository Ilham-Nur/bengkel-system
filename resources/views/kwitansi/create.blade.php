@extends('layout.app')

@section('title', 'Buat Kwitansi')

@section('content')
<h1 class="page-title">Create Kwitansi</h1>

<section class="panel" style="padding:1rem;">
    @if ($availableWorkOrders->isEmpty())
        <div class="alert alert-warning" style="margin-bottom:1rem;">
            Tidak ada work order yang siap dibuat kwitansi. Pastikan ada work order yang belum memiliki kwitansi.
        </div>
    @endif

    <form action="{{ route('kwitansi.store') }}" method="POST" id="kwitansiForm">
        @csrf

        <div class="form-grid">
            <div>
                <label for="no_invoice">No Invoice / No Kwitansi</label>
                <input class="input" id="no_invoice" name="no_invoice" value="{{ old('no_invoice', $generatedInvoiceNo) }}" required>
            </div>
            <div>
                <label for="tanggal">Tanggal</label>
                <input class="input" id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', now()->toDateString()) }}" required>
            </div>
            <div>
                <label for="work_order_id">Relasi Work Order</label>
                <select class="input" id="work_order_id" name="work_order_id" @disabled($availableWorkOrders->isEmpty()) required>
                    <option value="">-- Pilih Work Order --</option>
                    @foreach ($availableWorkOrders as $wo)
                        <option
                            value="{{ $wo->id }}"
                            data-url="{{ route('kwitansi.create', ['work_order_id' => $wo->id]) }}"
                            @selected((int) old('work_order_id', $workOrder?->id) === $wo->id)
                        >
                            {{ $wo->no_wo }} - {{ $wo->customer?->name ?? '-' }} ({{ $wo->plat_nomor }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Nama Customer</label>
                <input class="input" value="{{ $workOrder?->customer?->name ?? '-' }}" readonly>
            </div>
            <div>
                <label>No HP Customer</label>
                <input class="input" value="{{ $workOrder?->customer?->no_hp ?? '-' }}" readonly>
            </div>
            <div>
                <label>Jenis Motor</label>
                <input class="input" value="{{ $workOrder?->jenis_motor ?? '-' }}" readonly>
            </div>
            <div>
                <label>Plat Nomor</label>
                <input class="input" value="{{ $workOrder?->plat_nomor ?? '-' }}" readonly>
            </div>
            <div>
                <label>Total Kwitansi</label>
                <input class="input" id="total_kwitansi_display" value="Rp 0" readonly>
            </div>
        </div>

        <hr style="margin:1rem 0; border:none; border-top:1px solid #e2e8f0;">
        <h3 style="margin-top:0;">Detail Item Kwitansi</h3>

        @php
            $oldItems = old('items', $workOrder?->complaintItems?->map(fn ($item) => [
                'item_name' => $item->keluhan_item,
                'qty' => 1,
                'unit_price' => (int) $item->estimasi_biaya,
            ])->toArray() ?? []);
        @endphp

        <div class="table-wrap" style="margin-top:.75rem;">
            <table id="kwitansiItemsTable">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Item</th>
                        <th style="width:120px;">Jumlah</th>
                        <th style="width:180px;">Harga Satuan</th>
                        <th style="width:180px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="kwitansiItemsBody">
                    @forelse ($oldItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <textarea class="input" name="items[{{ $index }}][item_name]" required>{{ $item['item_name'] }}</textarea>
                            </td>
                            <td>
                                <input class="input item-qty" type="number" min="1" name="items[{{ $index }}][qty]" value="{{ $item['qty'] }}" required>
                            </td>
                            <td>
                                <input class="input item-price" type="number" min="0" step="0.01" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] }}" required>
                            </td>
                            <td>
                                <strong class="item-subtotal">Rp 0</strong>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyItemsRow">
                            <td colspan="5" style="text-align:center; color:#64748b;">Pilih work order untuk memuat detail item kwitansi.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align:right;">Total Kwitansi</th>
                        <th><span id="table_total_display">Rp 0</span></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="display:flex; gap:.5rem; margin-top:1rem; flex-wrap:wrap;">
            <a href="{{ route('kwitansi.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button class="btn btn-success" type="submit" @disabled($availableWorkOrders->isEmpty())><i class="bi bi-check2-circle"></i> Simpan Kwitansi</button>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<style>
    .form-grid { display:grid; gap:.8rem; grid-template-columns: repeat(1, minmax(0, 1fr)); }
    #kwitansiItemsTable textarea { min-height: 64px; }
    @media (min-width: 768px) {
        .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
</style>
<script>
    const workOrderSelect = document.getElementById('work_order_id');
    const totalDisplay = document.getElementById('total_kwitansi_display');
    const tableTotalDisplay = document.getElementById('table_total_display');

    workOrderSelect?.addEventListener('change', (event) => {
        const selectedOption = event.target.selectedOptions[0];
        const url = selectedOption?.dataset?.url;

        if (url) {
            window.location.href = url;
            return;
        }

        window.location.href = '{{ route('kwitansi.create') }}';
    });

    function rupiah(value) {
        return `Rp ${new Intl.NumberFormat('id-ID').format(value)}`;
    }

    function updateTotal() {
        const rows = document.querySelectorAll('#kwitansiItemsBody tr');
        let total = 0;

        rows.forEach((row) => {
            const qtyInput = row.querySelector('.item-qty');
            const priceInput = row.querySelector('.item-price');
            const subtotalEl = row.querySelector('.item-subtotal');

            if (!qtyInput || !priceInput || !subtotalEl) {
                return;
            }

            const qty = Number(qtyInput.value || 0);
            const price = Number(priceInput.value || 0);
            const subtotal = qty * price;

            subtotalEl.textContent = rupiah(subtotal);
            total += subtotal;
        });

        const totalText = rupiah(total);
        totalDisplay.value = totalText;
        tableTotalDisplay.textContent = totalText;
    }

    document.addEventListener('input', (event) => {
        if (event.target.classList.contains('item-qty') || event.target.classList.contains('item-price')) {
            updateTotal();
        }
    });

    updateTotal();
</script>
@endpush
