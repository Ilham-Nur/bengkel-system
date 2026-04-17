<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function create(): View
    {
        $this->ensureAdmin();

        $customers = User::query()
            ->where('role', User::ROLE_PELANGGAN)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'username']);

        return view('workorder.create', [
            'customers' => $customers,
            'generatedNoWo' => $this->generateNoWo(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tanggal' => ['required', 'date'],
            'jenis_motor' => ['required', 'string', 'max:255'],
            'plat_nomor' => ['required', 'string', 'max:20'],
            'km_motor' => ['required', 'integer', 'min:0'],
            'complaint_items' => ['required', 'array', 'min:1'],
            'complaint_items.*.keluhan_item' => ['required', 'string'],
            'complaint_items.*.rekomendasi_perbaikan' => ['nullable', 'string'],
            'complaint_items.*.sparepart' => ['nullable', 'string'],
            'complaint_items.*.estimasi_biaya' => ['required', 'numeric', 'min:0'],
            'complaint_items.*.photos' => ['nullable', 'array'],
            'complaint_items.*.photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'complaint_items.*.photo_descriptions' => ['nullable', 'array'],
            'complaint_items.*.photo_descriptions.*' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $generatedNoWo = $this->generateNoWo(withLock: true);
            $totalKeluhanBiaya = collect($validated['complaint_items'])->sum(
                fn (array $item): float => (float) $item['estimasi_biaya']
            );

            $workOrder = WorkOrder::query()->create([
                'no_wo' => $generatedNoWo,
                'user_id' => $validated['user_id'],
                'tanggal' => $validated['tanggal'],
                'jenis_motor' => $validated['jenis_motor'],
                'plat_nomor' => strtoupper($validated['plat_nomor']),
                'km_motor' => $validated['km_motor'],
                'total_keluhan_biaya' => $totalKeluhanBiaya,
            ]);

            foreach ($validated['complaint_items'] as $itemIndex => $item) {
                $complaint = $workOrder->complaintItems()->create([
                    'keluhan_item' => $item['keluhan_item'],
                    'rekomendasi_perbaikan' => $item['rekomendasi_perbaikan'] ?? null,
                    'sparepart' => $item['sparepart'] ?? null,
                    'estimasi_biaya' => $item['estimasi_biaya'],
                ]);

                $photoFiles = $request->file("complaint_items.$itemIndex.photos", []);
                $photoDescriptions = $item['photo_descriptions'] ?? [];

                foreach ($photoFiles as $photoIndex => $photoFile) {
                    if (! $photoFile) {
                        continue;
                    }

                    $storedPath = $photoFile->store('work-orders/photos', 'public');

                    $complaint->photos()->create([
                        'photo_path' => $storedPath,
                        'photo_description' => $photoDescriptions[$photoIndex] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('workorder.index')->with('success', 'Work order berhasil dibuat.');
    }

    private function generateNoWo(bool $withLock = false): string
    {
        $year = now()->format('Y');
        $prefix = "WO-$year-";

        $query = WorkOrder::query()
            ->where('no_wo', 'like', $prefix.'%')
            ->orderByDesc('id');

        if ($withLock) {
            $query->lockForUpdate();
        }

        $latestNoWo = $query->value('no_wo');
        $lastSequence = 0;

        if (is_string($latestNoWo) && preg_match('/^WO-\d{4}-(\d{4})$/', $latestNoWo, $matches)) {
            $lastSequence = (int) $matches[1];
        }

        return $prefix.str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
    }
}
