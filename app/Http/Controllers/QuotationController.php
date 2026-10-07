<?php

namespace App\Http\Controllers;

use App\Http\Requests\CloseQuotationRequest;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\CoveragePoint;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Setting;
use App\Models\ServicePackage;
use App\Services\InvoiceService;
use App\Services\QuotationService;
use Barryvdh\DomPDF\Facade\Pdf;
use RuntimeException;

class QuotationController extends Controller
{
    public function __construct(
        private QuotationService $quotationService,
        private InvoiceService $invoiceService,
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', Quotation::class);

        $quotations = Quotation::with('createdBy')->latest()->paginate(20);

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $this->authorize('create', Quotation::class);

        $registrationFee = (float) Setting::get('registration_fee', '0');

        return view('quotations.create', array_merge($this->formData(), [
            'quotation' => null,
            'ppnRate' => (float) Setting::get('ppn_rate', '11'),
            'existingItemsJson' => $registrationFee > 0 ? [
                ['service_package_id' => null, 'item_name' => 'Biaya Registrasi', 'qty' => 1, 'unit_price' => $registrationFee],
            ] : [],
        ]));
    }

    public function store(StoreQuotationRequest $request)
    {
        $quotation = $this->quotationService->create($request->validated(), $request->user(), $request);

        return redirect()->route('quotations.show', $quotation)->with('status', 'Surat penawaran berhasil dibuat.');
    }

    public function show(Quotation $quotation)
    {
        $this->authorize('view', $quotation);

        $quotation->load('items', 'createdBy', 'customer', 'invoice');

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $this->authorize('update', $quotation);

        $quotation->load('items');

        return view('quotations.create', array_merge($this->formData(), [
            'quotation' => $quotation,
            'ppnRate' => (float) $quotation->ppn_rate,
            'existingItemsJson' => $quotation->items->map(fn ($i) => [
                'service_package_id' => $i->service_package_id,
                'item_name' => $i->item_name,
                'qty' => $i->qty,
                'unit_price' => (float) $i->unit_price,
            ])->values(),
        ]));
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        $this->quotationService->update($quotation, $request->validated(), $request->user(), $request);

        return redirect()->route('quotations.show', $quotation)->with('status', 'Surat penawaran berhasil diperbarui.');
    }

    public function pdf(Quotation $quotation)
    {
        $this->authorize('view', $quotation);

        $quotation->load('items', 'createdBy');

        $company = [
            'name' => Setting::get('company_name', 'PT REPLAY INTI MEDIA'),
            'address' => Setting::get('company_address', 'Kuningan, Jawa Barat'),
            'phone' => Setting::get('company_phone', ''),
            'email' => Setting::get('company_email', ''),
        ];

        $logoPath = public_path('images/branding/logo-replay-net-2x.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('quotations.pdf', compact('quotation', 'company', 'logoDataUri'))->setPaper('a4');

        return $pdf->download('Surat-Penawaran-'.str_replace('/', '-', $quotation->quotation_number).'.pdf');
    }

    public function closeForm(Quotation $quotation)
    {
        $this->authorize('create', Invoice::class);

        if ($quotation->invoice()->exists()) {
            return redirect()->route('invoices.show', $quotation->invoice)
                ->with('status', 'Penawaran ini sudah di-closing sebelumnya.');
        }

        $quotation->load('items');

        return view('quotations.close', [
            'quotation' => $quotation,
            'packages' => ServicePackage::where('is_active', true)->orderBy('name')->get(),
            'itemsJson' => $quotation->items->map(fn ($i) => [
                'service_package_id' => $i->service_package_id,
                'item_name' => $i->item_name,
                'qty' => $i->qty,
                'unit_price' => (float) $i->unit_price,
            ])->values(),
            'packagesJson' => ServicePackage::where('is_active', true)->orderBy('name')->get()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'speed_label' => $p->speed_label,
                'price' => (float) $p->price,
            ])->values(),
        ]);
    }

    public function close(CloseQuotationRequest $request, Quotation $quotation)
    {
        try {
            $invoice = $this->invoiceService->close($quotation, $request->validated()['items'], $request->user(), $request);
        } catch (RuntimeException $e) {
            return redirect()->route('quotations.show', $quotation)->with('status', $e->getMessage());
        }

        return redirect()->route('invoices.show', $invoice)->with('status', 'Closing berhasil, invoice dibuat dan pelanggan masuk ke daftar aktivasi NOC.');
    }

    private function formData(): array
    {
        $packages = ServicePackage::where('is_active', true)->orderBy('name')->get();
        $coveragePoints = CoveragePoint::where('is_active', true)->get(['id', 'name', 'latitude', 'longitude', 'radius_km']);

        return [
            'packages' => $packages,
            'coveragePoints' => $coveragePoints,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'company']),
            'packagesJson' => $packages->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'speed_label' => $p->speed_label,
                'price' => (float) $p->price,
            ])->values(),
            'coveragePointsJson' => $coveragePoints->map(fn ($p) => [
                'name' => $p->name,
                'lat' => (float) $p->latitude,
                'lng' => (float) $p->longitude,
                'radius_km' => (float) $p->radius_km,
            ])->values(),
        ];
    }
}
