<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateMonthlyInvoicesRequest;
use App\Http\Requests\StoreManualInvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\ServicePackage;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        $invoices = Invoice::with('createdBy', 'customer', 'quotation')
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->string('status') === 'OVERDUE') {
                    $query->where('status', 'UNPAID')->whereDate('due_date', '<', now());
                } else {
                    $query->where('status', $request->string('status'));
                }
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load('items', 'createdBy', 'customer', 'quotation', 'paidBy');

        return view('invoices.show', compact('invoice'));
    }

    public function create()
    {
        $this->authorize('create', Invoice::class);

        return view('invoices.create', [
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'company']),
            'ppnRate' => (float) Setting::get('ppn_rate', '11'),
            'packagesJson' => ServicePackage::where('is_active', true)->orderBy('name')->get()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'speed_label' => $p->speed_label,
                'price' => (float) $p->price,
            ])->values(),
        ]);
    }

    public function store(StoreManualInvoiceRequest $request)
    {
        $data = $request->validated();
        $customer = Customer::findOrFail($data['customer_id']);

        $invoice = $this->invoiceService->createManual($customer, $data['items'], $data['type'], $data['due_date'], $request->user(), $request);

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice berhasil dibuat.');
    }

    public function markPaid(Request $request, Invoice $invoice)
    {
        $this->authorize('manage', Invoice::class);

        $this->invoiceService->markPaid($invoice, $request->user(), $request);

        return back()->with('status', 'Invoice ditandai lunas.');
    }

    public function markUnpaid(Request $request, Invoice $invoice)
    {
        $this->authorize('manage', Invoice::class);

        $this->invoiceService->markUnpaid($invoice, $request->user(), $request);

        return back()->with('status', 'Invoice ditandai belum lunas.');
    }

    public function generateMonthlyForm()
    {
        $this->authorize('manage', Invoice::class);

        $billedCount = Customer::where('is_active', true)->whereNotNull('subscription_price')->where('subscription_price', '>', 0)->count();

        return view('invoices.generate-monthly', compact('billedCount'));
    }

    public function generateMonthly(GenerateMonthlyInvoicesRequest $request)
    {
        $period = Carbon::parse($request->validated()['period'].'-01');

        $invoices = $this->invoiceService->generateMonthly($period, $request->user(), $request);

        return redirect()->route('invoices.index')->with('status', "Berhasil generate {$invoices->count()} invoice tagihan bulanan untuk periode {$period->translatedFormat('F Y')}.");
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load('items', 'createdBy', 'customer', 'quotation');

        $company = [
            'name' => Setting::get('company_name', 'PT REPLAY INTI MEDIA'),
            'address' => Setting::get('company_address', 'Kuningan, Jawa Barat'),
            'phone' => Setting::get('company_phone', ''),
            'email' => Setting::get('company_email', ''),
        ];

        $bank = [
            'name' => Setting::get('bank_name', 'BANK BRI'),
            'account_number' => Setting::get('bank_account_number', '002801001940304'),
            'account_holder' => Setting::get('bank_account_holder', 'REPLAY INTI MEDIA'),
        ];

        $logoPath = public_path('images/branding/logo-replay-net-2x.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'company', 'bank', 'logoDataUri'))->setPaper('a4');

        return $pdf->download('Invoice-'.str_replace('/', '-', $invoice->invoice_number).'.pdf');
    }
}
