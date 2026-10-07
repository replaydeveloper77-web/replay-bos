<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use App\Services\ContractTemplateService;
use App\Services\EmployeeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeService $employeeService,
        private ContractTemplateService $contractTemplateService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Employee::class);

        $employees = Employee::with('user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = '%'.$request->string('q').'%';
                $query->where(fn ($q) => $q->where('name', 'like', $search)
                    ->orWhere('employee_number', 'like', $search)
                    ->orWhere('position', 'like', $search));
            })
            ->when($request->filled('department'), fn ($query) => $query->where('department', $request->string('department')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function show(Employee $employee)
    {
        $this->authorize('view', Employee::class);

        $employee->load('user', 'createdBy');

        return view('employees.show', compact('employee'));
    }

    public function create()
    {
        $this->authorize('create', Employee::class);

        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('employees.create', compact('users'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $this->employeeService->create($request->validated(), $request->user(), $request);

        $target = $request->input('redirect_to', route('employees.index'));

        return redirect($target)->with('status', 'Data karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $this->authorize('update', Employee::class);

        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('employees.edit', compact('employee', 'users'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $this->employeeService->update($employee, $request->validated(), $request->user(), $request);

        $target = $request->input('redirect_to', route('employees.index'));

        return redirect($target)->with('status', 'Data karyawan berhasil diperbarui.');
    }

    public function disable(Request $request, Employee $employee)
    {
        $this->authorize('update', Employee::class);

        $this->employeeService->setActive($employee, false, $request->user(), $request);

        return redirect()->route('employees.index')->with('status', 'Karyawan ditandai tidak aktif (resign).');
    }

    public function enable(Request $request, Employee $employee)
    {
        $this->authorize('update', Employee::class);

        $this->employeeService->setActive($employee, true, $request->user(), $request);

        return redirect()->route('employees.index')->with('status', 'Karyawan diaktifkan kembali.');
    }

    public function contractPdf(Employee $employee)
    {
        $this->authorize('view', Employee::class);

        $template = $this->contractTemplateService->forEmployee($employee);
        $company = $this->contractTemplateService->buildCompanyData();
        $logoDataUri = $this->contractTemplateService->buildLogoDataUri();
        $renderedBody = $this->contractTemplateService->renderBody($template->body, $employee, $company);
        $docTitle = $employee->contractDocumentTitle();
        $docNumber = $this->contractTemplateService->documentNumber($employee);
        $isPreview = false;

        $pdf = Pdf::loadView('employees.contract-pdf', compact('employee', 'company', 'logoDataUri', 'renderedBody', 'docTitle', 'docNumber', 'isPreview'))->setPaper('a4');

        return $pdf->download(str_replace(' ', '-', $docTitle).'-'.$employee->employee_number.'.pdf');
    }
}
