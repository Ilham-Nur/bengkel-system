<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KwitansiController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    private function ensureCanView(Kwitansi $kwitansi): void
    {
        $user = auth()->user();
        abort_unless($user?->isAdmin() || $kwitansi->workOrder?->user_id === $user?->id, 403);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->string('q', ''));
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = (int) $request->input('per_page', 10);

        if (! in_array($perPage, [5, 10, 25], true)) {
            $perPage = 10;
        }

        $query = Kwitansi::query()
            ->with(['workOrder:id,user_id,no_wo', 'workOrder.customer:id,name'])
            ->latest('tanggal')
            ->latest();

        if (! $user?->isAdmin()) {
            $query->whereHas('workOrder', fn ($workOrderQuery) => $workOrderQuery->where('user_id', $user?->id));
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('no_invoice', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('plat_nomor', 'like', "%{$search}%")
                    ->orWhereHas('workOrder', fn ($workOrderQuery) => $workOrderQuery->where('no_wo', 'like', "%{$search}%"));
            });
        }

        if (filled($startDate)) {
            $query->whereDate('tanggal', '>=', $startDate);
        }

        if (filled($endDate)) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        return view('kwitansi.index', [
            'rows' => $query->paginate($perPage)->withQueryString(),
            'filters' => [
                'q' => $search,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin();

        $workOrderId = $request->integer('work_order_id');

        $workOrderQuery = WorkOrder::query()
            ->with(['customer:id,name,username,no_hp', 'complaintItems:id,work_order_id,keluhan_item,estimasi_biaya'])
            ->whereDoesntHave('kwitansi')
            ->latest('tanggal')
            ->latest();

        $workOrder = $workOrderId
            ? (clone $workOrderQuery)->whereKey($workOrderId)->first()
            : null;

        $availableWorkOrders = (clone $workOrderQuery)
            ->limit(50)
            ->get(['id', 'no_wo', 'tanggal', 'user_id', 'jenis_motor', 'plat_nomor']);

        if ($workOrder && ! $workOrder->relationLoaded('complaintItems')) {
            $workOrder->load(['customer:id,name,username,no_hp', 'complaintItems:id,work_order_id,keluhan_item,estimasi_biaya']);
        }

        return view('kwitansi.create', [
            'generatedInvoiceNo' => $this->generateInvoiceNumber(),
            'workOrder' => $workOrder,
            'availableWorkOrders' => $availableWorkOrders,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'work_order_id' => ['required', 'exists:work_orders,id'],
            'tanggal' => ['required', 'date'],
            'no_invoice' => ['required', 'string', 'max:50', 'unique:kwitansis,no_invoice'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $workOrder = WorkOrder::query()
            ->with(['customer:id,name,username,no_hp'])
            ->whereKey($validated['work_order_id'])
            ->firstOrFail();

        abort_if($workOrder->kwitansi()->exists(), 422, 'Work order ini sudah memiliki kwitansi.');

        DB::transaction(function () use ($validated, $workOrder): void {
            $kwitansi = Kwitansi::query()->create([
                'no_invoice' => $validated['no_invoice'],
                'work_order_id' => $workOrder->id,
                'tanggal' => $validated['tanggal'],
                'customer_name' => $workOrder->customer?->name ?? '-',
                'customer_phone' => $workOrder->customer?->no_hp,
                'jenis_motor' => $workOrder->jenis_motor,
                'plat_nomor' => $workOrder->plat_nomor,
                'total_kwitansi' => collect($validated['items'])->sum(fn (array $item): float => (float) $item['qty'] * (float) $item['unit_price']),
                'is_paid' => false,
                'paid_at' => null,
            ]);

            foreach ($validated['items'] as $item) {
                $qty = (int) $item['qty'];
                $unitPrice = (float) $item['unit_price'];

                $kwitansi->items()->create([
                    'item_name' => $item['item_name'],
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $qty * $unitPrice,
                ]);
            }
        });

        return redirect()->route('kwitansi.index')->with('success', 'Kwitansi berhasil dibuat.');
    }

    public function exportPdf(Kwitansi $kwitansi): View
    {
        $kwitansi->load(['workOrder:id,user_id,no_wo,km_motor', 'items']);
        $this->ensureCanView($kwitansi);

        return view('kwitansi.pdf', [
            'kwitansi' => $kwitansi,
        ]);
    }

    public function togglePaid(Kwitansi $kwitansi): RedirectResponse
    {
        $this->ensureAdmin();

        $isPaid = ! $kwitansi->is_paid;
        $kwitansi->update([
            'is_paid' => $isPaid,
            'paid_at' => $isPaid ? now() : null,
        ]);

        return redirect()->route('kwitansi.index')->with(
            'success',
            $isPaid ? 'Invoice berhasil diubah menjadi lunas.' : 'Invoice berhasil diubah menjadi belum lunas.'
        );
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $lastNumber = Kwitansi::query()
            ->where('no_invoice', 'like', $prefix . '%')
            ->pluck('no_invoice')
            ->map(fn (string $noInvoice): int => (int) substr($noInvoice, -4))
            ->max() ?? 0;

        return $prefix . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }
}
