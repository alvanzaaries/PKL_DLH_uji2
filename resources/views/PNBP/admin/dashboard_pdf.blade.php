<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Dashboard PNBP</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #111827; font-size: 10px; line-height: 1.3; }
        h1 { font-size: 14px; margin: 0 0 5px; font-weight: bold; text-align: center; text-transform: uppercase; }
        h2 { font-size: 11px; margin: 15px 0 6px; font-weight: bold; border-bottom: 2px solid #4b5563; padding-bottom: 4px; text-transform: uppercase; }
        h3 { font-size: 10px; margin: 12px 0 4px; font-weight: bold; color: #374151; }
        .muted { color: #6b7280; font-size: 9px; margin-bottom: 4px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #9ca3af; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        
        .right { text-align: right; }
        .center { text-align: center; }
        .total-row { background-color: #e5e7eb; font-weight: bold; }
        .page-break { page-break-after: always; }
        
        /* Helper for header info */
        .info-header { text-align: center; margin-bottom: 25px; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; }
        .info-header p { margin: 2px 0; font-size: 10px; }
    </style>
</head>
<body>

    {{-- HEADER UTAMA --}}
    <div class="info-header">
        <h1>LAPORAN PNBP</h1>
        <p><strong>{{ strtoupper($filter ?? 'SEMUA DATA') }}</strong></p>
        <p class="muted">Dicetak pada: {{ now()->format('d/m/Y H:i') }} | Sistem Pelaporan PNBP</p>
    </div>

    {{-- BAGIAN 1: RINGKASAN DATA --}}
    <h2>RINGKASAN</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 50%">INDIKATOR</th>
                <th class="right" style="width: 50%">NILAI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total File Rekonsiliasi</td>
                <td class="right">{{ number_format($totalFiles) }} Dokumen</td>
            </tr>
            <tr>
                <td>Total Volume Produksi (Akumulasi)</td>
                <td class="right">{{ number_format($financials->total_volume ?? 0, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Nilai LHP</td>
                <td class="right">Rp {{ number_format($financials->total_lhp ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Nilai Billing</td>
                <td class="right">Rp {{ number_format($financials->total_billing ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL NILAI SETOR (PNBP)</td>
                <td class="right">Rp {{ number_format($financials->total_setor ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- BAGIAN 2: REKAPITULASI HASIL HUTAN --}}
    <h2>REKAPITULASI HASIL HUTAN</h2>
    <table>
        <thead>
            <tr>
                <th>HASIL HUTAN</th>
                <th class="right">TOTAL VOLUME</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>HASIL HUTAN KAYU</td>
                <td class="right">{{ number_format($volumeByCat['HASIL HUTAN KAYU'] ?? 0, 2, ',', '.') }} m3</td>
            </tr>
            <tr>
                <td>HASIL HUTAN BUKAN KAYU (HHBK)</td>
                <td class="right">{{ number_format($volumeByCat['HASIL HUTAN BUKAN KAYU (HHBK)'] ?? 0, 2, ',', '.') }} Ton/Kg</td>
            </tr>
            <tr>
                <td>HASIL HUTAN LAINNYA</td>
                <td class="right">{{ number_format($volumeByCat['HASIL HUTAN LAINNYA'] ?? 0, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- BAGIAN 3: SEBARAN WILAYAH --}}
    <h2>SEBARAN PENERIMAAN PER WILAYAH</h2>
    <table>
        <thead>
            <tr>
                <th>WILAYAH / CABANG DINAS</th>
                <th class="right">TOTAL SETOR (Rp)</th>
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
                    <td colspan="2" class="center muted">Tidak ada data wilayah tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- BAGIAN 4: DETAIL JENIS SDH --}}
    @if(count($statsJenis) > 0)
    <h2>DETAIL JENIS SUMBER DAYA HUTAN (SDH)</h2>
    <table>
        <thead>
            <tr>
                <th>JENIS SDH</th>
                <th class="right">VOLUME</th>
                <th class="right">NILAI SETOR (Rp)</th>
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
                    <td colspan="3" class="center muted">Tidak ada data jenis SDH.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

</body>
</html>
