<?php

namespace App\Imports;

use App\Models\IndustriBase;
use App\Models\Perajin;
use App\Helpers\KabupatenHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PerajinImport
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
                'Nama Perajin',
                'Alamat',
                'Kabupaten/Kota',
                'Latitude',
                'Longitude',
                'Penanggung Jawab',
                'Kontak',
                'Nomor SK',
                'Tanggal SK',
                'Status'
            ];

            if ($header !== $expectedHeaders) {
                $this->errors[] = [
                    'row' => 5,
                    'message' => 'Format header tidak sesuai. Pastikan menggunakan template yang benar.'
                ];
                return $this->getResult();
            }

            // Process each row starting from row 6 (index 5)
            for ($index = 5; $index < count($rows); $index++) {
                $row = $rows[$index];
                $rowNumber = $index + 1;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $this->processRow($row, $rowNumber);
                    $this->successCount++;
                } catch (\Exception $e) {
                    $this->errorCount++;
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => $e->getMessage()
                    ];
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
     * Process single row
     */
    protected function processRow($row, $rowNumber)
    {
        // Parse data sesuai urutan kolom di template
        $data = [
            'nama' => $row[0] ?? null,                    // Nama Perajin
            'alamat' => $row[1] ?? null,                  // Alamat
            'kabupaten' => KabupatenHelper::normalize($row[2] ?? ''),               // Kabupaten
            'latitude' => $row[3] ?? null,                // Latitude
            'longitude' => $row[4] ?? null,               // Longitude
            'penanggungjawab' => $row[5] ?? null,         // Penanggung Jawab
            'kontak' => $row[6] ?? null,                  // Kontak
            'nomor_izin' => $row[7] ?? null,              // Nomor Izin
            'tanggal' => $row[8] ?? null,                 // Tanggal SK
            'status' => $row[9] ?? 'Aktif',               // Status
        ];

        // Validasi data
        $validator = Validator::make($data, [
            'nama' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:100|unique:industries,nomor_izin',
            'alamat' => 'required|string',
            'kabupaten' => 'required|string|max:100',
            'penanggungjawab' => 'required|string|max:255',
            'kontak' => 'required|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tanggal' => 'required',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validasi gagal: ' . implode(', ', $validator->errors()->all()));
        }

        // Parse tanggal
        $tanggal = $this->parseDate($data['tanggal']);
        if (!$tanggal) {
            throw new \Exception('Format tanggal tidak valid. Gunakan format DD/MM/YYYY atau DD/MM/YYYY');
        }

        DB::beginTransaction();
        try {
            // Create industri base
            $industri = IndustriBase::create([
                'type' => 'end_user',
                'nama' => $data['nama'],
                'nomor_izin' => $data['nomor_izin'],
                'alamat' => $data['alamat'],
                'kabupaten' => $data['kabupaten'],
                'penanggungjawab' => $data['penanggungjawab'],
                'kontak' => $data['kontak'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'tanggal' => $tanggal,
                'status' => $data['status'],
            ]);

            // Create perajin
            Perajin::create([
                'industri_id' => $industri->id,
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
