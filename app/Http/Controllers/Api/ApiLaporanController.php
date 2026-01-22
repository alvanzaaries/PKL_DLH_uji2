<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\LaporanValidationService;
use App\Services\LaporanDataService;
use Illuminate\Support\Facades\Log;

class ApiLaporanController extends Controller
{
    protected $validationService;
    protected $dataService;

    public function __construct(LaporanValidationService $validationService, LaporanDataService $dataService)
    {
        $this->validationService = $validationService;
        $this->dataService = $dataService;
    }

    /**
     * Upload single laporan via API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            // Validasi request
            $validated = $request->validate([
                'industri_id' => 'required|exists:industries,id',
                'bulan' => 'required|integer|between:1,12',
                'tahun' => 'required|integer|min:2020',
                'jenis_laporan' => 'required|string|in:Laporan Penerimaan Kayu Bulat,Laporan Mutasi Kayu Bulat (LMKB),Laporan Penerimaan Kayu Olahan,Laporan Mutasi Kayu Olahan (LMKO),Laporan Penjualan Kayu Olahan',
                'file_excel' => 'required|file|mimes:xlsx,xls|max:5120',
            ]);

            // Validasi unique: cek apakah sudah ada laporan untuk periode yang sama
            $existingLaporan = Laporan::where('industri_id', $validated['industri_id'])
                ->where('jenis_laporan', $validated['jenis_laporan'])
                ->whereYear('tanggal', $validated['tahun'])
                ->whereMonth('tanggal', $validated['bulan'])
                ->first();

            if ($existingLaporan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan jenis "' . $validated['jenis_laporan'] . '" untuk bulan ' . $validated['bulan'] . ' tahun ' . $validated['tahun'] . ' sudah ada.',
                    'error_code' => 'DUPLICATE_LAPORAN'
                ], 409);
            }

            // Upload file sementara
            $file = $request->file('file_excel');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('temp', $fileName, 'local');

            // Baca Excel dan validasi menggunakan service yang sudah ada
            $previewData = $this->validationService->readAndValidateExcel($filePath, $validated['jenis_laporan']);

            // Cek apakah ada error validasi
            if (!empty($previewData['errors'])) {
                // Hapus file temporary
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'File Excel memiliki error validasi. Mohon perbaiki file dan upload ulang.',
                    'errors' => $previewData['errors'],
                    'total_rows' => $previewData['total'] ?? 0,
                    'valid_rows' => $previewData['valid'] ?? 0,
                    'error_code' => 'VALIDATION_ERROR'
                ], 422);
            }

            // Semua validasi OK, simpan ke database
            DB::beginTransaction();

            try {
                // Buat tanggal dari bulan dan tahun
                $tanggal = $validated['tahun'] . '-' . str_pad($validated['bulan'], 2, '0', STR_PAD_LEFT) . '-01';

                // Simpan laporan master
                $laporan = Laporan::create([
                    'industri_id' => $validated['industri_id'],
                    'jenis_laporan' => $validated['jenis_laporan'],
                    'tanggal' => $tanggal,
                    'path_laporan' => '',
                ]);

                // Unwrap rows dari struktur ['cells' => ..., 'source_row' => ...] menjadi array sederhana
                $unwrappedRows = [];
                foreach ($previewData['rows'] as $row) {
                    if (is_array($row) && isset($row['cells'])) {
                        $unwrappedRows[] = $row['cells'];
                    } else {
                        $unwrappedRows[] = $row;
                    }
                }

                // Simpan detail menggunakan service yang sudah ada
                $this->dataService->saveDetailData($laporan, $validated['jenis_laporan'], $unwrappedRows);

                DB::commit();

                // Hapus file temporary
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Laporan berhasil diupload dan disimpan.',
                    'data' => [
                        'laporan_id' => $laporan->id,
                        'jenis_laporan' => $laporan->jenis_laporan,
                        'periode' => [
                            'bulan' => (int) $validated['bulan'],
                            'tahun' => (int) $validated['tahun'],
                        ],
                        'total_rows' => $previewData['total'] ?? 0,
                    ]
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi request gagal.',
                'errors' => $e->errors(),
                'error_code' => 'VALIDATION_ERROR'
            ], 422);

        } catch (\Exception $e) {
            // Hapus file temporary jika ada error
            if (isset($filePath) && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            Log::error('API Laporan upload failed', [
                'exception' => $e,
                'request' => $request->except(['file_excel'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses laporan: ' . $e->getMessage(),
                'error_code' => 'SERVER_ERROR'
            ], 500);
        }
    }
}
