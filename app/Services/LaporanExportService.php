<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanExportService
{
    private $jenisOptions = [
        'penerimaan_kayu_bulat' => 'Laporan Penerimaan Kayu Bulat',
        'penerimaan_kayu_olahan' => 'Laporan Penerimaan Kayu Olahan',
        'mutasi_kayu_bulat' => 'Laporan Mutasi Kayu Bulat (LMKB)',
        'mutasi_kayu_olahan' => 'Laporan Mutasi Kayu Olahan (LMKO)',
        'penjualan_kayu_olahan' => 'Laporan Penjualan Kayu Olahan',
    ];

    private $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    /**
     * Export rekap laporan ke Excel
     */
    public function exportRekap($items, $bulan, $tahun, $jenis, $filters = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Setup base layout (Title, Header, etc.)
        $this->setupBaseLayout($sheet, $bulan, $tahun, $jenis, null);

        // Header kolom dan data
        $this->writeHeaderAndData($sheet, $items, $jenis);

        // Generate Filename
        $filename = $this->generateFilename('Rekap', null, $bulan, $tahun, $jenis, $filters);

        $this->downloadExcel($spreadsheet, $filename);
    }

    /**
     * Export rekap tahunan dengan breakdown per bulan
     * Data: rows = groups (kabupaten/jenis), columns = 12 months
     */
    public function exportRekapTahunan($rekapData, $tahun, $kategori, $groupByLabel)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title
        $sheet->setCellValue('A1', 'REKAP TAHUNAN ' . strtoupper($kategori));
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Year
        $sheet->setCellValue('A2', 'Tahun: ' . $tahun);
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Tanggal ekspor
        $sheet->setCellValue('A3', 'Tanggal Ekspor: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A3:O3');
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getFont()->getColor()->setRGB('666666');

        // Headers (Row 4)
        $headers = ['No', $groupByLabel, 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Total'];
        $colIndex = 1;
        foreach ($headers as $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '4', $header);
            $colIndex++;
        }

        // Style headers
        $sheet->getStyle('A4:O4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        for ($i = 3; $i <= 15; $i++) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setWidth(12);
        }

        // Data
        $row = 5;
        $no = 1;
        $data = $rekapData['data'] ?? [];

        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['nama']);

            // Months (columns C-N = months 1-12)
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($bulan + 2);
                $value = $item['bulan'][$bulan] ?? 0;
                $sheet->setCellValue($colLetter . $row, number_format($value, 2, '.', ''));
            }

            // Total (column O)
            $sheet->setCellValue('O' . $row, number_format($item['total'], 2, '.', ''));

            // Style data row
            $sheet->getStyle('A' . $row . ':O' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // Right-align numbers
            $sheet->getStyle('C' . $row . ':O' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
            $no++;
        }

        // Grand Total Row
        $grandTotal = $rekapData['grand_total'] ?? [];
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($bulan + 2);
            $value = $grandTotal[$bulan] ?? 0;
            $sheet->setCellValue($colLetter . $row, number_format($value, 2, '.', ''));
        }
        $sheet->setCellValue('O' . $row, number_format($grandTotal['total'] ?? 0, 2, '.', ''));

        $sheet->getStyle('A' . $row . ':O' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
        ]);

        // Filename
        $kategoriStr = str_replace(' ', '_', $kategori);
        $groupByStr = str_replace(' ', '_', $groupByLabel);
        $filename = "Rekap_Tahunan_{$kategoriStr}_{$groupByStr}_{$tahun}.xlsx";

        $this->downloadExcel($spreadsheet, $filename);
    }

    /**
     * Export detail laporan ke Excel
     */
    public function exportDetail($items, $bulan, $tahun, $jenis, $companyName, $filters = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Setup base layout with Company Name
        $this->setupBaseLayout($sheet, $bulan, $tahun, $jenis, $companyName);

        // Header kolom dan data
        $this->writeHeaderAndData($sheet, $items, $jenis);

        // Generate Filename
        $filename = $this->generateFilename('Detail', $companyName, $bulan, $tahun, $jenis, $filters);

        $this->downloadExcel($spreadsheet, $filename);
    }

    /**
     * Helper: Setup Judul dan Layout Dasar
     */
    private function setupBaseLayout($sheet, $bulan, $tahun, $jenis, $companyName = null)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(25);

        $startRow = 1;

        if ($companyName) {
            // Baris 1: Nama Perusahaan
            $sheet->setCellValue('A1', strtoupper($companyName));
            $sheet->mergeCells('A1:I1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $startRow = 2;
        }

        // Baris Judul Laporan
        $sheet->setCellValue('A' . $startRow, $companyName ? strtoupper($this->jenisOptions[$jenis] ?? '') : 'REKAP ' . strtoupper($this->jenisOptions[$jenis] ?? ''));
        $sheet->mergeCells('A' . $startRow . ':I' . $startRow);
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris Bulan / Tahun
        $dateRow = $startRow + 1;
        $sheet->setCellValue('A' . $dateRow, ($this->namaBulan[$bulan] ?? $bulan) . ' / ' . $tahun);
        $sheet->mergeCells('A' . $dateRow . ':I' . $dateRow);
        $sheet->getStyle('A' . $dateRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $dateRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Tanggal ekspor
        $exportRow = $dateRow + 1;
        $sheet->setCellValue('A' . $exportRow, 'Tanggal Ekspor: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A' . $exportRow . ':I' . $exportRow);
        $sheet->getStyle('A' . $exportRow)->getFont()->setSize(10);
        $sheet->getStyle('A' . $exportRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $exportRow)->getFont()->getColor()->setRGB('666666');

        // Warning text
        $warnRow = $exportRow + 1;
        $sheet->setCellValue('A' . $warnRow, '* Jangan Ubah Struktur Kolom');
        $sheet->getStyle('A' . $warnRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
    }

    /**
     * Helper: Tulis Header dan Data (Core logic)
     */
    private function writeHeaderAndData($sheet, $items, $jenis)
    {
        // Header kolom - Baris 6 (fixed for now based on original code logic, adjusting relative to start isn't strictly needed if we assume standard layout)
        // Original code used absolute rows 6 and 7. Let's stick to that to minimize regression risk.
        $headerRow = 6;
        $columnNumberRow = 7;

        switch ($jenis) {
            case 'penerimaan_kayu_bulat':
                $headers = ['No', 'Perusahaan', 'Nomor Dokumen', 'Tanggal', 'Asal Kayu (Kabupaten)', 'Jenis Kayu', 'Jumlah Batang', 'Volume (m³)', 'Keterangan'];
                $widths = [null, null, 20, 15, 20, 18, 15, 15, 25]; // A and B set globally
                break;
            case 'penerimaan_kayu_olahan':
                $headers = ['No', 'Perusahaan', 'Nomor Dokumen', 'Tanggal', 'Asal Kayu', 'Jenis Olahan', 'Jumlah Keping', 'Volume (m³)', 'Keterangan'];
                $widths = [null, null, 20, 15, 20, 18, 15, 15, 25];
                break;
            case 'mutasi_kayu_bulat':
                $headers = ['No', 'Perusahaan', 'Jenis Kayu', 'Persediaan Awal (m³)', 'Penambahan (m³)', 'Penggunaan (m³)', 'Persediaan Akhir (m³)', 'Keterangan'];
                $widths = [null, null, 18, 20, 18, 18, 20, 25];
                break;
            case 'mutasi_kayu_olahan':
                $headers = ['No', 'Perusahaan', 'Jenis Olahan', 'Persediaan Awal (m³)', 'Penambahan (m³)', 'Penggunaan (m³)', 'Persediaan Akhir (m³)', 'Keterangan'];
                $widths = [null, null, 18, 20, 18, 18, 20, 25];
                break;
            case 'penjualan_kayu_olahan':
                $headers = ['No', 'Perusahaan', 'Nomor Dokumen', 'Tanggal', 'Tujuan Kirim', 'Jenis Olahan', 'Jumlah Keping', 'Volume (m³)', 'Keterangan'];
                $widths = [null, null, 20, 15, 20, 18, 15, 15, 25];
                break;
            default:
                return;
        }

        // Set Headers and Widths
        $colIndex = 1;
        foreach ($headers as $idx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . $headerRow, $header);

            if (isset($widths[$idx]) && $widths[$idx] !== null) {
                $sheet->getColumnDimension($colLetter)->setWidth($widths[$idx]);
            }
            $colIndex++;
        }

        // Style header
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = 'A' . $headerRow . ':' . $lastColumn . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Numbering row (1, 2, 3...)
        for ($col = 1; $col <= count($headers); $col++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($columnLetter . $columnNumberRow, $col);
        }
        $sheet->getStyle('A' . $columnNumberRow . ':' . $lastColumn . $columnNumberRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A8D08D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Write Data
        $row = 8;
        $no = 1;
        foreach ($items as $item) {
            $perusahaan = $item->laporan->industri->nama ?? '-';

            // Note: Ensuring date formatting and null handling matches original
            if (in_array($jenis, ['penerimaan_kayu_bulat', 'penerimaan_kayu_olahan', 'penjualan_kayu_olahan'])) {
                // Layout 9 columns
                $doc = $item->nomor_dokumen;
                $date = date('d/m/Y', strtotime($item->tanggal));
                $ket = $item->keterangan ?? '-';

                if ($jenis === 'penerimaan_kayu_bulat') {
                    $vals = [$no, $perusahaan, $doc, $date, $item->asal_kayu, $item->jenis_kayu, $item->jumlah_batang, $item->volume, $ket];
                } elseif ($jenis === 'penerimaan_kayu_olahan') {
                    $vals = [$no, $perusahaan, $doc, $date, $item->asal_kayu, $item->jenis_olahan, $item->jumlah_keping, $item->volume, $ket];
                } else { // penjualan
                    $vals = [$no, $perusahaan, $doc, $date, $item->tujuan_kirim, $item->jenis_olahan, $item->jumlah_keping, $item->volume, $ket];
                }
            } else {
                // Mutasi (8 columns)
                $ket = $item->keterangan ?? '-';
                if ($jenis === 'mutasi_kayu_bulat') {
                    $name = $item->jenis_kayu;
                } else {
                    $name = $item->jenis_olahan;
                }
                $vals = [$no, $perusahaan, $name, $item->persediaan_awal_volume, $item->penambahan_volume, $item->penggunaan_pengurangan_volume, $item->persediaan_akhir_volume, $ket];
            }

            // Flush row
            $c = 1;
            foreach ($vals as $v) {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . $row, $v);
                $c++;
            }

            // Align numbers
            if (count($headers) === 9) {
                $sheet->getStyle('G' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            } else {
                $sheet->getStyle('D' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $row++;
            $no++;
        }

        // Totals Row
        if (count($items) > 0) {
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            if (in_array($jenis, ['penerimaan_kayu_bulat'])) {
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->setCellValue('G' . $row, $items->sum('jumlah_batang'));
                $sheet->setCellValue('H' . $row, $items->sum('volume'));
                $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);
            } elseif (in_array($jenis, ['penerimaan_kayu_olahan', 'penjualan_kayu_olahan'])) {
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->setCellValue('G' . $row, $items->sum('jumlah_keping'));
                $sheet->setCellValue('H' . $row, $items->sum('volume'));
                $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);
            } else {
                // Mutasi
                $sheet->mergeCells('A' . $row . ':C' . $row);
                $sheet->setCellValue('D' . $row, $items->sum('persediaan_awal_volume'));
                $sheet->setCellValue('E' . $row, $items->sum('penambahan_volume'));
                $sheet->setCellValue('F' . $row, $items->sum('penggunaan_pengurangan_volume'));
                $sheet->setCellValue('G' . $row, $items->sum('persediaan_akhir_volume'));
                $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);
            }
        }
    }

    private function generateFilename($prefix, $companyName, $bulan, $tahun, $jenis, $filters)
    {
        $safeCompany = $companyName ? preg_replace('/[^A-Za-z0-9\-_.]/', '_', str_replace(' ', '_', $companyName)) : '';
        $jenisStr = str_replace(' ', '_', $this->jenisOptions[$jenis] ?? 'Laporan');

        $base = $prefix . ($safeCompany ? '_' . $safeCompany : '') . '_' . $jenisStr . '_' . ($this->namaBulan[$bulan] ?? $bulan) . '_' . $tahun;

        $filterSuffix = '';
        if (!empty($filters)) {
            $parts = [];
            foreach ($filters as $k => $v) {
                if ($v === null || $v === '' || $k === 'industri_id')
                    continue;
                $safe = preg_replace('/[^A-Za-z0-9\-_.]/', '_', str_replace(' ', '_', (string) $v));
                $parts[] = $k . '-' . $safe;
            }
            if (!empty($parts)) {
                $filterSuffix = '_' . implode('_', $parts);
            }
        }

        return $base . $filterSuffix . '.xlsx';
    }

    private function downloadExcel($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
