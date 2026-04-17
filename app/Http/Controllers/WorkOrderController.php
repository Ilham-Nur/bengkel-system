<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderComplaintItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = WorkOrder::query()
            ->with(['customer:id,name', 'complaintItems'])
            ->latest('tanggal')
            ->latest();

        if (! $user?->isAdmin()) {
            $query->where('user_id', $user?->id);
        }

        return view('workorder.index', [
            'workOrders' => $query->paginate(10)->withQueryString(),
        ]);
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

        $validated = $this->validateWorkOrder($request);

        DB::transaction(function () use ($request, $validated): void {
            $workOrder = WorkOrder::query()->create([
                'no_wo' => $this->generateNoWo(withLock: true),
                'user_id' => $validated['user_id'],
                'tanggal' => $validated['tanggal'],
                'jenis_motor' => $validated['jenis_motor'],
                'plat_nomor' => strtoupper($validated['plat_nomor']),
                'km_motor' => $validated['km_motor'],
                'total_keluhan_biaya' => collect($validated['complaint_items'])->sum('estimasi_biaya'),
            ]);

            $this->syncComplaintItems(
                workOrder: $workOrder,
                validatedItems: $validated['complaint_items'],
                request: $request,
                oldPhotoPaths: collect(),
            );
        });

        return redirect()->route('workorder.index')->with('success', 'Work order berhasil dibuat.');
    }

    public function edit(WorkOrder $workorder): View
    {
        $this->ensureAdmin();

        $workorder->load(['complaintItems.photos', 'customer:id,name,email,username']);

        $customers = User::query()
            ->where('role', User::ROLE_PELANGGAN)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'username']);

        return view('workorder.edit', [
            'workOrder' => $workorder,
            'customers' => $customers,
        ]);
    }

    public function update(Request $request, WorkOrder $workorder): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $this->validateWorkOrder($request);

        DB::transaction(function () use ($request, $validated, $workorder): void {
            $oldPhotoPaths = $workorder->complaintItems()
                ->with('photos:id,work_order_complaint_item_id,photo_path')
                ->get()
                ->flatMap(fn (WorkOrderComplaintItem $item) => $item->photos->pluck('photo_path'))
                ->values();

            $workorder->update([
                'user_id' => $validated['user_id'],
                'tanggal' => $validated['tanggal'],
                'jenis_motor' => $validated['jenis_motor'],
                'plat_nomor' => strtoupper($validated['plat_nomor']),
                'km_motor' => $validated['km_motor'],
                'total_keluhan_biaya' => collect($validated['complaint_items'])->sum('estimasi_biaya'),
            ]);

            $workorder->complaintItems()->delete();

            $this->syncComplaintItems(
                workOrder: $workorder,
                validatedItems: $validated['complaint_items'],
                request: $request,
                oldPhotoPaths: $oldPhotoPaths,
            );
        });

        return redirect()->route('workorder.index')->with('success', 'Work order berhasil diperbarui.');
    }

    public function destroy(WorkOrder $workorder): RedirectResponse
    {
        $this->ensureAdmin();

        DB::transaction(function () use ($workorder): void {
            $photoPaths = $workorder->complaintItems()
                ->with('photos:id,work_order_complaint_item_id,photo_path')
                ->get()
                ->flatMap(fn (WorkOrderComplaintItem $item) => $item->photos->pluck('photo_path'));

            $workorder->delete();

            if ($photoPaths->isNotEmpty()) {
                Storage::disk('public')->delete($photoPaths->all());
            }
        });

        return redirect()->route('workorder.index')->with('success', 'Work order berhasil dihapus.');
    }

    private function validateWorkOrder(Request $request): array
    {
        return $request->validate([
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
            'complaint_items.*.existing_photo_paths' => ['nullable', 'array'],
            'complaint_items.*.existing_photo_paths.*' => ['nullable', 'string'],
            'complaint_items.*.existing_photo_descriptions' => ['nullable', 'array'],
            'complaint_items.*.existing_photo_descriptions.*' => ['nullable', 'string', 'max:500'],
            'complaint_items.*.photos' => ['nullable', 'array'],
            'complaint_items.*.photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'complaint_items.*.photo_descriptions' => ['nullable', 'array'],
            'complaint_items.*.photo_descriptions.*' => ['nullable', 'string', 'max:500'],
        ]);
    }

    private function syncComplaintItems(WorkOrder $workOrder, array $validatedItems, Request $request, Collection $oldPhotoPaths): void
    {
        $keptOldPaths = collect();
        $oldPathLookup = $oldPhotoPaths->flip();

        foreach ($validatedItems as $itemIndex => $item) {
            $complaint = $workOrder->complaintItems()->create([
                'keluhan_item' => $item['keluhan_item'],
                'rekomendasi_perbaikan' => $item['rekomendasi_perbaikan'] ?? null,
                'sparepart' => $item['sparepart'] ?? null,
                'estimasi_biaya' => $item['estimasi_biaya'],
            ]);

            foreach (($item['existing_photo_paths'] ?? []) as $existingIndex => $path) {
                if (! is_string($path) || ! $oldPathLookup->has($path)) {
                    continue;
                }

                $keptOldPaths->push($path);
                $complaint->photos()->create([
                    'photo_path' => $path,
                    'photo_description' => $item['existing_photo_descriptions'][$existingIndex] ?? null,
                ]);
            }

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

        $deletedOldPaths = $oldPhotoPaths->diff($keptOldPaths)->values();
        if ($deletedOldPaths->isNotEmpty()) {
            Storage::disk('public')->delete($deletedOldPaths->all());
        }
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
