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
        .coverage-note { margin: 10px 0 20px; padding: 8px 10px; font-size: 10.5px; border: 1px solid #ccc; background: #f7f8fa; }
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
                <div class="company-detail">Tanggal: {{ $quotation->created_at->format('d F Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">SURAT PENAWARAN HARGA</div>
    <div class="doc-number">No. {{ $quotation->quotation_number }}</div>

    <table class="info-table">
        <tr>
            <td class="info-label">Kepada Yth.</td>
            <td>: {{ $quotation->prospect_name }}</td>
        </tr>
        @if ($quotation->prospect_company)
        <tr>
            <td class="info-label">Perusahaan</td>
            <td>: {{ $quotation->prospect_company }}</td>
        </tr>
        @endif
        @if ($quotation->prospect_address)
        <tr>
            <td class="info-label">Alamat</td>
            <td>: {{ $quotation->prospect_address }}</td>
        </tr>
        @endif
        @if ($quotation->prospect_phone)
        <tr>
            <td class="info-label">No. WhatsApp</td>
            <td>: {{ $quotation->prospect_phone }}</td>
        </tr>
        @endif
    </table>

    <p>Dengan hormat,</p>
    <p>Bersama ini kami sampaikan penawaran harga layanan internet dari {{ $company['name'] }} sebagai berikut:</p>

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
            @foreach ($quotation->items as $item)
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
            <td class="text-end">Rp {{ number_format($quotation->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>PPN {{ rtrim(rtrim(number_format($quotation->ppn_rate, 2, ',', '.'), '0'), ',') }}%</td>
            <td class="text-end">Rp {{ number_format($quotation->ppn_amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total (sudah PPN)</td>
            <td class="text-end">Rp {{ number_format($quotation->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if (! is_null($quotation->is_covered))
        <div class="coverage-note">
            @if ($quotation->is_covered)
                <strong>Status Coverage:</strong> Lokasi berada dalam jangkauan layanan kami (± {{ $quotation->nearest_distance_km }} km dari titik {{ $quotation->nearest_point_name }}).
            @else
                <strong>Status Coverage:</strong> Lokasi saat ini berada di luar radius jangkauan terdekat kami (± {{ $quotation->nearest_distance_km }} km dari titik {{ $quotation->nearest_point_name }}). Survei lokasi lanjutan mungkin diperlukan sebelum instalasi dapat dipastikan.
            @endif
        </div>
    @endif

    <div class="closing">
        Penawaran ini berlaku sampai dengan tanggal <strong>{{ $quotation->valid_until?->format('d F Y') ?? '-' }}</strong>.
        @if ($quotation->notes)
            <br><br><strong>Catatan:</strong> {{ $quotation->notes }}
        @endif
        <br><br>
        Demikian penawaran ini kami sampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.
    </div>

    <table class="signature-table">
        <tr>
            <td>
                Menyetujui,<br>({{ $quotation->prospect_name }})
                <div class="signature-space"></div>
                (___________________________)
            </td>
            <td>
                Hormat kami,<br>{{ $company['name'] }}
                <div class="signature-space"></div>
                ({{ $quotation->createdBy?->name }})
            </td>
        </tr>
    </table>

    <div class="footer-note">Dokumen ini dibuat otomatis oleh sistem — No. {{ $quotation->quotation_number }}</div>
</body>
</html>
