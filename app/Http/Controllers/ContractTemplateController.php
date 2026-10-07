<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateContractTemplateRequest;
use App\Models\ContractTemplate;
use App\Models\Employee;
use App\Services\ContractTemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ContractTemplateController extends Controller
{
    public function __construct(private ContractTemplateService $contractTemplateService)
    {
    }

    public function index()
    {
        $this->authorize('contract.template.manage');

        $templates = ContractTemplate::with('updatedBy')->orderBy('type')->get();

        return view('contract-templates.index', compact('templates'));
    }

    public function edit(string $type)
    {
        $this->authorize('contract.template.manage');

        $template = ContractTemplate::where('type', $type)->firstOrFail();
        $employees = Employee::where('is_active', true)->orderBy('name')->get(['id', 'name', 'employment_status']);
        $placeholders = collect(ContractTemplate::PLACEHOLDERS)
            ->map(fn ($description, $token) => ['display' => '{{'.$token.'}}', 'description' => $description])
            ->values();

        return view('contract-templates.edit', compact('template', 'employees', 'placeholders'));
    }

    public function update(UpdateContractTemplateRequest $request, string $type)
    {
        $template = ContractTemplate::where('type', $type)->firstOrFail();

        $this->contractTemplateService->update($template, $request->validated()['body'], $request->user(), $request);

        return redirect()->route('contract-templates.edit', $type)->with('status', 'Redaksi berhasil disimpan.');
    }

    public function preview(Request $request, string $type)
    {
        $this->authorize('contract.template.manage');

        $data = $request->validate([
            'body' => ['required', 'string'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
        ]);

        $employee = $data['employee_id'] ?? null
            ? Employee::findOrFail($data['employee_id'])
            : $this->sampleEmployee($type);

        $company = $this->contractTemplateService->buildCompanyData();
        $logoDataUri = $this->contractTemplateService->buildLogoDataUri();
        $renderedBody = $this->contractTemplateService->renderBody($data['body'], $employee, $company);
        $docTitle = $employee->contractDocumentTitle();
        $docNumber = $this->contractTemplateService->documentNumber($employee);
        $isPreview = true;

        $pdf = Pdf::loadView('employees.contract-pdf', compact('employee', 'company', 'logoDataUri', 'renderedBody', 'docTitle', 'docNumber', 'isPreview'))->setPaper('a4');

        return $pdf->stream('Pratinjau-'.$type.'.pdf');
    }

    private function sampleEmployee(string $type): Employee
    {
        $employee = new Employee([
            'employee_number' => 'EMP-CONTOH',
            'name' => 'Contoh Karyawan',
            'nik_ktp' => '3208xxxxxxxxxxx',
            'gender' => 'L',
            'birth_place' => 'Kuningan',
            'birth_date' => '1995-01-01',
            'address' => 'Alamat contoh karyawan',
            'position' => 'Jabatan Contoh',
            'department' => 'NOC',
            'employment_status' => $type === 'PKWTT' ? 'TETAP' : 'KONTRAK',
            'join_date' => now()->subMonths(3)->toDateString(),
            'contract_end_date' => now()->addMonths(3)->toDateString(),
            'salary' => 3000000,
        ]);
        $employee->exists = false;

        return $employee;
    }
}
