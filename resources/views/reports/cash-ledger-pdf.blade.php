<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .header { width: 100%; margin-bottom: 12px; }
        .header td { vertical-align: middle; }
        .logo { width: 70px; }
        .company { font-size: 14px; font-weight: 700; }
        .report-title { font-size: 13px; font-weight: 700; margin-top: 2px; }
        .meta { margin-top: 2px; font-size: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px 6px; }
        th { background: #f3f4f6; text-align: center; }
        td.num { text-align: right; }
        td.center { text-align: center; }
        .muted { color: #666; }
    </style>
</head>
<body>

<table class="header">
    <tr>
        <td class="logo">
            @php
                $logoSrc = $logoDataUri ?? null;
                if (empty($logoSrc)) {
                    $logoSrc = $logoFileUri ?? null;
                }
            @endphp

            @if(!empty($logoSrc))
                <img src="{{ $logoSrc }}" style="max-width: 70px; max-height: 70px;">
            @endif
        </td>
        <td>
            <div class="company">{{ $companyName }}</div>
            <div class="report-title">{{ $title }}</div>
            <div class="meta">{{ $periodLabel }}</div>
        </td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th style="width: 13%">Tanggal</th>
        <th style="width: 16%">Kategori</th>
        <th>Keterangan</th>
        <th style="width: 12%">Debit</th>
        <th style="width: 12%">Kredit</th>
        <th style="width: 12%">Saldo</th>
        <th style="width: 15%">Input oleh</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="center muted" colspan="5">Saldo awal</td>
        <td class="num">{{ number_format($openingBalance, 2, ',', '.') }}</td>
        <td class="center muted">-</td>
    </tr>

    @foreach($rows as $row)
        <tr>
            <td class="center">{{ $row['occurred_at'] }}</td>
            <td>{{ $row['category'] }}</td>
            <td>{{ $row['description'] }}</td>
            <td class="num">{{ $row['debit'] > 0 ? number_format($row['debit'], 2, ',', '.') : '' }}</td>
            <td class="num">{{ $row['credit'] > 0 ? number_format($row['credit'], 2, ',', '.') : '' }}</td>
            <td class="num">{{ number_format($row['balance'], 2, ',', '.') }}</td>
            <td>{{ $row['input_by'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
