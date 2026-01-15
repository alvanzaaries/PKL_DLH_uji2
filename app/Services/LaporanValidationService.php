<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Storage;

class LaporanValidationService
{
    /**
     * Baca dan validasi Excel berdasarkan jenis laporan
     */
    public function readAndValidateExcel($filePath, $jenisLaporan)
    {
        // Baca file Excel menggunakan PhpSpreadsheet
        $spreadsheet = IOFactory::load(Storage::path($filePath));
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Convert ke array
        $sheet = $worksheet->toArray();
        
        // Validasi format berdasarkan jenis laporan
        switch ($jenisLaporan) {
            case 'Laporan Penerimaan Kayu Bulat':
                return $this->validateLaporanPenerimaanKayuBulat($sheet);
            
            case 'Laporan Mutasi Kayu Bulat (LMKB)':
                return $this->validateLaporanMutasiKayuBulat($sheet);
                
            case 'Laporan Penerimaan Kayu Olahan':
                return $this->validateLaporanPenerimaanKayuOlahan($sheet);
                
            case 'Laporan Mutasi Kayu Olahan (LMKO)':
                return $this->validateLaporanMutasiKayuOlahan($sheet);
                
            case 'Laporan Penjualan Kayu Olahan':
                return $this->validateLaporanPenjualanKayuOlahan($sheet);
            
            default:
                throw new \Exception('Jenis laporan tidak dikenali: ' . $jenisLaporan);
        }
    }

    /**
     * Validasi format Laporan Penerimaan Kayu Bulat
     * Header di row 7, Data dimulai row 8, kolom A (No) diabaikan
     * Kolom B-H: Nomor Dokumen | Tanggal | Asal Kayu | Jenis Kayu | Jumlah Batang | Volume | Keterangan
     */
    private function validateLaporanPenerimaanKayuBulat($sheet)
    {
        $expectedHeaders = ['Nomor Dokumen', 'Tanggal', 'Asal Kayu', 'Jenis Kayu', 'Jumlah Batang', 'Volume', 'Keterangan'];
        
        // Header di row 7 (index 6), skip kolom A (index 0)
        $headerRow = $sheet[6] ?? [];
        $headers = array_slice($headerRow, 1); // Skip kolom A (No)
        
        // Data dimulai dari row 8 (index 7)
        $dataRows = array_slice($sheet, 7);
        $errors = [];
        $validRows = [];
        
        // Validasi header
        if (count($headers) < 7) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 7 kolom: Nomor Dokumen, Tanggal, Asal Kayu, Jenis Kayu, Jumlah Batang, Volume, Keterangan');
        }
        
        // Validasi setiap baris data
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + 8; // Baris aktual di Excel (dimulai dari row 8)
            $row = array_slice($fullRow, 1); // Skip kolom A (No)
            $rowErrors = [];
            
            // Skip baris kosong
            if ($this->isEmptyRow($row)) {
                continue;
            }
            
            // Validasi Nomor Dokumen (kolom 0) - wajib
            if (empty($row[0])) {
                $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
            }
            
