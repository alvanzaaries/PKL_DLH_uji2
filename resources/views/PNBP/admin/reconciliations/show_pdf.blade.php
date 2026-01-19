<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Rekonsiliasi</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #111827; font-size: 9px; }
        h1 { font-size: 14px; margin: 0 0 4px; }
        h2 { font-size: 11px; margin: 10px 0 4px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #e5e7eb; padding: 3px; text-align: left; }
        .right { text-align: right; }
        .page-break { page-break-before: always; }
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

    <h2>Total Volume per Satuan</h2>
    <table>
        <thead>
            <tr>
                <th>Satuan</th>
                <th class="right">Total Volume</th>
            </tr>
        </thead>
        <tbody>
            @forelse($totalPerSatuan as $t)
                <tr>
                    <td>{{ $t->satuan == '-' ? 'LAINNYA' : $t->satuan }}</td>
                    <td class="right">{{ rtrim(rtrim(number_format(($t->total_volume_final ?? $t->total_volume), 3, '.', ','), '0'), ',') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="muted">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Rekap Wilayah</h2>
    <table>
        <thead>
            <tr>
                <th>Wilayah</th>
                <th class="right">Volume</th>
                <th class="right">Nilai LHP</th>
                <th class="right">Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statsWilayah as $s)
                <tr>
                    <td>{{ $s->label }}</td>
                    <td class="right">{{ number_format($s->total_volume, 2, '.', ',') }}</td>
                    <td class="right">Rp {{ number_format($s->total_nilai ?? 0, 0, '.', ',') }}</td>
                    <td class="right">{{ $s->count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Rekap Bank Penyetor</h2>
    <table>
        <thead>
            <tr>
                <th>Bank</th>
                <th class="right">Total Setor</th>
                <th class="right">Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statsBank as $s)
                <tr>
                    <td>{{ $s->label }}</td>
                    <td class="right">Rp {{ number_format($s->total_nilai, 0, '.', ',') }}</td>
                    <td class="right">{{ $s->count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="muted">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
