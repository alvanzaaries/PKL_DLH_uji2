<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Create public/templates directory if not exists
$templatesDir = __DIR__ . '/public/templates';
if (!is_dir($templatesDir)) {
    mkdir($templatesDir, 0755, true);
}

// ========== INDUSTRI SEKUNDER TEMPLATE ==========
echo "Generating Industri Sekunder template...\n";

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set title (rows 1-3 empty for user info)
$sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA INDUSTRI SEKUNDER');
$sheet->mergeCells('A1:M1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'CATATAN: Satu baris = satu jenis produksi. Jika perusahaan punya 2 jenis produksi, buat 2 baris dengan nomor izin yang sama.');
$sheet->mergeCells('A2:M2');
$sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Headers on row 5
$headers = [
    'Nama Perusahaan', 'Alamat', 'Kabupaten', 'Latitude', 'Longitude',
    'Penanggung Jawab', 'Kontak', 'Nomor Izin', 'Tanggal SK',
    'Pemberi Izin', 'Jenis Produksi', 'Kapasitas Izin (m³/tahun)', 'Status'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '5', $header);
    $sheet->getStyle($col . '5')->getFont()->setBold(true);
    $sheet->getStyle($col . '5')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF4CAF50');
    $sheet->getStyle($col . '5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $col++;
}

// Add sample data (rows 6-8)
$sampleData = [
    ['PT Kayu Jaya', 'Jl. Industri No. 1', 'Semarang', '-7.0051', '110.4381', 'Budi Santoso', '081234567890', 'SK-001/2024', '2024-01-15', 'Gubernur Jawa Tengah', 'Kayu Lapis', '3000', 'Aktif'],
    ['PT Kayu Jaya', 'Jl. Industri No. 1', 'Semarang', '-7.0051', '110.4381', 'Budi Santoso', '081234567890', 'SK-001/2024', '2024-01-15', 'Gubernur Jawa Tengah', 'Veneer', '2000', 'Aktif'],
    ['PT Mebel Indah', 'Jl. Raya Solo Km 5', 'Solo', '-7.5561', '110.8316', 'Andi Wijaya', '082345678901', 'SK-002/2024', '2024-02-10', 'BKPM', 'Plywood', '5000', 'Aktif'],
];

$row = 6;
foreach ($sampleData as $data) {
    $col = 'A';
    foreach ($data as $value) {
        $sheet->setCellValue($col . $row, $value);
        $col++;
    }
    $row++;
}

// Auto-size columns
foreach (range('A', 'M') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save($templatesDir . '/template_industri_sekunder.xlsx');
echo "✓ Industri Sekunder template created\n";

// ========== TPTKB TEMPLATE ==========
echo "Generating TPTKB template...\n";

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA TPTKB');
$sheet->mergeCells('A1:N1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'CATATAN: Satu baris = satu sumber bahan baku. Jika perusahaan punya 2 sumber, buat 2 baris dengan nomor izin yang sama.');
$sheet->mergeCells('A2:N2');
$sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Headers on row 5
$headers = [
    'Nama Perusahaan', 'Alamat', 'Kabupaten', 'Latitude', 'Longitude',
    'Penanggung Jawab', 'Kontak', 'Nomor Izin', 'Tanggal SK',
    'Pemberi Izin', 'Sumber Bahan Baku', 'Kapasitas (m³/tahun)', 'Masa Berlaku', 'Status'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '5', $header);
    $sheet->getStyle($col . '5')->getFont()->setBold(true);
    $sheet->getStyle($col . '5')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF2196F3');
    $sheet->getStyle($col . '5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $col++;
}

// Add sample data
$sampleData = [
    ['PT Hutan Lestari', 'Jl. Kehutanan No. 10', 'Semarang', '-7.0051', '110.4381', 'Bambang Susilo', '081234567890', 'SK-TPT-001/2024', '2024-01-15', 'Dinas Kehutanan', 'Hutan Alam', '3000', '2029-01-15', 'Aktif'],
    ['PT Hutan Lestari', 'Jl. Kehutanan No. 10', 'Semarang', '-7.0051', '110.4381', 'Bambang Susilo', '081234567890', 'SK-TPT-001/2024', '2024-01-15', 'Dinas Kehutanan', 'Hutan Tanaman', '2000', '2029-01-15', 'Aktif'],
    ['CV Kayu Makmur', 'Jl. Industri Raya 25', 'Solo', '-7.5561', '110.8316', 'Siti Nurhaliza', '082345678901', 'SK-TPT-002/2024', '2024-02-10', 'Dinas Kehutanan', 'Hutan Rakyat', '1500', '2029-02-10', 'Aktif'],
];

$row = 6;
foreach ($sampleData as $data) {
    $col = 'A';
    foreach ($data as $value) {
        $sheet->setCellValue($col . $row, $value);
        $col++;
    }
    $row++;
}

// Auto-size columns
foreach (range('A', 'N') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save($templatesDir . '/template_tptkb.xlsx');
echo "✓ TPTKB template created\n";

// ========== INDUSTRI PRIMER TEMPLATE ==========
echo "Generating Industri Primer template...\n";

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA INDUSTRI PRIMER');
$sheet->mergeCells('A1:M1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'CATATAN: Satu baris = satu jenis produksi. Jika perusahaan punya 2 jenis produksi, buat 2 baris dengan nomor izin yang sama.');
$sheet->mergeCells('A2:M2');
$sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Headers on row 5
$headers = [
    'Nama Perusahaan', 'Alamat', 'Kabupaten', 'Latitude', 'Longitude',
    'Penanggung Jawab', 'Kontak', 'Nomor Izin', 'Tanggal SK',
    'Pemberi Izin', 'Jenis Produksi', 'Kapasitas Izin (m³/tahun)', 'Status'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '5', $header);
    $sheet->getStyle($col . '5')->getFont()->setBold(true);
    $sheet->getStyle($col . '5')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF9C27B0');
    $sheet->getStyle($col . '5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $col++;
}

// Add sample data (multi-row format)
$sampleData = [
    ['PT Kayu Prima', 'Jl. Industri No. 5', 'Semarang', '-7.0051', '110.4381', 'Ahmad Yani', '081234567890', 'SK-P-001/2024', '2024-01-15', 'Gubernur Jawa Tengah', 'Gergajian', '3000', 'Aktif'],
    ['PT Kayu Prima', 'Jl. Industri No. 5', 'Semarang', '-7.0051', '110.4381', 'Ahmad Yani', '081234567890', 'SK-P-001/2024', '2024-01-15', 'Gubernur Jawa Tengah', 'Veneer', '2000', 'Aktif'],
    ['CV Plywood Jaya', 'Jl. Raya Solo Km 10', 'Solo', '-7.5561', '110.8316', 'Siti Aminah', '082345678901', 'SK-P-002/2024', '2024-02-10', 'BKPM', 'Plywood', '8000', 'Aktif'],
];

$row = 6;
foreach ($sampleData as $data) {
    $col = 'A';
    foreach ($data as $value) {
        $sheet->setCellValue($col . $row, $value);
        $col++;
    }
    $row++;
}

// Auto-size columns
foreach (range('A', 'M') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save($templatesDir . '/template_industri_primer.xlsx');
echo "✓ Industri Primer template created\n";

// ========== PERAJIN TEMPLATE ==========
echo "Generating Perajin template...\n";

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA PERAJIN');
$sheet->mergeCells('A1:J1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Headers on row 5
$headers = [
    'Nama Perajin', 'Alamat', 'Kabupaten', 'Latitude', 'Longitude',
    'Penanggung Jawab', 'Kontak', 'Nomor Izin', 'Tanggal SK', 'Status'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '5', $header);
    $sheet->getStyle($col . '5')->getFont()->setBold(true);
    $sheet->getStyle($col . '5')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFFF9800');
    $sheet->getStyle($col . '5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $col++;
}

// Add sample data
$sampleData = [
    ['Perajin Mebel Jati', 'Jl. Kerajinan No. 15', 'Semarang', '-7.0051', '110.4381', 'Budi Santoso', '081234567890', 'SK-PR-001/2024', '2024-01-15', 'Aktif'],
    ['Ukir Kayu Indah', 'Jl. Seni Rupa 20', 'Solo', '-7.5561', '110.8316', 'Dewi Lestari', '082345678901', 'SK-PR-002/2024', '2024-02-10', 'Aktif'],
];

$row = 6;
foreach ($sampleData as $data) {
    $col = 'A';
    foreach ($data as $value) {
        $sheet->setCellValue($col . $row, $value);
        $col++;
    }
    $row++;
}

// Auto-size columns
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save($templatesDir . '/template_perajin.xlsx');
echo "✓ Perajin template created\n";

echo "\nTemplates generated successfully in: $templatesDir\n";
echo "- template_industri_primer.xlsx\n";
echo "- template_industri_sekunder.xlsx\n";
echo "- template_tptkb.xlsx\n";
echo "- template_perajin.xlsx\n";
