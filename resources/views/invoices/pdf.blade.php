<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; }
        .header-table { width: 100%; border-bottom: 2px solid #0b1d3a; padding-bottom: 8px; margin-bottom: 16px; }
        .header-table td { vertical-align: top; }
        .company-name { font-size: 16px; font-weight: bold; color: #0b1d3a; }
        .company-detail { font-size: 10px; color: #555; }
        .doc-title { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin: 10px 0 4px; }
        .doc-number { text-align: center; font-size: 10px; color: #555; margin-bottom: 16px; }
        .info-table { width: 100%; margin-bottom: 14px; }
        .info-table td { padding: 2px 0; font-size: 11px; vertical-align: top; }
        .info-label { width: 130px; color: #444; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items-table th, .items-table td { border: 1px solid #ccc; padding: 6px 8px; font-size: 10.5px; }
        .items-table th { background: #0b1d3a; color: #fff; text-align: left; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .totals-table { width: 45%; margin-left: 55%; margin-bottom: 20px; }
        .totals-table td { padding: 4px 8px; font-size: 11px; }
        .totals-table .grand-total td { border-top: 1.5px solid #0b1d3a; font-weight: bold; font-size: 12px; }
        .payment-box { margin: 10px 0 20px; padding: 10px 12px; font-size: 11px; border: 1.5px solid #0b1d3a; background: #f7f8fa; }
        .payment-box .bank-name { font-weight: bold; font-size: 12px; color: #0b1d3a; }
        .payment-box .account-number { font-size: 15px; font-weight: bold; letter-spacing: 1px; }
        .closing { font-size: 11px; line-height: 1.6; margin-bottom: 40px; }
        .signature-table { width: 100%; margin-top: 30px; }
        .signature-table td { width: 50%; text-align: center; font-size: 11px; vertical-align: top; }
        .signature-space { height: 60px; }
        .footer-note { font-size: 9px; color: #888; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            @if ($logoDataUri)
                <td style="width: 60px;">
                    <img src="{{ $logoDataUri }}" style="width: 50px; height: 50px;">
                </td>
            @endif
            <td>
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-detail">{{ $company['address'] }}</div>
                <div class="company-detail">
                    @if ($company['phone']) Telp/WA: {{ $company['phone'] }} @endif
                    @if ($company['email']) &nbsp;|&nbsp; Email: {{ $company['email'] }} @endif
                </div>
            </td>
            <td style="width: 25%; text-align: right;">
                <div class="company-detail">Tanggal: {{ $invoice->created_at->format('d F Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">INVOICE</div>
    <div class="doc-number">
        No. {{ $invoice->invoice_number }} &middot; {{ $invoice->typeLabel() }}
        @if ($invoice->quotation) &middot; Ref. Penawaran {{ $invoice->quotation->quotation_number }} @endif
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Kepada Yth.</td>
            <td>: {{ $invoice->customer?->name ?? $invoice->quotation?->prospect_name }}</td>
        </tr>
        @if ($invoice->due_date)
        <tr>
            <td class="info-label">Jatuh Tempo</td>
            <td>: {{ $invoice->due_date->format('d F Y') }}</td>
        </tr>
        @endif
        @if ($invoice->customer?->company)
        <tr>
            <td class="info-label">Perusahaan</td>
            <td>: {{ $invoice->customer->company }}</td>
        </tr>
        @endif
        @if ($invoice->customer?->location)
        <tr>
            <td class="info-label">Alamat</td>
            <td>: {{ $invoice->customer->location }}</td>
        </tr>
        @endif
        @if ($invoice->customer?->whatsapp)
        <tr>
            <td class="info-label">No. WhatsApp</td>
            <td>: {{ $invoice->customer->whatsapp }}</td>
        </tr>
        @endif
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Layanan / Item</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 22%;" class="text-end">Harga Satuan</th>
                <th style="width: 23%;" class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal (belum PPN)</td>
            <td class="text-end">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>PPN {{ rtrim(rtrim(number_format($invoice->ppn_rate, 2, ',', '.'), '0'), ',') }}%</td>
            <td class="text-end">Rp {{ number_format($invoice->ppn_amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total Tagihan</td>
            <td class="text-end">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="payment-box">
        <div>Mohon pembayaran ditransfer ke rekening berikut:</div>
        <div class="bank-name">{{ $bank['name'] }}</div>
        <div class="account-number">{{ $bank['account_number'] }}</div>
        <div>a.n. {{ $bank['account_holder'] }}</div>
    </div>

    <div class="closing">
        @if ($invoice->notes)
            <strong>Catatan:</strong> {{ $invoice->notes }}<br><br>
        @endif
        Demikian invoice ini kami sampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.
    </div>

    <table class="signature-table">
        <tr>
            <td>
                Diterima oleh,<br>({{ $invoice->customer?->name ?? $invoice->quotation?->prospect_name }})
                <div class="signature-space"></div>
                (___________________________)
            </td>
            <td>
                Hormat kami,<br>{{ $company['name'] }}
                <div class="signature-space"></div>
                ({{ $invoice->createdBy?->name }})
            </td>
        </tr>
    </table>

    <div class="footer-note">Dokumen ini dibuat otomatis oleh sistem — No. {{ $invoice->invoice_number }}</div>
</body>
</html>
