<?php

namespace App\Services;

use App\Models\FieldVisitReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FieldVisitReportService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): FieldVisitReport
    {
        $report = FieldVisitReport::create([
            'report_number' => $this->generateReportNumber(),
            'customer_id' => $data['customer_id'] ?? null,
            'location_label' => $data['location_label'] ?? null,
            'purpose' => $data['purpose'] ?? 'KUNJUNGAN_RUTIN',
            'visit_date' => $data['visit_date'],
            'notes' => $data['notes'] ?? null,
            'result' => $data['result'] ?? null,
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'FIELD_VISIT',
            action: 'CREATE_FIELD_VISIT_REPORT',
            target: 'FieldVisitReport',
            targetId: $report->id,
            newValue: $report->only(['report_number', 'customer_id', 'location_label', 'purpose', 'visit_date']),
        );

        return $report;
    }

    public function update(FieldVisitReport $report, array $data, User $actor, Request $request): FieldVisitReport
    {
        $old = $report->only(['customer_id', 'location_label', 'purpose', 'visit_date', 'notes', 'result']);

        $report->update([
            'customer_id' => $data['customer_id'] ?? null,
            'location_label' => $data['location_label'] ?? null,
            'purpose' => $data['purpose'] ?? $report->purpose,
            'visit_date' => $data['visit_date'],
            'notes' => $data['notes'] ?? null,
            'result' => $data['result'] ?? null,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'FIELD_VISIT',
            action: 'UPDATE_FIELD_VISIT_REPORT',
            target: 'FieldVisitReport',
            targetId: $report->id,
            oldValue: $old,
            newValue: $report->only(['customer_id', 'location_label', 'purpose', 'visit_date', 'notes', 'result']),
        );

        return $report;
    }

    private function generateReportNumber(): string
    {
        return DB::transaction(function () {
            $prefix = 'FVR-';

            $lastNumber = FieldVisitReport::where('report_number', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('report_number')
                ->value('report_number');

            $nextSeq = $lastNumber ? ((int) substr($lastNumber, strlen($prefix))) + 1 : 1;

            return $prefix.str_pad((string) $nextSeq, 6, '0', STR_PAD_LEFT);
        });
    }
}
