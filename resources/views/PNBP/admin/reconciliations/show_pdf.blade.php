<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Rekonsiliasi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 10px; }
        h1 { font-size: 16px; margin: 0 0 6px; }
        h2 { font-size: 12px; margin: 10px 0 6px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #e5e7eb; padding: 4px; text-align: left; }
        th { background: #f9fafb; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Detail Rekonsiliasi</h1>
    <div class="muted">File: {{ $reconciliation->original_filename }}</div>
    <div class="muted">Tahun {{ $reconciliation->year }} - Triwulan {{ $reconciliation->quarter }}</div>
    <div class="muted">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>

    <h2>Ringkasan</h2>
    <table>
        <thead>
            <tr>
                <th>Metric</th>
                <th class="right">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Nilai LHP</td>
                <td class="right">Rp {{ number_format(($totalNilaiLhpFinal ?? $statsJenis->sum('total_nilai')), 0, '.', ',') }}</td>
            </tr>
            <tr>
                <td>Total Nilai Setor</td>
                <td class="right">Rp {{ number_format(($baseTotalNilaiSetor ?? 0), 0, '.', ',') }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Rekap Jenis Hasil Hutan</h2>
    <table>
        <thead>
            <tr>
                <th>Jenis</th>
                <th class="right">Volume</th>
                <th class="right">Nilai LHP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statsJenis as $s)
                <tr>
                    <td>{{ $s->label }}</td>
                    <td class="right">{{ number_format($s->total_volume, 2, '.', ',') }} {{ $s->satuan }}</td>
                    <td class="right">Rp {{ number_format(($s->total_nilai ?? 0), 0, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Detail Transaksi ({{ $details->count() }} baris)</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Wilayah</th>
                <th>LHP No</th>
                <th>Jenis HH</th>
                <th class="right">Volume</th>
                <th>Sat</th>
                <th class="right">Nilai LHP</th>
                <th>Billing No</th>
                <th class="right">Nilai Billing</th>
                <th>NTPN</th>
                <th class="right">Nilai Setor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details as $detail)
                <tr>
                    <td>{{ $detail->no_urut ?? '-' }}</td>
                    <td>{{ $detail->wilayah }}</td>
                    <td>{{ $detail->lhp_no }}</td>
                    <td>{{ $detail->jenis_sdh }}</td>
                    <td class="right">{{ number_format($detail->volume, 2, '.', ',') }}</td>
                    <td>{{ $detail->satuan }}</td>
                    <td class="right">{{ number_format($detail->lhp_nilai, 0, '.', ',') }}</td>
                    <td>{{ $detail->billing_no }}</td>
                    <td class="right">{{ number_format($detail->billing_nilai ?? 0, 0, '.', ',') }}</td>
                    <td>{{ $detail->setor_ntpn }}</td>
                    <td class="right">{{ number_format($detail->setor_nilai ?? 0, 0, '.', ',') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="muted">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
