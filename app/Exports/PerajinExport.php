<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PerajinExport
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
        $sheet->setCellValue('A1', 'Data Perajin' . ($filterText ? ' berdasarkan ' . $filterText : ''));
        $sheet->mergeCells('A1:G3');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '000000']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Set header kolom di row 4
        $headers = [
            'No',
            'Nama Perajin',
            'Nomor Izin',
            'Jenis Kerajinan',
            'Kapasitas Produksi (m³)',
            'Status',
            'Tanggal'
        ];

        // Style header - HIJAU
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:G4')->applyFromArray([
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
            $sheet->fromArray([
                $index + 1,
                $item->industri->nama ?? '-',
                $item->industri->nomor_izin ?? '-',
                $item->jenis_kerajinan ?? '-',
                $item->kapasitas_produksi ?? 0,
                $item->industri->status ?? '-',
                $item->industri->tanggal ? \Carbon\Carbon::parse($item->industri->tanggal)->format('d-m-Y') : '-'
            ], null, 'A' . $row);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders
        $sheet->getStyle('A4:G' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Format number columns
        $sheet->getStyle('E5:E' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Perajin_' . date('Y-m-d_His') . '.xlsx';
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
        
        if (!empty($this->filters['search'])) {
            $filterParts[] = 'Pencarian: ' . $this->filters['search'];
        }
        if (!empty($this->filters['jenis_kerajinan'])) {
            $filterParts[] = 'Jenis Kerajinan: ' . $this->filters['jenis_kerajinan'];
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
        
        return implode(', ', $filterParts);
    }
}
