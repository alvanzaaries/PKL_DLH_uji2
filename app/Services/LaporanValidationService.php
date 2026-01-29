<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LaporanValidationService
{
    // Sheet layout constants (0-based indexes)
    private const SHEET_HEADER_ROW = 14; // header is on Excel row 15
    private const SHEET_DATA_START = 15; // data starts on Excel row 16
    /**
     * Baca dan validasi Excel berdasarkan jenis laporan
     */
    public function readAndValidateExcel($filePath, $jenisLaporan)
    {
        // Baca file Excel menggunakan PhpSpreadsheet
        $spreadsheet = IOFactory::load(Storage::path($filePath));
        $worksheet = $spreadsheet->getActiveSheet();

        // Convert ke array - ambil raw values (tidak ter-format) sehingga kita mendapat nilai numerik asli dari sel
        // toArray signature: toArray($nullValue=null, $calculateFormulas=true, $formatData=true, $returnCellRef=false)
        // set $formatData = false untuk mencegah PhpSpreadsheet mengembalikan string ter-format (mis. "2,789.71")
        $sheet = $worksheet->toArray(null, true, false, false);

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
     * Validasi data manual input berdasarkan jenis laporan
     * Mengubah array manual_data menjadi format yang sama dengan Excel validation
     */
    public function validateManualData($manualData, $jenisLaporan)
    {
        // Define field mappings for each report type
        $fieldMappings = [
            'Laporan Penerimaan Kayu Bulat' => [
                'headers' => ['Nomor Dokumen', 'Tanggal', 'Asal Kayu', 'Jenis Kayu', 'Jumlah Batang', 'Volume', 'Keterangan'],
                'fields' => ['nomor_dokumen', 'tanggal', 'asal_kayu', 'jenis_kayu', 'jumlah_batang', 'volume', 'keterangan'],
                'required' => ['nomor_dokumen', 'tanggal', 'asal_kayu', 'jenis_kayu', 'jumlah_batang', 'volume'],
                'numeric' => ['jumlah_batang', 'volume'],
                'date' => ['tanggal']
            ],
            'Laporan Mutasi Kayu Bulat (LMKB)' => [
                'headers' => ['Jenis Kayu', 'Persediaan Awal', 'Penambahan', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'],
                'fields' => ['jenis_kayu', 'persediaan_awal_volume', 'penambahan_volume', 'penggunaan_pengurangan_volume', 'persediaan_akhir_volume', 'keterangan'],
                'required' => ['jenis_kayu', 'persediaan_awal_volume', 'penambahan_volume', 'penggunaan_pengurangan_volume', 'persediaan_akhir_volume'],
                'numeric' => ['persediaan_awal_volume', 'penambahan_volume', 'penggunaan_pengurangan_volume', 'persediaan_akhir_volume'],
                'validate_logic' => true
            ],
            'Laporan Penerimaan Kayu Olahan' => [
                'headers' => ['Nomor Dokumen', 'Tanggal', 'Asal Kayu', 'Jenis Produk', 'Jumlah Keping', 'Volume', 'Keterangan'],
                'fields' => ['nomor_dokumen', 'tanggal', 'asal_kayu', 'jenis_olahan', 'jumlah_keping', 'volume', 'keterangan'],
                'required' => ['nomor_dokumen', 'tanggal', 'asal_kayu', 'jenis_olahan', 'jumlah_keping', 'volume'],
                'numeric' => ['jumlah_keping', 'volume'],
                'date' => ['tanggal']
            ],
            'Laporan Mutasi Kayu Olahan (LMKO)' => [
                'headers' => ['Jenis Produk', 'Persediaan Awal', 'Penambahan', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'],
                'fields' => ['jenis_olahan', 'persediaan_awal_volume', 'penambahan_volume', 'penggunaan_pengurangan_volume', 'persediaan_akhir_volume', 'keterangan'],
                'required' => ['jenis_olahan', 'persediaan_awal_volume', 'penambahan_volume', 'penggunaan_pengurangan_volume', 'persediaan_akhir_volume'],
                'numeric' => ['persediaan_awal_volume', 'penambahan_volume', 'penggunaan_pengurangan_volume', 'persediaan_akhir_volume'],
                'validate_logic' => true
            ],
            'Laporan Penjualan Kayu Olahan' => [
                'headers' => ['Nomor Dokumen', 'Tanggal', 'Tujuan Kirim', 'Jenis Produk', 'Jumlah Keping', 'Volume', 'Keterangan'],
                'fields' => ['nomor_dokumen', 'tanggal', 'tujuan_kirim', 'jenis_olahan', 'jumlah_keping', 'volume', 'keterangan'],
                'required' => ['nomor_dokumen', 'tanggal', 'tujuan_kirim', 'jenis_olahan', 'jumlah_keping', 'volume'],
                'numeric' => ['jumlah_keping', 'volume'],
                'date' => ['tanggal']
            ],
        ];

        if (!isset($fieldMappings[$jenisLaporan])) {
            throw new \Exception('Jenis laporan tidak dikenali: ' . $jenisLaporan);
        }

        $mapping = $fieldMappings[$jenisLaporan];
        $errors = [];
        $allRows = [];
        $validCount = 0;

        // Process each row of manual data
        foreach ($manualData as $rowIndex => $rowData) {
            $rowNumber = $rowIndex; // Use the row index from manual input
            $rowErrors = [];
            $cells = [];

            // Build cells array in the same order as headers
            foreach ($mapping['fields'] as $fieldName) {
                $value = $rowData[$fieldName] ?? '';
                $cells[] = $value;
            }

            // Validate required fields
            foreach ($mapping['required'] as $requiredField) {
                $fieldIndex = array_search($requiredField, $mapping['fields']);
                $fieldLabel = $mapping['headers'][$fieldIndex];
                $value = trim((string) ($rowData[$requiredField] ?? ''));

                if ($value === '') {
                    $rowErrors[] = "Baris {$rowNumber}: {$fieldLabel} tidak boleh kosong";
                }
            }

            // Validate numeric fields
            if (isset($mapping['numeric'])) {
                foreach ($mapping['numeric'] as $numericField) {
                    $fieldIndex = array_search($numericField, $mapping['fields']);
                    $fieldLabel = $mapping['headers'][$fieldIndex];
                    $value = $rowData[$numericField] ?? '';

                    if (trim((string) $value) !== '') {
                        if (!is_numeric($value)) {
                            $rowErrors[] = "Baris {$rowNumber}: {$fieldLabel} harus berupa angka";
                        } elseif ((float) $value < 0) {
                            $rowErrors[] = "Baris {$rowNumber}: {$fieldLabel} tidak boleh negatif";
                        }
                    }
                }
            }

            // Validate date fields
            if (isset($mapping['date'])) {
                foreach ($mapping['date'] as $dateField) {
                    $fieldIndex = array_search($dateField, $mapping['fields']);
                    $fieldLabel = $mapping['headers'][$fieldIndex];
                    $value = $rowData[$dateField] ?? '';

                    if (trim((string) $value) !== '') {
                        $parsedDate = $this->parseManualDate($value);
                        if (!$parsedDate) {
                            $rowErrors[] = "Baris {$rowNumber}: {$fieldLabel} format tidak valid (gunakan format: YYYY-MM-DD)";
                        } else {
                            // Format for preview (DD/MM/YYYY)
                            $d = \DateTime::createFromFormat('Y-m-d', $parsedDate);
                            if ($d !== false) {
                                $cells[$fieldIndex] = $d->format('d/m/Y');
                            }
                        }
                    }
                }
            }

            // Validate logic for mutation reports
            if (isset($mapping['validate_logic']) && $mapping['validate_logic']) {
                $persediaanAwal = (float) ($rowData[$mapping['fields'][1]] ?? 0);
                $penambahan = (float) ($rowData[$mapping['fields'][2]] ?? 0);
                $penggunaan = (float) ($rowData[$mapping['fields'][3]] ?? 0);
                $persediaanAkhir = (float) ($rowData[$mapping['fields'][4]] ?? 0);

                $expectedAkhir = $persediaanAwal + $penambahan - $penggunaan;
                if (abs($expectedAkhir - $persediaanAkhir) > 0.01) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak sesuai (seharusnya {$expectedAkhir})";
                }
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validCount++;
            }

            $allRows[] = ['cells' => $cells, 'source_row' => $rowNumber];
        }

        return [
            'headers' => $mapping['headers'],
            'rows' => $allRows,
            'total' => count($allRows),
            'valid' => $validCount,
            'errors' => $errors
        ];
    }

    /**
     * Helper: Parse tanggal dari input manual (format HTML date input: YYYY-MM-DD)
     */
    private function parseManualDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $s = trim((string) $value);

        // HTML date input returns YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) {
            $d = \DateTime::createFromFormat('Y-m-d', $s);
            if ($d !== false) {
                return $d->format('Y-m-d');
            }
        }

        return null;
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
        $headerRow = $sheet[self::SHEET_HEADER_ROW] ?? [];
        $headers = array_slice($headerRow, 1); // Skip kolom A (No)

        // Data dimulai dari row 8 (index 7)
        $dataRows = array_slice($sheet, self::SHEET_DATA_START);
        $errors = [];
        $allRows = [];
        $validCount = 0;

        // Validasi header
        if (count($headers) < 7) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 7 kolom: Nomor Dokumen, Tanggal, Asal Kayu, Jenis Kayu, Jumlah Batang, Volume, Keterangan');
        }

        // Validasi setiap baris data
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + self::SHEET_DATA_START + 1; // Baris aktual di Excel
            $row = array_slice($fullRow, 1); // Skip kolom A (No)
            $rowErrors = [];

            // Skip baris kosong
            if ($this->isEmptyRow($row)) {
                continue;
            }

            // Validasi Nomor Dokumen (kolom 0) - wajib
            if (trim((string) ($row[0] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
            }

            // Validasi Tanggal (kolom 1) - wajib dan format
            if (trim((string) ($row[1] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
            } else {
                $tanggal = $this->parseExcelDate($row[1]);
                if (!$tanggal) {
                    $rowErrors[] = "Baris {$rowNumber}: Format tanggal tidak valid (gunakan format: DD/MM/YYYY atau YYYY-MM-DD)";
                } else {
                    // Untuk preview, tampilkan tanggal dalam format yang mudah dibaca (DD/MM/YYYY)
                    $d = \DateTime::createFromFormat('Y-m-d', $tanggal);
                    if ($d !== false) {
                        $row[1] = $d->format('d/m/Y');
                    }
                }
            }

            // Validasi Asal Kayu (kolom 2) - wajib
            if (trim((string) ($row[2] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Asal Kayu tidak boleh kosong";
            }

            // Validasi Jenis Kayu (kolom 3) - wajib
            if (trim((string) ($row[3] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Kayu tidak boleh kosong";
            }

            // Validasi Jumlah Batang (kolom 4) - wajib, harus angka
            if (trim((string) ($row[4] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Batang tidak boleh kosong";
            } else {
                $jumlahBatang = $this->parseExcelNumber($row[4]);
                if ($jumlahBatang === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Batang harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234 atau 1234)";
                } elseif ($jumlahBatang < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Batang tidak boleh negatif";
                }
            }

            // Validasi Volume (kolom 5) - wajib, harus angka
            if (trim((string) ($row[5] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
            } else {
                $volume = $this->parseExcelNumber($row[5]);
                if ($volume === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Volume harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($volume < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh negatif";
                }
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validCount++;
            }

            // Tambahkan semua row, baik valid maupun invalid; sertakan nomor baris sumber Excel agar bisa dipetakan ke nomor sementara
            $allRows[] = ['cells' => $row, 'source_row' => $rowNumber];
        }

        return [
            'headers' => $expectedHeaders,
            'rows' => $allRows,
            'total' => count($allRows),
            'valid' => $validCount,
            'errors' => $errors
        ];
    }

    /**
     * Validasi format Laporan Mutasi Kayu Bulat
     */
    private function validateLaporanMutasiKayuBulat($sheet)
    {
        $expectedHeaders = ['Jenis Kayu', 'Persediaan Awal', 'Penambahan', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'];

        $headerRow = $sheet[self::SHEET_HEADER_ROW] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, self::SHEET_DATA_START);
        $errors = [];
        $allRows = [];
        $validCount = 0;

        if (count($headers) < 6) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 6 kolom: Jenis Kayu, Persediaan Awal, Penambahan, Penggunaan/Pengurangan, Persediaan Akhir, Keterangan');
        }

        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + self::SHEET_DATA_START + 1;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];

            if ($this->isEmptyRow($row)) {
                continue;
            }

            if (trim((string) ($row[0] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Kayu tidak boleh kosong";
            }

            if (trim((string) ($row[1] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh kosong";
            } else {
                $persediaanAwal = $this->parseExcelNumber($row[1]);
                if ($persediaanAwal === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($persediaanAwal < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh negatif";
                }
            }

            if (trim((string) ($row[2] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Penambahan tidak boleh kosong";
            } else {
                $penambahan = $this->parseExcelNumber($row[2]);
                if ($penambahan === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Penambahan harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($penambahan < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Penambahan tidak boleh negatif";
                }
            }

            if (trim((string) ($row[3] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh kosong";
            } else {
                $penggunaan = $this->parseExcelNumber($row[3]);
                if ($penggunaan === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($penggunaan < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh negatif";
                }
            }

            if (trim((string) ($row[4] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh kosong";
            } else {
                $persediaanAkhir = $this->parseExcelNumber($row[4]);
                if ($persediaanAkhir === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($persediaanAkhir < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh negatif";
                }
            }

            // Validasi logika: Persediaan Akhir = Persediaan Awal + Penambahan - Penggunaan
            $persediaanAwal = $this->parseExcelNumber($row[1]);
            $penambahan = $this->parseExcelNumber($row[2]);
            $penggunaan = $this->parseExcelNumber($row[3]);
            $persediaanAkhir = $this->parseExcelNumber($row[4]);

            if ($persediaanAwal !== null && $penambahan !== null && $penggunaan !== null && $persediaanAkhir !== null) {
                $expectedAkhir = $persediaanAwal + $penambahan - $penggunaan;
                if (abs($expectedAkhir - $persediaanAkhir) > 0.01) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak sesuai (seharusnya {$expectedAkhir})";
                }
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validCount++;
            }

            // Tambahkan semua row, baik valid maupun invalid; sertakan nomor baris sumber Excel agar bisa dipetakan ke nomor sementara
            $allRows[] = ['cells' => $row, 'source_row' => $rowNumber];
        }

        return [
            'headers' => $expectedHeaders,
            'rows' => $allRows,
            'total' => count($allRows),
            'valid' => $validCount,
            'errors' => $errors
        ];
    }

    /**
     * Validasi format Laporan Penerimaan Kayu Olahan
     */
    private function validateLaporanPenerimaanKayuOlahan($sheet)
    {
        $expectedHeaders = ['Nomor Dokumen', 'Tanggal', 'Asal Kayu', 'Jenis Produk', 'Jumlah Keping', 'Volume', 'Keterangan'];

        $headerRow = $sheet[self::SHEET_HEADER_ROW] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, self::SHEET_DATA_START);
        $errors = [];
        $allRows = [];
        $validCount = 0;

        if (count($headers) < 7) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 7 kolom: Nomor Dokumen, Tanggal, Asal Kayu, Jenis Produk, Jumlah Keping, Volume, Keterangan');
        }

        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + self::SHEET_DATA_START + 1;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];

            if ($this->isEmptyRow($row)) {
                continue;
            }

            if (trim((string) ($row[0] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
            }

            if (trim((string) ($row[1] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
            } elseif (!$this->parseExcelDate($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Format tanggal tidak valid";
            }

            // Jika tanggal valid, format untuk preview
            $tanggalPreview = $this->parseExcelDate($row[1]);
            if ($tanggalPreview) {
                $d = \DateTime::createFromFormat('Y-m-d', $tanggalPreview);
                if ($d !== false) {
                    $row[1] = $d->format('d/m/Y');
                }
            }

            if (trim((string) ($row[2] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Asal Kayu tidak boleh kosong";
            }

            if (trim((string) ($row[3] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
            }

            if (trim((string) ($row[4] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh kosong";
            } else {
                $jumlahKeping = $this->parseExcelNumber($row[4]);
                if ($jumlahKeping === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234 atau 1234)";
                } elseif ($jumlahKeping < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh negatif";
                }
            }

            if (trim((string) ($row[5] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
            } else {
                $volume = $this->parseExcelNumber($row[5]);
                if ($volume === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Volume harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($volume < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh negatif";
                }
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validCount++;
            }

            // Jika ada tanggal dan valid, pastikan tampilannya user-friendly di preview
            $tanggalPreview = $this->parseExcelDate($row[1] ?? null);
            if ($tanggalPreview) {
                $d = \DateTime::createFromFormat('Y-m-d', $tanggalPreview);
                if ($d !== false) {
                    $row[1] = $d->format('d/m/Y');
                }
            }

            // Tambahkan semua row, baik valid maupun invalid; sertakan nomor baris sumber Excel agar bisa dipetakan ke nomor sementara
            $allRows[] = ['cells' => $row, 'source_row' => $rowNumber];
        }

        return [
            'headers' => $expectedHeaders,
            'rows' => $allRows,
            'total' => count($allRows),
            'valid' => $validCount,
            'errors' => $errors
        ];
    }

    /**
     * Validasi format Laporan Mutasi Kayu Olahan
     */
    private function validateLaporanMutasiKayuOlahan($sheet)
    {
        $expectedHeaders = ['Jenis Produk', 'Persediaan Awal', 'Penambahan', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'];

        $headerRow = $sheet[self::SHEET_HEADER_ROW] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, self::SHEET_DATA_START);
        $errors = [];
        $allRows = [];
        $validCount = 0;

        if (count($headers) < 6) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 6 kolom: Jenis Produk, Persediaan Awal, Penambahan, Penggunaan/Pengurangan, Persediaan Akhir, Keterangan');
        }

        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + self::SHEET_DATA_START + 1;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];

            if ($this->isEmptyRow($row)) {
                continue;
            }

            if (trim((string) ($row[0] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
            }

            if (trim((string) ($row[1] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh kosong";
            } else {
                $persediaanAwal = $this->parseExcelNumber($row[1]);
                if ($persediaanAwal === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($persediaanAwal < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh negatif";
                }
            }

            if (trim((string) ($row[2] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Penambahan tidak boleh kosong";
            } else {
                $penambahan = $this->parseExcelNumber($row[2]);
                if ($penambahan === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Penambahan harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($penambahan < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Penambahan tidak boleh negatif";
                }
            }

            if (trim((string) ($row[3] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh kosong";
            } else {
                $penggunaan = $this->parseExcelNumber($row[3]);
                if ($penggunaan === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($penggunaan < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh negatif";
                }
            }

            if (trim((string) ($row[4] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh kosong";
            } else {
                $persediaanAkhir = $this->parseExcelNumber($row[4]);
                if ($persediaanAkhir === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($persediaanAkhir < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh negatif";
                }
            }

            // Validasi logika: Persediaan Akhir = Persediaan Awal + Penambahan - Penggunaan
            $persediaanAwal = $this->parseExcelNumber($row[1]);
            $penambahan = $this->parseExcelNumber($row[2]);
            $penggunaan = $this->parseExcelNumber($row[3]);
            $persediaanAkhir = $this->parseExcelNumber($row[4]);

            if ($persediaanAwal !== null && $penambahan !== null && $penggunaan !== null && $persediaanAkhir !== null) {
                $expectedAkhir = $persediaanAwal + $penambahan - $penggunaan;
                if (abs($expectedAkhir - $persediaanAkhir) > 0.01) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak sesuai (seharusnya {$expectedAkhir})";
                }
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validCount++;
            }

            // Tambahkan semua row, baik valid maupun invalid; sertakan nomor baris sumber Excel agar bisa dipetakan ke nomor sementara
            $allRows[] = ['cells' => $row, 'source_row' => $rowNumber];
        }

        return [
            'headers' => $expectedHeaders,
            'rows' => $allRows,
            'total' => count($allRows),
            'valid' => $validCount,
            'errors' => $errors
        ];
    }

    /**
     * Validasi format Laporan Penjualan Kayu Olahan
     */
    private function validateLaporanPenjualanKayuOlahan($sheet)
    {
        $expectedHeaders = ['Nomor Dokumen', 'Tanggal', 'Tujuan Kirim', 'Jenis Produk', 'Jumlah Keping', 'Volume', 'Keterangan'];

        $headerRow = $sheet[self::SHEET_HEADER_ROW] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, self::SHEET_DATA_START);
        $errors = [];
        $allRows = [];
        $validCount = 0;

        if (count($headers) < 7) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 7 kolom: Nomor Dokumen, Tanggal, Tujuan Kirim, Jenis Produk, Jumlah Keping, Volume, Keterangan');
        }

        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + self::SHEET_DATA_START + 1;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];

            if ($this->isEmptyRow($row)) {
                continue;
            }

            if (trim((string) ($row[0] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
            }

            if (trim((string) ($row[1] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
            } elseif (!$this->parseExcelDate($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Format tanggal tidak valid";
            }

            if (trim((string) ($row[2] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Tujuan Kirim tidak boleh kosong";
            }

            if (trim((string) ($row[3] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
            }

            if (trim((string) ($row[4] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh kosong";
            } else {
                $jumlahKeping = $this->parseExcelNumber($row[4]);
                if ($jumlahKeping === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234 atau 1234)";
                } elseif ($jumlahKeping < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh negatif";
                }
            }

            if (trim((string) ($row[5] ?? '')) === '') {
                $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
            } else {
                $volume = $this->parseExcelNumber($row[5]);
                if ($volume === null) {
                    $rowErrors[] = "Baris {$rowNumber}: Volume harus berupa angka (format: desimal pakai titik, ribuan pakai koma. Contoh: 1,234.56 atau 2.27)";
                } elseif ($volume < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh negatif";
                }
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validCount++;
            }

            // Tambahkan semua row, baik valid maupun invalid; sertakan nomor baris sumber Excel agar bisa dipetakan ke nomor sementara
            $allRows[] = ['cells' => $row, 'source_row' => $rowNumber];
        }

        return [
            'headers' => $expectedHeaders,
            'rows' => $allRows,
            'total' => count($allRows),
            'valid' => $validCount,
            'errors' => $errors
        ];
    }

    /**
     * Helper: Parse angka dari format Excel
     * Format: Titik (.) untuk desimal, Koma (,) untuk ribuan
     * Contoh valid: 1,234.56 atau 1234.56 atau 2.27 atau 2,000
     */
    private function parseExcelNumber($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Jika sudah numeric (tanpa format string), langsung return
        // Excel sering mengirim float 1.5 secara langsung
        if (is_numeric($value)) {
            return (float) $value;
        }

        $s = trim((string) $value);

        // Format yang diterima:
        // - Koma (,) hanya sebagai pemisah ribuan
        // - Titik (.) hanya sebagai pemisah desimal
        // - Format valid: 1,234.56 atau 1234.56 atau 1,234 atau 1234

        // Jika ada koma DAN titik, validasi posisi:
        // Titik harus di belakang koma (format US: 1,234.56)
        if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
            $lastComma = strrpos($s, ',');
            $lastDot = strrpos($s, '.');

            if ($lastDot > $lastComma) {
                // Format valid: 1,234.56 -> Hapus koma (ribuan)
                $s = str_replace(',', '', $s);
            } else {
                // Format tidak valid: 1.234,56 (format Indonesia tidak diterima)
                return null;
            }
        }
        // Jika HANYA ada Koma (1,234 atau 3,85)
        elseif (strpos($s, ',') !== false) {
            // Validasi: koma hanya valid sebagai pemisah ribuan
            // Pemisah ribuan harus diikuti oleh tepat 3 digit (atau pola ribuan yang benar)
            // Format valid: 1,234 atau 1,234,567 atau 12,345
            // Format invalid: 3,85 atau 1,2 atau 12,3456 (ini format desimal Indonesia)

            // Split by comma dan cek pola
            $parts = explode(',', $s);
            $isValidThousandsSeparator = true;

            // Semua bagian kecuali yang terakhir harus 1-3 digit (bagian pertama) atau tepat 3 digit
            // Bagian terakhir harus tepat 3 digit untuk thousand separator yang valid
            if (count($parts) >= 2) {
                $lastPart = array_pop($parts);

                // Bagian terakhir harus tepat 3 digit untuk valid thousand separator
                if (strlen($lastPart) !== 3) {
                    // Jika bukan 3 digit, kemungkinan ini format desimal Indonesia (3,85)
                    return null; // Reject format Indonesia
                }

                // Cek bagian-bagian sebelumnya
                foreach ($parts as $i => $part) {
                    if ($i === 0) {
                        // Bagian pertama bisa 1-3 digit
                        if (strlen($part) < 1 || strlen($part) > 3 || !ctype_digit($part)) {
                            $isValidThousandsSeparator = false;
                            break;
                        }
                    } else {
                        // Bagian tengah harus tepat 3 digit
                        if (strlen($part) !== 3 || !ctype_digit($part)) {
                            $isValidThousandsSeparator = false;
                            break;
                        }
                    }
                }
            } else {
                $isValidThousandsSeparator = false;
            }

            if ($isValidThousandsSeparator) {
                // Valid thousand separator, hapus koma
                $s = str_replace(',', '', $s);
            } else {
                // Format tidak valid (kemungkinan format desimal Indonesia)
                return null;
            }
        }
        // Jika HANYA ada Titik (1.5 atau 1.234)
        // Titik dianggap desimal (format valid)

        // Bersihkan karakter non-numeric lain (misal spasi, Rp, dll) selain titik dan minus
        $s = preg_replace('/[^0-9\.\-]/', '', $s);

        if (is_numeric($s)) {
            return (float) $s;
        }

        return null;
    }

    /**
     * Helper: Parse tanggal dari berbagai format Excel
     */
    private function parseExcelDate($value): ?string
    {
        if (empty($value))
            return null;

        // Jika sudah dalam format tanggal Excel (numeric)
        if (is_numeric($value)) {
            try {
                $date = Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Try to robustly parse common ambiguous formats (DD/MM/YYYY vs MM/DD/YYYY)
        $s = trim((string) $value);

        // If ISO-like year first (YYYY-MM-DD or YYYY/MM/DD)
        if (preg_match('/^\d{4}[\-\/]\d{1,2}[\-\/]\d{1,2}$/', $s)) {
            $d = \DateTime::createFromFormat('Y-m-d', str_replace('/', '-', $s));
            if ($d !== false)
                return $d->format('Y-m-d');
        }

        // Split by common separators
        if (preg_match('/^[0-9]{1,4}[\/\-\.][0-9]{1,2}[\/\-\.][0-9]{1,4}$/', $s)) {
            $parts = preg_split('/[\/\-\.]/', $s);
            if (count($parts) === 3) {
                $p0 = (int) $parts[0];
                $p1 = (int) $parts[1];
                $p2 = (int) $parts[2];

                // Normalize 2-digit year
                if ($p2 < 100) {
                    $p2 += ($p2 >= 70) ? 1900 : 2000;
                }

                // Heuristics:
                // - If first part > 12 -> treat as day (d/m/y)
                // - Else if second part > 12 -> treat as month/day swap (m/d/y) but unlikely
                // - Else both <=12 -> ambiguous -> default to d/m/Y (local format)
                if ($p0 > 12) {
                    $day = $p0;
                    $month = $p1;
                    $year = $p2;
                } elseif ($p1 > 12) {
                    $day = $p1;
                    $month = $p0;
                    $year = $p2;
                } else {
                    // Ambiguous - default to day/month/year
                    $day = $p0;
                    $month = $p1;
                    $year = $p2;
                }

                if (checkdate($month, $day, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }
        }

        // Fallback: try several common formats explicitly
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $s);
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
