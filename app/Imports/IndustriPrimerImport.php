<?php

namespace App\Imports;

use App\Models\IndustriBase;
use App\Models\IndustriPrimer;
use App\Models\MasterJenisProduksi;
use App\Helpers\KabupatenHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class IndustriPrimerImport
{
    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;

    /**
     * Import data dari file Excel
     * 
     * @param string $filePath Path ke file Excel
     * @return array ['success' => int, 'errors' => array, 'total' => int]
     */
    public function import($filePath)
    {
        $this->errors = [];
        $this->successCount = 0;
        $this->errorCount = 0;

        try {
            \Log::info('Import: Loading file', ['path' => $filePath]);

            if (!file_exists($filePath)) {
                throw new \Exception('File tidak ditemukan: ' . $filePath);
            }

            $spreadsheet = IOFactory::load($filePath);
            \Log::info('Import: File loaded successfully');

            $worksheet = $spreadsheet->getActiveSheet();
            \Log::info('Import: Got active worksheet');

            $rows = $worksheet->toArray();
            \Log::info('Import: Converted to array', ['row_count' => count($rows)]);

            // Skip first 4 rows (baris 1-4), header di baris 5 (index 4)
            $header = $rows[4] ?? null;

            if (!$header) {
                throw new \Exception('File Excel tidak memiliki header di baris 5');
            }

            \Log::info('Import: Header extracted from row 5', ['header' => $header]);

            // Validasi header
            $expectedHeaders = [
                'Nama Perusahaan',
                'Alamat',
                'Kabupaten/Kota',
                'Latitude',
                'Longitude',
                'Penanggung Jawab',
                'Kontak',
                'Nomor SK/NIB/SS',
                'Tanggal SK',
                'Total Nilai Investasi',
                'Total Pegawai',
                'Pemberi Izin',
                'Jenis Produksi',
                'Kapasitas Izin (m³/tahun)',
                'Status'
            ];

            if ($header !== $expectedHeaders) {
                \Log::error('Import: Header mismatch', [
                    'expected' => $expectedHeaders,
                    'actual' => $header
                ]);
                $this->errors[] = [
                    'row' => 5,
                    'message' => 'Format header tidak sesuai. Pastikan menggunakan template yang benar.'
                ];
                return $this->getResult();
            }


            \Log::info('Import: Header validated successfully');

            // Collect data rows starting from row 6 (index 5)
            $dataRows = [];
            for ($index = 5; $index < count($rows); $index++) {
                $row = $rows[$index];
                $rowNumber = $index + 1;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $dataRows[] = [
                    'row' => $row,
                    'rowNumber' => $rowNumber
                ];
            }

            // Group rows by nomor_izin (index 7)
            $groupedByCompany = [];
            foreach ($dataRows as $dataRow) {
                $nomorIzin = trim($dataRow['row'][7] ?? '');
                if (empty($nomorIzin)) {
                    $this->errorCount++;
                    $this->errors[] = [
                        'row' => $dataRow['rowNumber'],
                        'message' => 'Nomor Izin tidak boleh kosong'
                    ];
                    continue;
                }

                if (!isset($groupedByCompany[$nomorIzin])) {
                    $groupedByCompany[$nomorIzin] = [];
                }
                $groupedByCompany[$nomorIzin][] = $dataRow;
            }

            // Process each company group
            foreach ($groupedByCompany as $nomorIzin => $companyRows) {
                try {
                    $this->processCompanyGroup($companyRows);
                    $this->successCount += count($companyRows);
                } catch (\Exception $e) {
                    $this->errorCount += count($companyRows);
                    foreach ($companyRows as $companyRow) {
                        $this->errors[] = [
                            'row' => $companyRow['rowNumber'],
                            'message' => $e->getMessage()
                        ];
                    }
                }
            }


        } catch (\Exception $e) {
            \Log::error('Import: Fatal error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->errors[] = [
                'row' => 0,
                'message' => 'Error membaca file: ' . $e->getMessage()
            ];
        }

        return $this->getResult();
    }

    /**
     * Process a group of rows for the same company (same nomor_izin)
     */
    protected function processCompanyGroup($companyRows)
    {
        // Use first row for company data
        $firstRow = $companyRows[0]['row'];

        // Extract company data from first row
        $companyData = [
            'nama' => trim($firstRow[0] ?? ''),
            'alamat' => trim($firstRow[1] ?? ''),
            'kabupaten' => KabupatenHelper::normalize(trim($firstRow[2] ?? '')),
            'latitude' => $firstRow[3] ?? null,
            'longitude' => $firstRow[4] ?? null,
            'penanggungjawab' => trim($firstRow[5] ?? ''),
            'kontak' => trim($firstRow[6] ?? ''),
            'nomor_izin' => trim($firstRow[7] ?? ''),
            'tanggal' => $firstRow[8] ?? null,
            'total_nilai_investasi' => is_numeric($firstRow[9] ?? null) ? intval($firstRow[9]) : null,
            'total_pegawai' => is_numeric($firstRow[10] ?? null) ? intval($firstRow[10]) : null,
            'pemberi_izin' => trim($firstRow[11] ?? ''),
            'status' => trim($firstRow[14] ?? 'Aktif'),
        ];


        // Validate company data consistency across all rows
        foreach ($companyRows as $companyRow) {
            $row = $companyRow['row'];
            $rowNumber = $companyRow['rowNumber'];

            // Check if company data is consistent
            $inconsistencies = [];
            if (trim($row[0] ?? '') !== $companyData['nama'])
                $inconsistencies[] = 'Nama Perusahaan';
            if (trim($row[1] ?? '') !== $companyData['alamat'])
                $inconsistencies[] = 'Alamat';
            if (KabupatenHelper::normalize(trim($row[2] ?? '')) !== $companyData['kabupaten'])
                $inconsistencies[] = 'Kabupaten';
            if (trim($row[5] ?? '') !== $companyData['penanggungjawab'])
                $inconsistencies[] = 'Penanggung Jawab';
            if (trim($row[6] ?? '') !== $companyData['kontak'])
                $inconsistencies[] = 'Kontak';
            if (trim($row[11] ?? '') !== $companyData['pemberi_izin'])
                $inconsistencies[] = 'Pemberi Izin';

            if (!empty($inconsistencies)) {
                throw new \Exception("Baris $rowNumber: Data tidak konsisten dengan baris lain untuk Nomor Izin yang sama (" . implode(', ', $inconsistencies) . ")");
            }
        }

        // Validate company data
        $validator = Validator::make($companyData, [
            'nama' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:100',
            'alamat' => 'required|string',
            'kabupaten' => 'required|string|max:100',
            'penanggungjawab' => 'required|string|max:255',
            'kontak' => 'required|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tanggal' => 'required',
            'pemberi_izin' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validasi gagal: ' . implode(', ', $validator->errors()->all()));
        }

        // Parse tanggal
        $tanggal = $this->parseDate($companyData['tanggal']);
        if (!$tanggal) {
            throw new \Exception('Format tanggal tidak valid. Gunakan format DD/MM/YYYY atau DD/MM/YYYY');
        }

        DB::beginTransaction();
        try {
            // Check if company exists (upsert by nomor_izin)
            $industri = IndustriBase::where('nomor_izin', $companyData['nomor_izin'])->first();

            if ($industri) {
                // Update existing
                $industri->update([
                    'nama' => $companyData['nama'],
                    'alamat' => $companyData['alamat'],
                    'kabupaten' => $companyData['kabupaten'],
                    'penanggungjawab' => $companyData['penanggungjawab'],
                    'kontak' => $companyData['kontak'],
                    'latitude' => $companyData['latitude'],
                    'longitude' => $companyData['longitude'],
                    'tanggal' => $tanggal,
                    'status' => $companyData['status'],
                ]);
            } else {
                // Create new
                $industri = IndustriBase::create([
                    'type' => 'primer',
                    'nama' => $companyData['nama'],
                    'nomor_izin' => $companyData['nomor_izin'],
                    'alamat' => $companyData['alamat'],
                    'kabupaten' => $companyData['kabupaten'],
                    'penanggungjawab' => $companyData['penanggungjawab'],
                    'kontak' => $companyData['kontak'],
                    'latitude' => $companyData['latitude'],
                    'longitude' => $companyData['longitude'],
                    'tanggal' => $tanggal,
                    'status' => $companyData['status'],
                ]);
            }

            // Get or create industri primer
            $industriPrimer = IndustriPrimer::firstOrCreate(
                ['industri_id' => $industri->id],
                [
                    'pemberi_izin' => $companyData['pemberi_izin'],
                    'kapasitas_izin' => 0, // Will be sum of all production types
                    'pelaporan' => 'Pending',
                    'total_nilai_investasi' => $companyData['total_nilai_investasi'],
                    'total_pegawai' => $companyData['total_pegawai'],
                ]
            );

            // Detach existing jenis produksi to avoid duplicates
            $industriPrimer->jenisProduksi()->detach();

            // Process each production type with its capacity
            $totalKapasitas = 0;
            foreach ($companyRows as $companyRow) {
                $row = $companyRow['row'];
                $rowNumber = $companyRow['rowNumber'];

                $jenisProduksi = trim($row[12] ?? '');
                $kapasitasIzin = $row[13] ?? null;

                // Validate production type data
                if (empty($jenisProduksi)) {
                    throw new \Exception("Baris $rowNumber: Jenis Produksi tidak boleh kosong");
                }

                if (empty($kapasitasIzin) || !is_numeric($kapasitasIzin)) {
                    throw new \Exception("Baris $rowNumber: Kapasitas Izin harus berupa angka");
                }

                // Normalize and find master jenis produksi (case-insensitive)
                $masterJenisProduksi = MasterJenisProduksi::where('kategori', 'primer')
                    ->whereRaw('LOWER(TRIM(nama)) = ?', [strtolower(trim($jenisProduksi))])
                    ->first();

                if (!$masterJenisProduksi) {
                    // Not found in seed - use "Lainnya" with custom name
                    $lainnya = MasterJenisProduksi::where('nama', 'Lainnya')
                        ->whereIn('kategori', ['primer', 'both'])
                        ->first();

                    if (!$lainnya) {
                        throw new \Exception("Baris $rowNumber: Master data 'Lainnya' tidak ditemukan. Pastikan seeder sudah dijalankan.");
                    }

                    // Attach with custom name
                    $industriPrimer->jenisProduksi()->attach($lainnya->id, [
                        'kapasitas_izin' => $kapasitasIzin,
                        'nama_custom' => $jenisProduksi
                    ]);
                } else {
                    // Found in seed - use it
                    $industriPrimer->jenisProduksi()->attach($masterJenisProduksi->id, [
                        'kapasitas_izin' => $kapasitasIzin
                    ]);
                }


                $totalKapasitas += $kapasitasIzin;
            }

            // Update total kapasitas
            $industriPrimer->update(['kapasitas_izin' => $totalKapasitas]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Parse tanggal dari berbagai format
     */
    protected function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // Jika sudah dalam format Carbon
        if ($date instanceof Carbon) {
            return $date->format('Y-m-d');
        }

        // Jika numeric (Excel date serial)
        if (is_numeric($date)) {
            try {
                $excelEpoch = Carbon::create(1899, 12, 30);
                return $excelEpoch->addDays($date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Parse string date
        try {
            // Try DD/MM/YYYY
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
            }

            // Try DD/MM/YYYY
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            }

            // Try other formats
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getResult()
    {
        return [
            'success' => $this->successCount,
            'errors_count' => $this->errorCount,
            'total' => $this->successCount + $this->errorCount,
            'errors' => $this->errors
        ];
    }
}