            // Validasi Tanggal (kolom 1) - wajib dan format
            if (empty($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
            } else {
                $tanggal = $this->parseExcelDate($row[1]);
                if (!$tanggal) {
                    $rowErrors[] = "Baris {$rowNumber}: Format tanggal tidak valid (gunakan format: DD/MM/YYYY atau YYYY-MM-DD)";
                }
            }
            
            // Validasi Asal Kayu (kolom 2) - wajib
            if (empty($row[2])) {
                $rowErrors[] = "Baris {$rowNumber}: Asal Kayu tidak boleh kosong";
            }
            
            // Validasi Jenis Kayu (kolom 3) - wajib
            if (empty($row[3])) {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Kayu tidak boleh kosong";
            }
            
            // Validasi Jumlah Batang (kolom 4) - wajib, harus angka
            if (empty($row[4]) && $row[4] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Batang tidak boleh kosong";
            } elseif (!is_numeric($row[4])) {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Batang harus berupa angka";
            } elseif ($row[4] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Batang tidak boleh negatif";
            }
            
            // Validasi Volume (kolom 5) - wajib, harus angka
            if (empty($row[5]) && $row[5] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
            } elseif (!is_numeric($row[5])) {
                $rowErrors[] = "Baris {$rowNumber}: Volume harus berupa angka";
            } elseif ($row[5] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh negatif";
            }
            
            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validRows[] = $row;
            }
        }
        
        return [
            'headers' => $expectedHeaders,
            'rows' => array_slice($validRows, 0, 10), // Preview 10 baris pertama
            'total' => count($validRows),
            'valid' => count($validRows),
            'errors' => $errors
        ];
    }

    /**
     * Validasi format Laporan Mutasi Kayu Bulat
     */
    private function validateLaporanMutasiKayuBulat($sheet)
    {
        $expectedHeaders = ['Jenis Kayu', 'Persediaan Awal', 'Penambahan', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'];
        
        $headerRow = $sheet[6] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, 7);
        $errors = [];
        $validRows = [];
        
        if (count($headers) < 6) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 6 kolom: Jenis Kayu, Persediaan Awal, Penambahan, Penggunaan/Pengurangan, Persediaan Akhir, Keterangan');
        }
        
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + 8;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];
            
            if ($this->isEmptyRow($row)) {
                continue;
            }
            
            if (empty($row[0])) {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Kayu tidak boleh kosong";
            }
            
            if (empty($row[1]) && $row[1] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh kosong";
            } elseif (!is_numeric($row[1]) || $row[1] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal harus berupa angka positif";
            }
            
            if (empty($row[2]) && $row[2] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penambahan tidak boleh kosong";
            } elseif (!is_numeric($row[2]) || $row[2] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penambahan harus berupa angka positif";
            }
            
            if (empty($row[3]) && $row[3] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh kosong";
            } elseif (!is_numeric($row[3]) || $row[3] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan harus berupa angka positif";
            }
            
            if (empty($row[4]) && $row[4] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh kosong";
            } elseif (!is_numeric($row[4]) || $row[4] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir harus berupa angka positif";
            }
            
            // Validasi logika: Persediaan Akhir = Persediaan Awal + Penambahan - Penggunaan
            if (is_numeric($row[1]) && is_numeric($row[2]) && is_numeric($row[3]) && is_numeric($row[4])) {
                $expectedAkhir = $row[1] + $row[2] - $row[3];
                if (abs($expectedAkhir - $row[4]) > 0.01) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak sesuai (seharusnya {$expectedAkhir})";
                }
            }
            
            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validRows[] = $row;
            }
        }
        
        return [
            'headers' => $expectedHeaders,
            'rows' => array_slice($validRows, 0, 10),
            'total' => count($validRows),
            'valid' => count($validRows),
            'errors' => $errors
        ];
    }
    
    /**
     * Validasi format Laporan Penerimaan Kayu Olahan
     */
    private function validateLaporanPenerimaanKayuOlahan($sheet)
    {
        $expectedHeaders = ['Nomor Dokumen', 'Tanggal', 'Asal Kayu', 'Jenis Produk', 'Jumlah Keping', 'Volume', 'Keterangan'];
        
        $headerRow = $sheet[6] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, 7);
        $errors = [];
        $validRows = [];
        
        if (count($headers) < 7) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 7 kolom: Nomor Dokumen, Tanggal, Asal Kayu, Jenis Produk, Jumlah Keping, Volume, Keterangan');
        }
        
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + 8;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];
            
            if ($this->isEmptyRow($row)) {
                continue;
            }
            
            if (empty($row[0])) {
                $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
            }
            
            if (empty($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
            } elseif (!$this->parseExcelDate($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Format tanggal tidak valid";
            }
            
            if (empty($row[2])) {
                $rowErrors[] = "Baris {$rowNumber}: Asal Kayu tidak boleh kosong";
            }
            
            if (empty($row[3])) {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
            }
            
            if (empty($row[4]) && $row[4] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh kosong";
            } elseif (!is_numeric($row[4]) || $row[4] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping harus berupa angka positif";
            }
            
            if (empty($row[5]) && $row[5] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
            } elseif (!is_numeric($row[5]) || $row[5] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Volume harus berupa angka positif";
            }
            
            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validRows[] = $row;
            }
        }
        
        return [
            'headers' => $expectedHeaders,
            'rows' => array_slice($validRows, 0, 10),
            'total' => count($validRows),
            'valid' => count($validRows),
            'errors' => $errors
        ];
    }
    
    /**
     * Validasi format Laporan Mutasi Kayu Olahan
     */
    private function validateLaporanMutasiKayuOlahan($sheet)
    {
        $expectedHeaders = ['Jenis Produk', 'Persediaan Awal', 'Penambahan', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'];
        
        $headerRow = $sheet[6] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, 7);
        $errors = [];
        $validRows = [];
        
        if (count($headers) < 6) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 6 kolom: Jenis Produk, Persediaan Awal, Penambahan, Penggunaan/Pengurangan, Persediaan Akhir, Keterangan');
        }
        
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + 8;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];
            
            if ($this->isEmptyRow($row)) {
                continue;
            }
            
            if (empty($row[0])) {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
            }
            
            if (empty($row[1]) && $row[1] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh kosong";
            } elseif (!is_numeric($row[1]) || $row[1] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal harus angka positif";
            }
            
            if (empty($row[2]) && $row[2] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penambahan tidak boleh kosong";
            } elseif (!is_numeric($row[2]) || $row[2] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penambahan harus angka positif";
            }
            
            if (empty($row[3]) && $row[3] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh kosong";
            } elseif (!is_numeric($row[3]) || $row[3] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan harus angka positif";
            }
            
            if (empty($row[4]) && $row[4] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh kosong";
            } elseif (!is_numeric($row[4]) || $row[4] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir harus angka positif";
            }
            
            // Validasi logika: Persediaan Akhir = Persediaan Awal + Penambahan - Penggunaan
            if (is_numeric($row[1]) && is_numeric($row[2]) && is_numeric($row[3]) && is_numeric($row[4])) {
                $expectedAkhir = $row[1] + $row[2] - $row[3];
                if (abs($expectedAkhir - $row[4]) > 0.01) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak sesuai (seharusnya {$expectedAkhir})";
                }
            }
            
            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validRows[] = $row;
            }
        }
        
        return [
            'headers' => $expectedHeaders,
            'rows' => array_slice($validRows, 0, 10),
            'total' => count($validRows),
            'valid' => count($validRows),
            'errors' => $errors
        ];
    }
    
    /**
     * Validasi format Laporan Penjualan Kayu Olahan
     */
    private function validateLaporanPenjualanKayuOlahan($sheet)
    {
        $expectedHeaders = ['Nomor Dokumen', 'Tanggal', 'Tujuan Kirim', 'Jenis Produk', 'Jumlah Keping', 'Volume', 'Keterangan'];
        
        $headerRow = $sheet[6] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, 7);
        $errors = [];
        $validRows = [];
        
        if (count($headers) < 7) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 7 kolom: Nomor Dokumen, Tanggal, Tujuan Kirim, Jenis Produk, Jumlah Keping, Volume, Keterangan');
        }
        
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + 8;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];
            
            if ($this->isEmptyRow($row)) {
                continue;
            }
            
            if (empty($row[0])) {
                $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
            }
            
            if (empty($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
            } elseif (!$this->parseExcelDate($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Format tanggal tidak valid";
            }
            
            if (empty($row[2])) {
                $rowErrors[] = "Baris {$rowNumber}: Tujuan Kirim tidak boleh kosong";
            }
            
            if (empty($row[3])) {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
            }
            
            if (empty($row[4]) && $row[4] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh kosong";
            } elseif (!is_numeric($row[4]) || $row[4] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping harus angka positif";
            }
            
            if (empty($row[5]) && $row[5] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
            } elseif (!is_numeric($row[5]) || $row[5] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Volume harus angka positif";
            }
            
            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validRows[] = $row;
            }
        }
        
        return [
            'headers' => $expectedHeaders,
            'rows' => array_slice($validRows, 0, 10),
            'total' => count($validRows),
            'valid' => count($validRows),
            'errors' => $errors
        ];
    }
    
    /**
     * Helper: Parse tanggal dari berbagai format Excel
     */
    private function parseExcelDate($value): ?string
    {
        if (empty($value)) return null;
        
        // Jika sudah dalam format tanggal Excel (numeric)
        if (is_numeric($value)) {
            try {
                $date = Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        
        // Coba parse string tanggal
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }
        
        return null;
    }

    /**
     * Helper: Cek apakah row kosong
     */
    private function isEmptyRow($row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && $cell !== '' && trim($cell) !== '') {
                return false;
            }
        }
        return true;
    }
}
