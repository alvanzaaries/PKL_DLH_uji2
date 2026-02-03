<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Laporan Penatausahaan Hasil Hutan</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            padding: 20px;
            color: #000;
        }

        .container {
            background-color: white;
            width: 210mm;
            /* Ukuran A4 */
            min-height: 297mm;
            padding: 25mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
        }

        .header {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 40px;
            font-size: 18px;
            line-height: 1.5;
        }

        .form-group {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
        }

        .label {
            width: 180px;
            font-weight: bold;
        }

        .input-line {
            flex: 1;
            border: none;
            border-bottom: 1px dotted black;
            font-family: inherit;
            font-weight: bold;
            font-size: 14px;
            padding: 5px;
            outline: none;
            background: transparent;
        }

        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .checklist-table td,
        .checklist-table th {
            border: 1px solid black;
            padding: 8px;
        }

        .checkbox-cell {
            text-align: center;
            width: 50px;
            font-size: 18px;
        }

        .signature-section {
            margin-top: 60px;
            float: right;
            width: 350px;
            text-align: center;
        }

        .signature-name {
            margin-top: 80px;
            /* Ruang untuk tanda tangan */
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-rank {
            margin-top: 5px;
        }

        .footer-link {
            position: absolute;
            bottom: 25mm;
            left: 25mm;
            font-size: 12px;
            color: blue;
            text-decoration: underline;
        }

        /* CSS untuk tampilan saat di-print */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                width: 100%;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <div>TANDA TERIMA</div>
            <div>LAPORAN PENATAUSAHAAN HASIL HUTAN</div>
        </div>

        <div class="form-group">
            <label class="label">Nomor</label>
            <span>:</span>
            <div class="input-line">{{ $receiptId }}</div>
        </div>
        <div class="form-group">
            <label class="label">Tanggal</label>
            <span>:</span>
            <div class="input-line">{{ $tanggalTerakhir }}</div>
        </div>
        <div class="form-group">
            <label class="label">Nama Perusahaan</label>
            <span>:</span>
            <div class="input-line">{{ $industri->nama }}</div>
        </div>

        <div class="form-group">
            <label class="label">Periode Laporan</label>
            <span>:</span>
            <div class="input-line" style="text-transform: capitalize;">{{ $bulan_nama }} / {{ $tahun }}</div>
        </div>

        <table class="checklist-table">
            <thead>
                <tr style="background-color: #eee;">
                    <th>Jenis Laporan</th>
                    <th>Cek (√)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Mapping Jenis Laporan Sesuai Template -->
                <tr>
                    <td>Laporan Penerimaan Kayu Bulat</td>
                    <td class="checkbox-cell">
                        {{ in_array('Laporan Penerimaan Kayu Bulat', $jenisLaporanList) ? '√' : '' }}
                    </td>
                </tr>
                <tr>
                    <td>Laporan Mutasi Kayu Bulat (LMKB)</td>
                    <td class="checkbox-cell">
                        {{ in_array('Laporan Mutasi Kayu Bulat (LMKB)', $jenisLaporanList) ? '√' : '' }}
                    </td>
                </tr>
                <tr>
                    <td>Laporan Penerimaan Kayu Olahan</td>
                    <td class="checkbox-cell">
                        {{ in_array('Laporan Penerimaan Kayu Olahan', $jenisLaporanList) ? '√' : '' }}
                    </td>
                </tr>
                <tr>
                    <td>Laporan Mutasi Kayu Olahan (LMKO)</td>
                    <td class="checkbox-cell">
                        {{ in_array('Laporan Mutasi Kayu Olahan', $jenisLaporanList) ? '√' : '' }}
                    </td>
                </tr>
                <tr>
                    <td>Laporan Penjualan Kayu Olahan</td>
                    <td class="checkbox-cell">
                        {{ in_array('Laporan Penjualan Kayu Olahan', $jenisLaporanList) ? '√' : '' }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="form-group">
            <label class="label">Waktu verifikasi</label>
            <span>:</span>
            <div class="input-line">{{ \Carbon\Carbon::now()->translatedFormat('H:i, d F Y') }}</div>
        </div>

        <div class="signature-section">
            <div style="text-transform: uppercase;">{{ $pejabat->jabatan ?? 'Kepala Dinas Lingkungan Hidup' }}</div>

            <div style="margin-top: 20px; font-size: 12px; color: #888;">
                [Dokumen ini telah diverifikasi secara elektronik]
            </div>

            <div class="signature-name">{{ $pejabat->nama ?? 'Nama Pejabat' }}</div>
            <div class="signature-rank">{{ $pejabat->pangkat ?? '-' }}</div>
            @if(isset($pejabat->nip) && $pejabat->nip != '-')
                <div class="signature-nip">NIP. {{ $pejabat->nip }}</div>
            @endif
        </div>

        <div class="footer-link no-print">
            <a href="javascript:window.print()">Cetak Halaman Ini</a>
        </div>
    </div>

    <script>
        window.onload = function () {
            // window.print();
        }
    </script>
</body>

</html>