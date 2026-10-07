<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFieldVisitPhotoRequest;
use App\Http\Requests\StoreFieldVisitReportRequest;
use App\Http\Requests\UpdateFieldVisitReportRequest;
use App\Models\Customer;
use App\Models\FieldVisitPhoto;
use App\Models\FieldVisitReport;
use App\Services\AuditService;
use App\Services\FieldVisitReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FieldVisitReportController extends Controller
{
    public function __construct(
        private FieldVisitReportService $fieldVisitReportService,
        private AuditService $auditService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', FieldVisitReport::class);

        $reports = FieldVisitReport::with('customer', 'createdBy')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = '%'.$request->string('q').'%';
                $query->where('report_number', 'like', $search)
                    ->orWhere('location_label', 'like', $search)
                    ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', $search));
            })
            ->when($request->filled('purpose'), fn ($query) => $query->where('purpose', $request->string('purpose')))
            ->orderByDesc('visit_date')
            ->paginate(20)
            ->withQueryString();

        return view('field-visit-reports.index', compact('reports'));
    }

    public function show(FieldVisitReport $fieldVisitReport)
    {
        $this->authorize('view', FieldVisitReport::class);

        $fieldVisitReport->load('customer', 'createdBy', 'photos.uploadedBy');

        return view('field-visit-reports.show', compact('fieldVisitReport'));
    }

    public function create()
    {
        $this->authorize('create', FieldVisitReport::class);

        $customers = Customer::orderBy('name')->get(['id', 'name', 'company']);

        return view('field-visit-reports.create', compact('customers'));
    }

    public function store(StoreFieldVisitReportRequest $request)
    {
        $report = $this->fieldVisitReportService->create($request->validated(), $request->user(), $request);

        return redirect()->route('field-visit-reports.show', $report)->with('status', 'Laporan kunjungan berhasil disimpan.');
    }

    public function edit(FieldVisitReport $fieldVisitReport)
    {
        $this->authorize('update', FieldVisitReport::class);

        $customers = Customer::orderBy('name')->get(['id', 'name', 'company']);

        return view('field-visit-reports.edit', compact('fieldVisitReport', 'customers'));
    }

    public function update(UpdateFieldVisitReportRequest $request, FieldVisitReport $fieldVisitReport)
    {
        $this->fieldVisitReportService->update($fieldVisitReport, $request->validated(), $request->user(), $request);

        return redirect()->route('field-visit-reports.show', $fieldVisitReport)->with('status', 'Laporan kunjungan berhasil diperbarui.');
    }

    public function storePhoto(StoreFieldVisitPhotoRequest $request, FieldVisitReport $fieldVisitReport)
    {
        foreach ($request->file('files') as $file) {
            $path = $file->store("field-visit-photos/{$fieldVisitReport->id}", 'public');

            $photo = FieldVisitPhoto::create([
                'field_visit_report_id' => $fieldVisitReport->id,
                'uploaded_by' => $request->user()->id,
                'disk_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $this->auditService->log(
                actor: $request->user(),
                request: $request,
                module: 'FIELD_VISIT',
                action: 'ADD_FIELD_VISIT_PHOTO',
                target: 'FieldVisitPhoto',
                targetId: $photo->id,
                newValue: ['filename' => $photo->original_filename],
            );
        }

        return redirect()->route('field-visit-reports.show', $fieldVisitReport)->with('status', 'Foto berhasil diupload.');
    }

    public function destroyPhoto(Request $request, FieldVisitReport $fieldVisitReport, FieldVisitPhoto $photo)
    {
        $this->authorize('update', $fieldVisitReport);
        abort_unless($photo->field_visit_report_id === $fieldVisitReport->id, 404);

        Storage::disk('public')->delete($photo->disk_path);

        $this->auditService->log(
            actor: $request->user(),
            request: $request,
            module: 'FIELD_VISIT',
            action: 'DELETE_FIELD_VISIT_PHOTO',
            target: 'FieldVisitPhoto',
            targetId: $photo->id,
            oldValue: ['filename' => $photo->original_filename],
        );

        $photo->delete();

        return redirect()->route('field-visit-reports.show', $fieldVisitReport)->with('status', 'Foto berhasil dihapus.');
    }
}
