<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TptkbExport
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
        $sheet->setCellValue('A1', 'Data TPTKB' . ($filterText ? ' berdasarkan ' . $filterText : ''));
        $sheet->mergeCells('A1:I3');
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
            'Nama Perusahaan',
            'Kabupaten',
            'Nomor Izin',
            'Sumber Bahan Baku',
            'Kapasitas Izin (m³)',
            'Masa Berlaku',
            'Status',
            'Tanggal'
        ];

        // Style header - HIJAU
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:I4')->applyFromArray([
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
            $sumberBahanBaku = $item->sumberBahanBaku->map(function($sumber) {
                $kapasitas = $sumber->pivot->kapasitas_izin ?? 0;
                return $sumber->nama . ' (' . number_format($kapasitas, 2) . ' m³)';
            })->implode(', ');

            $totalKapasitas = $item->sumberBahanBaku->sum('pivot.kapasitas_izin');

            $sheet->fromArray([
                $index + 1,
                $item->industri->nama ?? '-',
                $item->industri->kabupaten ?? '-',
                $item->industri->nomor_izin ?? '-',
                $sumberBahanBaku ?: '-',
                $totalKapasitas ?? 0,
                $item->masa_berlaku ? \Carbon\Carbon::parse($item->masa_berlaku)->format('d-m-Y') : '-',
                $item->industri->status ?? '-',
                $item->industri->tanggal ? \Carbon\Carbon::parse($item->industri->tanggal)->format('d-m-Y') : '-'
            ], null, 'A' . $row);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders
        $sheet->getStyle('A4:I' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Format number columns
        $sheet->getStyle('F5:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_TPTKB_' . date('Y-m-d_His') . '.xlsx';
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
        if (!empty($this->filters['sumber_bahan_baku'])) {
            $filterParts[] = 'Sumber Bahan Baku: ' . $this->filters['sumber_bahan_baku'];
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
