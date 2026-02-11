<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class IndustriSekunderExport
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters = [])
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title dengan informasi filter
        $filterText = $this->buildFilterText();
        $sheet->setCellValue('A1', 'Data Industri Sekunder' . ($filterText ? ' berdasarkan ' . $filterText : ''));
        $sheet->mergeCells('A1:K2');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '000000']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Tanggal dan jam ekspor
        $sheet->setCellValue('A3', 'Tanggal Ekspor: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '666666']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Set header kolom di row 4
        $headers = [
            'No',
            'Nama Perusahaan',
            'Kabupaten',
            'Nomor Izin',
            'Pemberi Izin',
            'Jenis Produksi',
            'Kapasitas Izin (m³)',
            'Total Nilai Investasi (Rp)',
            'Total Pegawai',
            'Status',
            'Tanggal'
        ];

        // Style header - HIJAU
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:K4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '16a34a']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Fill data mulai dari row 5
        $row = 5;
        foreach ($this->data as $index => $item) {
            $jenisProduksi = $item->jenisProduksi->map(function($jp) {
                return $jp->pivot->nama_custom ?? $jp->nama;
            })->implode(', ');

            $sheet->fromArray([
                $index + 1,
                $item->industri->nama ?? '-',
                $item->industri->kabupaten ?? '-',
                $item->industri->nomor_izin ?? '-',
                $item->pemberi_izin ?? '-',
                $jenisProduksi ?: '-',
                $item->kapasitas_izin ?? 0,
                $item->total_nilai_investasi ?? 0,
                $item->total_pegawai ?? 0,
                $item->industri->status ?? '-',
                $item->industri->tanggal ? \Carbon\Carbon::parse($item->industri->tanggal)->format('d-m-Y') : '-'
            ], null, 'A' . $row);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders
        $sheet->getStyle('A4:K' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Format number columns
        $sheet->getStyle('G5:G' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H5:H' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I5:I' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Industri_Sekunder_' . date('Y-m-d_His') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return [
            'file' => $temp_file,
            'filename' => $filename
        ];
    }

    private function buildFilterText()
    {
        $filterParts = [];
        
        if (!empty($this->filters['nama'])) {
            $filterParts[] = 'Nama: ' . $this->filters['nama'];
        }
        if (!empty($this->filters['kabupaten'])) {
            $filterParts[] = 'Kabupaten: ' . $this->filters['kabupaten'];
        }
        if (!empty($this->filters['jenis_produksi'])) {
            $filterParts[] = 'Jenis Produksi: ' . $this->filters['jenis_produksi'];
        }
        if (!empty($this->filters['pemberi_izin'])) {
            $filterParts[] = 'Pemberi Izin: ' . $this->filters['pemberi_izin'];
        }
        if (!empty($this->filters['status'])) {
            $filterParts[] = 'Status: ' . $this->filters['status'];
        }
        if (!empty($this->filters['tahun'])) {
            $filterParts[] = 'Tahun: ' . $this->filters['tahun'];
        }
        if (!empty($this->filters['bulan'])) {
            $bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $filterParts[] = 'Bulan: ' . $bulanNames[(int)$this->filters['bulan']];
        }
        if (!empty($this->filters['kapasitas'])) {
            $filterParts[] = 'Kapasitas: ' . $this->filters['kapasitas'] . ' m³';
        }
        
        return implode(', ', $filterParts);
    }
}
