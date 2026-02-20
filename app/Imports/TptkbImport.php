<?php

namespace App\Imports;

use App\Models\IndustriBase;
use App\Models\Tptkb;
use App\Models\MasterSumber;
use App\Helpers\KabupatenHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TptkbImport
{
    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;

    /**
     * Import data dari file Excel
     */
    public function import($filePath)
    {
        $this->errors = [];
        $this->successCount = 0;
        $this->errorCount = 0;

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip first 4 rows (baris 1-4), header di baris 5 (index 4)
            $header = $rows[4] ?? null;

            if (!$header) {
                throw new \Exception('File Excel tidak memiliki header di baris 5');
            }

            // Validasi header
            $expectedHeaders = [
                'Nama Perusahaan',
                'Alamat',
                'Kabupaten/Kota',
                'Latitude',
                'Longitude',
                'Penanggung Jawab',
                'Kontak',
                'Nomor SK',
                'Tanggal SK',
                'Pemberi Izin',
                'Sumber Bahan Baku',
                'Kapasitas (m³/tahun)',
                'Masa Berlaku',
                'Status'
            ];

            if ($header !== $expectedHeaders) {
                $this->errors[] = [
                    'row' => 5,
                    'message' => 'Format header tidak sesuai. Pastikan menggunakan template yang benar.'
                ];
                return $this->getResult();
            }

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
        $firstRowNumber = $companyRows[0]['rowNumber'];

        // Parse company data from first row
        $companyData = [
            'nama' => $firstRow[0] ?? null,
            'alamat' => $firstRow[1] ?? null,
            'kabupaten' => KabupatenHelper::normalize($firstRow[2] ?? ''),
            'latitude' => $firstRow[3] ?? null,
            'longitude' => $firstRow[4] ?? null,
            'penanggungjawab' => $firstRow[5] ?? null,
            'kontak' => $firstRow[6] ?? null,
            'nomor_izin' => $firstRow[7] ?? null,
            'tanggal' => $firstRow[8] ?? null,
            'pemberi_izin' => $firstRow[9] ?? null,
            'masa_berlaku' => $firstRow[12] ?? null,
            'status' => $firstRow[13] ?? 'Aktif',
        ];

        // Validate company data consistency across all rows
        foreach ($companyRows as $companyRow) {
            $row = $companyRow['row'];
            $rowNumber = $companyRow['rowNumber'];

            // Check if company data is consistent
            $inconsistencies = [];
            if (trim($row[0] ?? '') !== trim($companyData['nama']))
                $inconsistencies[] = 'Nama Perusahaan';
            if (trim($row[1] ?? '') !== trim($companyData['alamat']))
                $inconsistencies[] = 'Alamat';
            if (KabupatenHelper::normalize(trim($row[2] ?? '')) !== trim($companyData['kabupaten']))
                $inconsistencies[] = 'Kabupaten';
            if (trim($row[5] ?? '') !== trim($companyData['penanggungjawab']))
                $inconsistencies[] = 'Penanggung Jawab';
            if (trim($row[6] ?? '') !== trim($companyData['kontak']))
                $inconsistencies[] = 'Kontak';
            if (trim($row[9] ?? '') !== trim($companyData['pemberi_izin']))
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
            'masa_berlaku' => 'required',
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

        $masaBerlaku = $this->parseDate($companyData['masa_berlaku']);
        if (!$masaBerlaku) {
            throw new \Exception('Format masa berlaku tidak valid. Gunakan format DD/MM/YYYY atau DD/MM/YYYY');
        }

        DB::beginTransaction();
        try {
            // Check if company already exists
            $industri = IndustriBase::where('nomor_izin', $companyData['nomor_izin'])->first();

            if ($industri) {
                // Update existing company
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

                $tptkb = $industri->tptkb;
                $tptkb->update([
                    'pemberi_izin' => $companyData['pemberi_izin'],
                    'masa_berlaku' => $masaBerlaku,
                ]);

                // Clear existing sumber bahan baku
                $tptkb->sumberBahanBaku()->detach();
            } else {
                // Create new company
                $industri = IndustriBase::create([
                    'type' => 'tpt_kb',
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

                $tptkb = Tptkb::create([
                    'industri_id' => $industri->id,
                    'pemberi_izin' => $companyData['pemberi_izin'],
                    'sumber_bahan_baku' => '', // Will be updated
                    'kapasitas_izin' => 0, // Will be sum of all sources
                    'masa_berlaku' => $masaBerlaku,
                ]);
            }

            // Process each sumber bahan baku with its capacity
            $totalKapasitas = 0;
            $sumberNames = [];
            foreach ($companyRows as $companyRow) {
                $row = $companyRow['row'];
                $rowNumber = $companyRow['rowNumber'];

                $sumberBahanBaku = trim($row[10] ?? '');
                $kapasitas = $row[11] ?? null;

                // Validate sumber data
                if (empty($sumberBahanBaku)) {
                    throw new \Exception("Baris $rowNumber: Sumber Bahan Baku tidak boleh kosong");
                }

                if (empty($kapasitas) || !is_numeric($kapasitas)) {
                    throw new \Exception("Baris $rowNumber: Kapasitas harus berupa angka");
                }

                // Normalize and find master sumber (case-insensitive)
                $masterSumber = MasterSumber::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower(trim($sumberBahanBaku))])
                    ->first();

                if (!$masterSumber) {
                    // Not found in seed - use "Lainnya" with custom name
                    $lainnya = MasterSumber::where('nama', 'Lainnya')->first();

                    if (!$lainnya) {
                        throw new \Exception("Baris $rowNumber: Master data 'Lainnya' tidak ditemukan. Pastikan seeder sudah dijalankan.");
                    }

                    // Attach with custom name
                    $tptkb->sumberBahanBaku()->attach($lainnya->id, [
                        'kapasitas' => $kapasitas,
                        'nama_custom' => $sumberBahanBaku
                    ]);

                    $sumberNames[] = $sumberBahanBaku; // Use custom name
                } else {
                    // Found in seed - use it
                    $tptkb->sumberBahanBaku()->attach($masterSumber->id, [
                        'kapasitas' => $kapasitas
                    ]);

                    $sumberNames[] = $masterSumber->nama; // Use master name
                }

            }

            // Update total kapasitas and sumber names
            $tptkb->update([
                'kapasitas_izin' => $totalKapasitas,
                'sumber_bahan_baku' => implode(', ', $sumberNames)
            ]);

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

        if ($date instanceof Carbon) {
            return $date->format('Y-m-d');
        }

        if (is_numeric($date)) {
            try {
                $excelEpoch = Carbon::create(1899, 12, 30);
                return $excelEpoch->addDays($date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
            }

            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            }

            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get import result
     */
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
