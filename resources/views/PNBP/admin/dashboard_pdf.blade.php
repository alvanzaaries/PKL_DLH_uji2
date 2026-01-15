<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Dashboard PNBP</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        .muted { color: #6b7280; }
        .grid { display: table; width: 100%; margin: 12px 0; }
        .card { display: table-cell; border: 1px solid #e5e7eb; padding: 8px; vertical-align: top; }
        .card + .card { border-left: none; }
        .label { font-size: 11px; color: #6b7280; }
        .value { font-size: 14px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; text-align: left; }
        th { background: #f9fafb; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Dashboard Statistik PNBP</h1>
    <div class="muted">Filter: {{ $filter ?? 'Semua Data' }}</div>
    <div class="muted">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>

    <div class="grid">
        <div class="card">
            <div class="label">Total File Rekonsiliasi</div>
            <div class="value">{{ number_format($totalFiles) }}</div>
        </div>
        <div class="card">
            <div class="label">Total Nilai Setor</div>
            <div class="value">Rp {{ number_format($financials->total_setor ?? 0, 0, ',', '.') }}</div>
            <div class="label">Billing: Rp {{ number_format($financials->total_billing ?? 0, 0, ',', '.') }}</div>
            <div class="label">LHP: Rp {{ number_format($financials->total_lhp ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="label">Total Volume Produksi</div>
            <div class="value">{{ number_format($financials->total_volume ?? 0, 2, ',', '.') }}</div>
        </div>
    </div>

    <h2 style="font-size:14px; margin: 10px 0 6px;">Top Wilayah</h2>
    <table>
        <thead>
            <tr>
                <th>Wilayah</th>
                <th class="right">Total Setor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topWilayah as $wil)
                <tr>
                    <td>{{ $wil->wilayah }}</td>
                    <td class="right">Rp {{ number_format($wil->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="muted">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2 style="font-size:14px; margin: 12px 0 6px;">Statistik per Jenis SDH</h2>
    <table>
        <thead>
            <tr>
                <th>Jenis SDH</th>
                <th class="right">Volume</th>
                <th class="right">Nilai Setor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statsJenis as $jenis)
                <tr>
                    <td>{{ $jenis->jenis_sdh }}</td>
                    <td class="right">{{ number_format($jenis->total_vol, 2, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($jenis->total_setor, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="muted">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
