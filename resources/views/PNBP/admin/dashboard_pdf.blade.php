<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Dashboard PNBP</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #111827; font-size: 10px; }
        h1 { font-size: 14px; margin: 0 0 4px; }
        h2 { font-size: 11px; margin: 10px 0 4px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #e5e7eb; padding: 4px; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Statistik PNBP</h1>
    <div class="muted">Filter: {{ $filter ?? 'Semua Data' }}</div>
    <div class="muted">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>

    <h2>Ringkasan</h2>
    <table>
        <thead>
            <tr>
                <th></th>
                <th class="right">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total File Ter-Upload</td>
                <td class="right">{{ number_format($totalFiles) }}</td>
            </tr>
            <tr>
                <td>Total Nilai Setor</td>
                <td class="right">Rp {{ number_format($financials->total_setor ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Nilai Billing</td>
                <td class="right">Rp {{ number_format($financials->total_billing ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Nilai LHP</td>
                <td class="right">Rp {{ number_format($financials->total_lhp ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Volume Produksi</td>
                <td class="right">{{ number_format($financials->total_volume ?? 0, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Top Wilayah</h2>
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

    <h2>Statistik SDH</h2>
    <table>
        <thead>
            <tr>
                <th>SDH</th>
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
