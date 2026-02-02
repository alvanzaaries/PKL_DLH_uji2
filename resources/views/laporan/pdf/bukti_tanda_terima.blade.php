<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Tanda Terima Laporan PUHH</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            padding: 40px 50px;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11pt;
        }

        .title {
            text-align: center;
            margin: 40px 0;
        }

        .title h2 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .nomor {
            font-size: 12pt;
            margin-top: 10px;
        }

        .content {
            margin: 30px 0;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
        }

        .content table td {
            padding: 10px 5px;
            vertical-align: top;
        }

        .content table td:first-child {
            width: 30%;
            font-weight: bold;
        }

        .content table td:nth-child(2) {
            width: 5%;
            text-align: center;
        }

        .laporan-list {
            list-style-type: decimal;
            margin-left: 20px;
        }

        .laporan-list li {
            margin-bottom: 5px;
        }

        .signature-section {
            margin-top: 60px;
            display: table;
            width: 100%;
        }

        .signature-left,
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
        }

        .signature-box .title-text {
            font-weight: normal;
            margin-bottom: 80px;
        }

        .signature-box .name {
            border-top: 1px solid #000;
            padding-top: 5px;
            min-width: 180px;
            display: inline-block;
        }

        .date-place {
            text-align: right;
            margin-top: 40px;
            margin-bottom: 10px;
        }

        .note {
            margin-top: 50px;
            font-size: 9pt;
            font-style: italic;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Dinas Lingkungan Hidup</h1>
        <p>Provinsi Kalimantan Timur</p>
    </div>

    <div class="title">
        <h2>Tanda Terima Laporan PUHH</h2>
        <p class="nomor">No: {{ $nomorBukti }}</p>
    </div>

    <div class="content">
        <table>
            <tr>
                <td>Nama Perusahaan</td>
                <td>:</td>
                <td>{{ $namaPerusahaan }}</td>
            </tr>
            <tr>
                <td>Periode Laporan</td>
                <td>:</td>
                <td>{{ $periodeLaporan }}</td>
            </tr>
            <tr>
                <td>Jenis Laporan</td>
                <td>:</td>
                <td>
                    <ol class="laporan-list">
                        @foreach ($jenisLaporanList as $jenis)
                            <li>{{ $jenis }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            <tr>
                <td>Tanggal Diterima</td>
                <td>:</td>
                <td>{{ $tanggalDiterima }}</td>
            </tr>
        </table>
    </div>

    <div class="date-place">
        {{ $tempatTanggal }}
    </div>

    <div class="signature-section">
        <div class="signature-left">
            <div class="signature-box">
                <p class="title-text">Yang Menyerahkan,</p>
                <p class="name">{{ $penanggungJawab }}</p>
            </div>
        </div>
        <div class="signature-right">
            <div class="signature-box">
                <p class="title-text">Yang Menerima,</p>
                <p class="name">Petugas DLH</p>
            </div>
        </div>
    </div>

    <div class="note">
        *Dokumen ini adalah bukti sah penerimaan laporan PUHH dan digenerate secara otomatis oleh sistem SIMPEL-HUT.
    </div>
</body>

</html>