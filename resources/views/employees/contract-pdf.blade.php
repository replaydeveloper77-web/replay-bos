<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #1a1a1a; line-height: 1.5; margin-bottom: 25px; }
        #header { text-align: center; border-bottom: 2px solid #0b1d3a; padding-bottom: 6px; margin-bottom: 10px; }
        #header img { width: 40px; height: 40px; margin-bottom: 4px; }
        .company-name { font-size: 14px; font-weight: bold; }
        .company-detail { font-size: 9px; color: #333; }
        #footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: right; font-size: 8.5px; color: #777; }
        #footer .page-num:after { content: "{{ $company['name'] }} — Halaman " counter(page); }
        .doc-title { text-align: center; font-size: 13px; font-weight: bold; text-decoration: underline; margin: 10px 0 2px; }
        .doc-number { text-align: center; font-size: 10px; color: #555; margin-bottom: 16px; }
        .party-table { width: 100%; margin-bottom: 4px; }
        .party-table td { padding: 1px 0; font-size: 10.5px; vertical-align: top; }
        .party-label { width: 150px; }
        .pasal-title { font-weight: bold; margin-top: 10px; margin-bottom: 3px; }
        p { margin: 3px 0; text-align: justify; }
        ol, ul { margin: 3px 0; padding-left: 18px; }
        li { margin-bottom: 3px; text-align: justify; }
        .signature-table { width: 100%; margin-top: 30px; }
        .signature-table td { width: 50%; text-align: center; font-size: 10.5px; vertical-align: top; }
        .signature-space { height: 60px; }
        @if ($isPreview ?? false)
            .preview-banner { background: #fff3cd; border: 1px solid #ffe69c; color: #664d03; text-align: center; padding: 4px; font-size: 10px; margin-bottom: 10px; }
        @endif
    </style>
</head>
<body>
    <div id="footer"><span class="page-num"></span></div>

    @if ($isPreview ?? false)
        <div class="preview-banner">PRATINJAU — belum disimpan, dokumen ini bukan dokumen final</div>
    @endif

    <div id="header">
        @if ($logoDataUri)
            <img src="{{ $logoDataUri }}"><br>
        @endif
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-detail">Akta Notaris : {{ $company['akta_notaris'] }}</div>
        <div class="company-detail">SK. Menkumham : {{ $company['sk_menkumham'] }}</div>
        <div class="company-detail">{{ $company['address'] }}</div>
        <div class="company-detail">Phone: {{ $company['phone'] }} Email: {{ $company['email'] }}</div>
    </div>

    <div class="doc-title">{{ $docTitle }}</div>
    <div class="doc-number">Nomor : {{ $docNumber }}</div>

    {!! $renderedBody !!}

    <table class="signature-table">
        <tr>
            <td>
                PIHAK PERTAMA
                <div class="signature-space"></div>
                <strong>{{ strtoupper($company['representative_name'] ?: '........................') }}</strong><br>
                {{ $company['representative_title'] ?: 'Direktur' }}
            </td>
            <td>
                PIHAK KEDUA
                <div class="signature-space"></div>
                {{ $employee->name }}
            </td>
        </tr>
    </table>
</body>
</html>
