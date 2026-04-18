<?php

namespace App\Http\Controllers;

use App\Models\ServiceReport;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class LaporanController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    private function ensureCanView(WorkOrder $workOrder): void
    {
        $user = auth()->user();
        abort_unless($user?->isAdmin() || $workOrder->user_id === $user?->id, 403);
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

        $query = WorkOrder::query()
            ->with(['customer:id,name', 'complaintItems:id,work_order_id', 'serviceReport.items:id,service_report_id,work_order_complaint_item_id', 'kwitansi:id,work_order_id,no_invoice'])
            ->latest('tanggal')
            ->latest();

        if (! $user?->isAdmin()) {
            $query->where('user_id', $user?->id);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('no_wo', 'like', "%{$search}%")
                    ->orWhere('jenis_motor', 'like', "%{$search}%")
                    ->orWhere('plat_nomor', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if (filled($startDate)) {
            $query->whereDate('tanggal', '>=', $startDate);
        }

        if (filled($endDate)) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        return view('laporan.index', [
            'workOrders' => $query->paginate($perPage)->withQueryString(),
            'filters' => [
                'q' => $search,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show(WorkOrder $workorder): View
    {
        $this->ensureCanView($workorder);

        $workorder->load([
            'customer:id,name,email,username',
            'complaintItems.photos',
            'serviceReport.items.photos',
        ]);

        return view('laporan.show', [
            'workOrder' => $workorder,
            'report' => $workorder->serviceReport,
        ]);
    }

    public function form(WorkOrder $workorder): View
    {
        $this->ensureAdmin();

        $workorder->load([
            'customer:id,name,email,username',
            'complaintItems.photos',
            'serviceReport.items.photos',
        ]);

        return view('laporan.form', [
            'workOrder' => $workorder,
            'report' => $workorder->serviceReport,
        ]);
    }

    public function exportPdf(WorkOrder $workorder): View
    {
        $this->ensureCanView($workorder);

        $workorder->load([
            'customer:id,name,email,username',
            'complaintItems.photos',
            'serviceReport.items.photos',
        ]);

        return view('laporan.pdf', [
            'workOrder' => $workorder,
            'report' => $workorder->serviceReport,
        ]);
    }

    public function save(Request $request, WorkOrder $workorder): RedirectResponse
    {
        $this->ensureAdmin();

        $validator = Validator::make($request->all(), [
            'items' => ['required', 'array', 'min:1'],
            'items.*.complaint_item_id' => ['required', 'exists:work_order_complaint_items,id'],
            'items.*.is_completed' => ['nullable', 'boolean'],
            'items.*.service_finished_at' => ['nullable', 'date'],
            'items.*.service_description' => ['nullable', 'string'],
            'items.*.photos' => ['nullable', 'array'],
            'items.*.photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'items.*.photo_descriptions' => ['nullable', 'array'],
            'items.*.photo_descriptions.*' => ['nullable', 'string', 'max:500'],
            'items.*.existing_photo_paths' => ['nullable', 'array'],
            'items.*.existing_photo_paths.*' => ['nullable', 'string'],
            'items.*.existing_photo_descriptions' => ['nullable', 'array'],
            'items.*.existing_photo_descriptions.*' => ['nullable', 'string', 'max:500'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            foreach ($request->input('items', []) as $index => $item) {
                $isCompleted = (bool) ($item['is_completed'] ?? false);

                if ($isCompleted && blank($item['service_finished_at'] ?? null)) {
                    $validator->errors()->add("items.$index.service_finished_at", 'Tanggal & jam selesai wajib diisi jika keluhan ditandai selesai.');
                }
            }
        });

        $validated = $validator->validate();

        DB::transaction(function () use ($request, $validated, $workorder): void {
            $workorder->load(['serviceReport.items.photos']);

            $report = ServiceReport::query()->firstOrCreate([
                'work_order_id' => $workorder->id,
            ]);

            $oldPhotoPaths = collect();
            if ($workorder->serviceReport) {
                $oldPhotoPaths = $workorder->serviceReport->items
                    ->flatMap(fn ($item) => $item->photos->pluck('photo_path'))
                    ->values();
            }

            $report->items()->delete();

            $keptOld = collect();
            $oldLookup = $oldPhotoPaths->flip();

            foreach ($validated['items'] as $index => $item) {
                $isCompleted = (bool) ($item['is_completed'] ?? false);
                if (! $isCompleted) {
                    continue;
                }

                $reportItem = $report->items()->create([
                    'work_order_complaint_item_id' => $item['complaint_item_id'],
                    'service_finished_at' => $item['service_finished_at'] ?? null,
                    'service_description' => $item['service_description'] ?? null,
                ]);

                foreach (($item['existing_photo_paths'] ?? []) as $existingIndex => $path) {
                    if (! is_string($path) || ! $oldLookup->has($path)) {
                        continue;
                    }

                    $keptOld->push($path);
                    $reportItem->photos()->create([
                        'photo_path' => $path,
                        'photo_description' => $item['existing_photo_descriptions'][$existingIndex] ?? null,
                    ]);
                }

                $photoFiles = $request->file("items.$index.photos", []);
                $photoDescriptions = $item['photo_descriptions'] ?? [];
                foreach ($photoFiles as $photoIndex => $photoFile) {
                    if (! $photoFile) {
                        continue;
                    }

                    $storedPath = $photoFile->store('service-reports/photos', 'public');
                    $reportItem->photos()->create([
                        'photo_path' => $storedPath,
                        'photo_description' => $photoDescriptions[$photoIndex] ?? null,
                    ]);
                }
            }

            $deletedOld = $oldPhotoPaths->diff($keptOld)->values();
            if ($deletedOld->isNotEmpty()) {
                Storage::disk('public')->delete($deletedOld->all());
            }
        });

        return redirect()->route('laporan.index')->with('success', 'Laporan pekerjaan berhasil disimpan.');
    }
}
