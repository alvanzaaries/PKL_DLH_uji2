<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Ringkasan PNBP</title>
    <style>
        {{ file_get_contents(public_path('css/pnbp/pdf-style.css')) }}
    </style>
</head>
<body>

    {{-- HEADER UTAMA --}}
    <div class="info-header">
        <h1>LAPORAN PNBP</h1>
        <p><strong>{{ strtoupper($filter ?? 'SEMUA DATA') }}</strong></p>
        <p class="muted">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
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
                <td>Total Dokumen Ter-Upload</td>
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
                <th class="right">TOTAL NILAI SETOR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>HASIL HUTAN KAYU</td>
                <td class="right">{{ number_format($volumeByCat['HASIL HUTAN KAYU'] ?? 0, 2, ',', '.') }} m3</td>
                <td class="right">Rp {{ number_format($setorByCat['HASIL HUTAN KAYU'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>HASIL HUTAN BUKAN KAYU (HHBK)</td>
                <td class="right">{{ number_format($volumeByCat['HASIL HUTAN BUKAN KAYU (HHBK)'] ?? 0, 2, ',', '.') }} Ton/Kg</td>
                <td class="right">Rp {{ number_format($setorByCat['HASIL HUTAN BUKAN KAYU (HHBK)'] ?? 0, 0, ',', '.') }}</td>           
            </tr>
            <tr>
                <td>HASIL HUTAN LAINNYA</td>
                <td class="right">{{ number_format($volumeByCat['HASIL HUTAN LAINNYA'] ?? 0, 2, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($setorByCat['HASIL HUTAN LAINNYA'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- BAGIAN 3: SEBARAN WILAYAH --}}
    <h2>SEBARAN PENERIMAAN PER WILAYAH</h2>
    <table>
        <thead>
            <tr>
                <th>WILAYAH</th>
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
    <h2>REKAPITULASI HASIL HUTAN</h2>
    <table>
        <thead>
            <tr>
                <th>JENIS HH</th>
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
                    <td colspan="3" class="center muted">Tidak ada data Hasil Hutan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

</body>
</html>
