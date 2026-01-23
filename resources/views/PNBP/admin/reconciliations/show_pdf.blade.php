<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Rekonsiliasi - {{ $reconciliation->kph ?? 'PNBP' }}</title>
    <style>
        {{ file_get_contents(public_path('css/pnbp/pdf-style.css')) }}
    </style>
</head>
<body>

    {{-- HEADER UTAMA --}}
    <div class="info-header">
        <h1>RINGKASAN PNBP</h1>
        <p><strong>{{ strtoupper($reconciliation->kph ?? 'PROVINSI JAWA TENGAH') }}</strong></p>
        <p>TAHUN {{ $reconciliation->year }} — TRIWULAN {{ $reconciliation->quarter }}</p>
        <p class="muted">Dicetak pada: {{ $pageInfo['generated_at'] ?? date('d/m/Y H:i') }} | File Asli: {{ $reconciliation->original_filename }}</p>
    </div>

    {{-- BAGIAN 1: REKAPITULASI PENERIMAAN NEGARA --}}
    <h2>REKAPITULASI HASIL HUTAN</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 40%">URAIAN / KATEGORI</th>
                <th class="right" style="width: 20%">TOTAL VOLUME</th>
                <th class="right" style="width: 20%">TOTAL NILAI LHP (Rp)</th>
                <th class="right" style="width: 20%">TOTAL NILAI SETOR (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandVol = 0;
                $grandLhp = 0;
                $grandSetor = 0;
            @endphp

            @foreach($groupedData as $catName => $data)
                @php
                    $grandVol += $data['total_volume'];
                    $grandLhp += $data['total_lhp'];
                    $grandSetor += $data['total_setor'];
                @endphp
                <tr>
                    <td>{{ $catName }}</td>
                    <td class="right">{{ number_format($data['total_volume'], 2, '.', ',') }}</td>
                    <td class="right">Rp {{ number_format($data['total_lhp'], 0, '.', ',') }}</td>
                    <td class="right">Rp {{ number_format($data['total_setor'], 0, '.', ',') }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td>GRAND TOTAL</td>
                <td class="right">{{ number_format($grandVol, 2, '.', ',') }}</td>
                <td class="right">Rp {{ number_format($totalNilaiLhpOverride ?? $grandLhp, 0, '.', ',') }}</td>
                <td class="right">Rp {{ number_format($totalNilaiSetorOverride ?? $grandSetor, 0, '.', ',') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-bottom: 20px;"></div>

    {{-- BAGIAN 2: REKAPITULASI TAMBAHAN --}}
    
    <h2>REKAPITULASI BERDASARKAN JENIS HASIL HUTAN</h2>
    <table>
        <thead>
            <tr>
                <th>JENIS HASIL HUTAN</th>
                <th class="center" style="width: 10%">SATUAN</th>
                <th class="right" style="width: 20%">TOTAL VOLUME</th>
                <th class="right" style="width: 25%">TOTAL NILAI LHP (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statsJenis as $s)
                <tr>
                    <td>{{ $s->label }}</td>
                    <td class="center">{{ $s->satuan }}</td>
                    <td class="right">{{ number_format($s->total_volume, 2, '.', ',') }}</td>
                    <td class="right">Rp {{ number_format($s->total_nilai ?? 0, 0, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <h2>REKAPITULASI BERDASARKAN WILAYAH</h2>
    <table>
        <thead>
            <tr>
                <th>WILAYAH (KABUPATEN / KOTA)</th>
                <th class="center" style="width: 15%">FREKUENSI TRANS.</th>
                <th class="right" style="width: 20%">TOTAL VOLUME</th>
                <th class="right" style="width: 25%">TOTAL NILAI LHP (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statsWilayah as $s)
                <tr>
                    <td>{{ $s->label }}</td>
                    <td class="center">{{ $s->count }}</td>
                    <td class="right">{{ number_format($s->total_volume, 2, '.', ',') }}</td>
                    <td class="right">Rp {{ number_format($s->total_nilai ?? 0, 0, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-bottom: 20px;"></div>

    <h2>REKAPITULASI PENYETORAN BANK</h2>
    <table>
        <thead>
            <tr>
                <th>NAMA BANK</th>
                <th class="center" style="width: 15%">FREKUENSI TRANS.</th>
                <th class="right" style="width: 30%">TOTAL SETOR (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statsBank as $s)
                <tr>
                    <td>{{ $s->label }}</td>
                    <td class="center">{{ $s->count }}</td>
                    <td class="right">Rp {{ number_format($s->total_nilai ?? 0, 0, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
