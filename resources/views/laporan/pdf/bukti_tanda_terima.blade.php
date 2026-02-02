<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bukti Tanda Terima Laporan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            padding: 20px 40px;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            font-size: 10pt;
            margin-top: 5px;
        }

        .title {
            text-align: center;
            margin: 30px 0;
        }

        .title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .title p {
            font-size: 11pt;
            margin-top: 5px;
        }

        .content {
            margin: 25px 0;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
        }

        .content table td {
            padding: 8px 5px;
            vertical-align: top;
        }

        .content table td:first-child {
            width: 35%;
            font-weight: bold;
        }

        .content table td:nth-child(2) {
            width: 5%;
            text-align: center;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
            padding-right: 50px;
        }

        .footer .date {
            margin-bottom: 60px;
        }

        .footer .signature {
            text-align: center;
            display: inline-block;
        }

        .footer .signature .line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin-bottom: 5px;
        }

        .note {
            margin-top: 40px;
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
        <p>Sistem Informasi Pelaporan Industri Kehutanan</p>
    </div>

    <div class="title">
        <h2>Bukti Tanda Terima Laporan</h2>
        <p>No. {{ $nomorBukti }}</p>
    </div>

    <div class="content">
        <p style="margin-bottom: 15px;">Dengan ini menyatakan telah menerima laporan dari:</p>
        <table>
            <tr>
                <td>Nama Industri</td>
                <td>:</td>
                <td>{{ $namaIndustri }}</td>
            </tr>
            <tr>
                <td>Penanggung Jawab</td>
                <td>:</td>
                <td>{{ $penanggungJawab }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $alamat }}</td>
            </tr>
            <tr>
                <td>Jenis Laporan</td>
                <td>:</td>
                <td>{{ $jenisLaporan }}</td>
            </tr>
            <tr>
                <td>Periode Laporan</td>
                <td>:</td>
                <td>{{ $periodeLaporan }}</td>
            </tr>
            <tr>
                <td>Tanggal Diterima</td>
                <td>:</td>
                <td>{{ $tanggalDiterima }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="date">{{ $tempatTanggal }}</div>
        <div class="signature">
            <p>Sistem SIMPEL-HUT</p>
            <div class="line"></div>
            <p><em>Dokumen ini digenerate otomatis</em></p>
        </div>
    </div>

    <div class="note">
        *Bukti ini sah dan dapat digunakan sebagai bukti pelaporan. Dokumen digenerate secara otomatis oleh sistem.
    </div>
</body>

</html>