<?php

/**
 * Script untuk generate template Excel untuk import data industri
 * Jalankan dengan: php generate_excel_templates.php
 */

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Pastikan folder templates ada
$templateDir = __DIR__ . '/public/templates';
if (!is_dir($templateDir)) {
    mkdir($templateDir, 0755, true);
}

// ============================================
// 1. TEMPLATE INDUSTRI PRIMER
// ============================================
echo "Membuat template Industri Primer...\n";

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template Industri Primer');

// Header columns (tanpa Pelaporan sesuai permintaan user)
$headers = [
    'Nama Perusahaan',
    'Alamat',
    'Kabupaten',
    'Latitude',
    'Longitude',
    'Penanggung Jawab',
    'Kontak',
    'Nomor Izin',
    'Tanggal SK',
    'Pemberi Izin',
    'Jenis Produksi',
    'Kapasitas Izin (m³/tahun)',
    'Status'
];

// Set headers
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    $sheet->getColumnDimension($col)->setWidth(20);
    $col++;
}

// Sample data
$sampleData = [
    [
        'PT Kayu Jaya Abadi',
        'Jl. Industri No. 123, Semarang',
        'Kota Semarang',
        '-6.9667',
        '110.4167',
        'Budi Santoso',
        '081234567890',
        'SK.123/2024',
        '2024-01-15',
        'Gubernur',
        'Kayu Lapis, Kayu Gergajian',
        '5000',
        'Aktif'
    ],
    [
        'CV Mebel Sejahtera',
        'Jl. Raya Kudus KM 5',
        'Kabupaten Kudus',
        '-6.8048',
        '110.8405',
        'Siti Aminah',
        '082345678901',
        'SK.456/2024',
        '2024-02-20',
        'Bupati/Walikota',
        'Mebel',
        '3000',
        'Aktif'
    ]
];

$row = 2;
foreach ($sampleData as $data) {
    $col = 'A';
    foreach ($data as $value) {
        $sheet->setCellValue($col . $row, $value);
        $sheet->getStyle($col . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $col++;
    }
    $row++;
}

// Freeze header row
$sheet->freezePane('A2');

$writer = new Xlsx($spreadsheet);
$writer->save($templateDir . '/template_industri_primer.xlsx');
echo "✓ Template Industri Primer berhasil dibuat\n";

// ============================================
// 2. TEMPLATE INDUSTRI SEKUNDER
// ============================================
echo "Membuat template Industri Sekunder...\n";

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template Industri Sekunder');

$headers = [
    'Nama Perusahaan',
    'Alamat',
    'Kabupaten',
    'Latitude',
    'Longitude',
    'Penanggung Jawab',
    'Kontak',
    'Nomor Izin',
    'Tanggal SK',
    'Pemberi Izin',
    'Jenis Produksi',
    'Kapasitas Izin (m³/tahun)',
    'Status'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    $sheet->getColumnDimension($col)->setWidth(20);
    $col++;
}

$sampleData = [
    [
        'PT Furniture Nusantara',
        'Jl. Raya Solo KM 10',
        'Kabupaten Sukoharjo',
        '-7.6833',
        '110.8333',
        'Ahmad Yani',
        '083456789012',
        'SK.789/2024',
        '2024-03-10',
        'Gubernur',
        'Furniture Kayu',
        '4500',
        'Aktif'
    ]
];

$row = 2;
foreach ($sampleData as $data) {
    $col = 'A';
    foreach ($data as $value) {
        $sheet->setCellValue($col . $row, $value);
        $sheet->getStyle($col . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $col++;
    }
    $row++;
}

$sheet->freezePane('A2');

$writer = new Xlsx($spreadsheet);
$writer->save($templateDir . '/template_industri_sekunder.xlsx');
echo "✓ Template Industri Sekunder berhasil dibuat\n";

// ============================================
// 3. TEMPLATE TPTKB
// ============================================
echo "Membuat template TPTKB...\n";

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template TPTKB');

$headers = [
    'Nama Perusahaan',
    'Alamat',
    'Kabupaten',
    'Latitude',
    'Longitude',
    'Penanggung Jawab',
    'Kontak',
    'Nomor Izin',
    'Tanggal SK',
    'Pemberi Izin',
    'Sumber Bahan Baku',
    'Kapasitas (m³/tahun)',
    'Masa Berlaku',
    'Status'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    $sheet->getColumnDimension($col)->setWidth(20);
    $col++;
}

$sampleData = [
    [
        'CV Kayu Bakar Sejahtera',
        'Jl. Industri Kayu No. 45',
        'Kabupaten Pati',
        '-6.7500',
        '111.0333',
        'Bambang Susilo',
        '084567890123',
        'SK.321/2024',
        '2024-04-01',
        'Bupati/Walikota',
        'Hutan Rakyat, Limbah Industri',
        '2000',
        '2025-04-01',
        'Aktif'
    ]
];

$row = 2;
foreach ($sampleData as $data) {
    $col = 'A';
    foreach ($data as $value) {
        $sheet->setCellValue($col . $row, $value);
        $sheet->getStyle($col . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $col++;
    }
    $row++;
}

$sheet->freezePane('A2');

$writer = new Xlsx($spreadsheet);
$writer->save($templateDir . '/template_tptkb.xlsx');
echo "✓ Template TPTKB berhasil dibuat\n";

// ============================================
// 4. TEMPLATE PERAJIN
// ============================================
echo "Membuat template Perajin...\n";

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template Perajin');

$headers = [
    'Nama Perajin',
    'Alamat',
    'Kabupaten',
    'Latitude',
    'Longitude',
    'Penanggung Jawab',
    'Kontak',
    'Nomor Izin',
    'Tanggal SK',
    'Status'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    $sheet->getColumnDimension($col)->setWidth(20);
    $col++;
}

$sampleData = [
    [
        'Perajin Mebel Pak Slamet',
        'Desa Karanganyar RT 02 RW 03',
        'Kabupaten Jepara',
        '-6.5889',
        '110.6686',
        'Slamet Riyadi',
        '085678901234',
        'SK.654/2024',
        '2024-05-15',
        'Aktif'
    ],
    [
        'Kerajinan Ukir Bu Siti',
        'Desa Tahunan RT 01 RW 02',
        'Kabupaten Jepara',
        '-6.5900',
        '110.6700',
        'Siti Nurjanah',
        '086789012345',
        'SK.655/2024',
        '2024-05-20',
        'Aktif'
    ]
];

$row = 2;
foreach ($sampleData as $data) {
    $col = 'A';
    foreach ($data as $value) {
        $sheet->setCellValue($col . $row, $value);
        $sheet->getStyle($col . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $col++;
    }
    $row++;
}

$sheet->freezePane('A2');

$writer = new Xlsx($spreadsheet);
$writer->save($templateDir . '/template_perajin.xlsx');
echo "✓ Template Perajin berhasil dibuat\n";

echo "\n===========================================\n";
echo "Semua template berhasil dibuat di folder:\n";
echo "$templateDir\n";
echo "===========================================\n";
