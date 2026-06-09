<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Industri;
use App\Models\Laporan;
use App\Models\JenisLaporan;
use App\Services\LaporanValidationService;
use App\Services\LaporanDataService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmitDummyLaporan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laporan:submit-dummy {--year=2026} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submit dummy report data for March, April, May, and June using Excel files in laporan_to_submit';

    protected $validationService;
    protected $dataService;

    public function __construct(LaporanValidationService $validationService, LaporanDataService $dataService)
    {
        parent::__construct();
        $this->validationService = $validationService;
        $this->dataService = $dataService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = (int) $this->option('year');
        $force = $this->option('force');

        $basePath = base_path('laporan_to_submit');

        if (!File::isDirectory($basePath)) {
            $this->error("Folder 'laporan_to_submit' tidak ditemukan di root project!");
            return Command::FAILURE;
        }

        // Mapping dari jenis laporan ID ke nama folder & nama laporan
        $reportTypesMap = [
            1 => [
                'name' => 'Laporan Penerimaan Kayu Bulat',
                'folder' => 'Laporan Penerimaan Kayu Bulat',
            ],
            2 => [
                'name' => 'Laporan Mutasi Kayu Bulat (LMKB)',
                'folder' => 'Laporan Mutasi Kayu Bulat',
            ],
            3 => [
                'name' => 'Laporan Penerimaan Kayu Olahan',
                'folder' => 'Laporan Penerimaan Kayu Olahan',
            ],
            4 => [
                'name' => 'Laporan Mutasi Kayu Olahan (LMKO)',
                'folder' => 'Laporan Mutasi Kayu Olahan',
            ],
            5 => [
                'name' => 'Laporan Penjualan Kayu Olahan',
                'folder' => 'Laporan Penjualan Kayu Olahan',
            ],
        ];

        // Ambil industri aktif selain end_user
        $industries = Industri::where('status', 'Aktif')
            ->where(function ($q) {
                $q->whereNull('type')->orWhereNotIn('type', ['end_user']);
            })
            ->get();

        if ($industries->isEmpty()) {
            $this->warn("Tidak ada industri aktif (selain perajin) di database.");
            return Command::SUCCESS;
        }

        $months = [3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni'];
        
        $this->info("Memulai pengisian data dummy untuk tahun {$year}...");
        
        // Buat folder temp jika belum ada
        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        foreach ($industries as $industry) {
            $this->line("--------------------------------------------------");
            $this->info("Memproses Industri: {$industry->nama} (ID: {$industry->id})");

            foreach ($months as $month => $monthName) {
                $this->line("  Bulan: {$monthName} {$year}");

                foreach ($reportTypesMap as $typeId => $config) {
                    $reportName = $config['name'];
                    $folderName = $config['folder'];
                    $folderPath = $basePath . '/' . $folderName;

                    if (!File::isDirectory($folderPath)) {
                        $this->warn("    [FOLDER LEWAT] Sub-folder '{$folderName}' tidak ditemukan.");
                        continue;
                    }

                    // Scan file excel dalam folder
                    $files = File::files($folderPath);
                    $excelFiles = array_filter($files, function ($file) {
                        return in_array(strtolower($file->getExtension()), ['xlsx', 'xls']);
                    });

                    if (empty($excelFiles)) {
                        $this->warn("    [FILE LEWAT] Tidak ada file Excel di folder '{$folderName}'.");
                        continue;
                    }

                    // Cek jika laporan sudah ada
                    $existing = Laporan::where('industri_id', $industry->id)
                        ->where('jenis_laporan_id', $typeId)
                        ->whereYear('tanggal', $year)
                        ->whereMonth('tanggal', $month)
                        ->first();

                    if ($existing) {
                        if (!$force) {
                            $this->line("    [SUDAH ADA] Laporan '{$reportName}' sudah ada. Gunakan --force untuk menimpa.");
                            continue;
                        }

                        // Hapus laporan lama jika force
                        $this->deleteExistingLaporan($existing);
                        $this->line("    [TIMPA] Menghapus laporan '{$reportName}' yang sudah ada sebelumnya.");
                    }

                    // Pilih file secara acak
                    $randomFile = $excelFiles[array_rand($excelFiles)];
                    $originalName = $randomFile->getFilename();
                    
                    // Copy ke storage temp agar bisa dibaca oleh ValidationService
                    $tempFileName = time() . '_' . uniqid() . '_' . $originalName;
                    $tempPath = 'temp/' . $tempFileName;
                    Storage::put($tempPath, File::get($randomFile->getRealPath()));

                    try {
                        // Jalankan validasi
                        $previewData = $this->validationService->readAndValidateExcel($tempPath, $reportName);

                        if (!empty($previewData['errors'])) {
                            $this->error("    [ERROR VALIDASI] File '{$originalName}' gagal validasi:");
                            foreach (array_slice($previewData['errors'], 0, 5) as $err) {
                                $this->error("      - " . $err);
                            }
                            if (count($previewData['errors']) > 5) {
                                $this->error("      - ...dan " . (count($previewData['errors']) - 5) . " error lainnya.");
                            }
                            
                            // Bersihkan file temp dan lanjut
                            Storage::delete($tempPath);
                            continue;
                        }

                        // Simpan laporan ke DB
                        DB::beginTransaction();

                        $tanggal = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';

                        $laporan = Laporan::create([
                            'industri_id' => $industry->id,
                            'user_id' => 1, // Default Admin ID
                            'jenis_laporan_id' => $typeId,
                            'tanggal' => $tanggal,
                            'path_laporan' => '',
                        ]);

                        // Unwrap rows
                        $cleanedRows = array_map(function ($item) {
                            return isset($item['cells']) ? $item['cells'] : $item;
                        }, $previewData['rows']);

                        $this->dataService->saveDetailData($laporan, $reportName, $cleanedRows);

                        DB::commit();

                        $this->info("    [BERHASIL] Laporan '{$reportName}' berhasil diupload menggunakan '{$originalName}'.");

                    } catch (\Throwable $e) {
                        DB::rollBack();
                        $this->error("    [SYSTEM ERROR] Gagal memproses '{$originalName}': " . $e->getMessage());
                        Log::error("Submit dummy error for {$industry->nama}, Month {$month}: " . $e->getMessage(), [
                            'exception' => $e
                        ]);
                    } finally {
                        // Hapus file temp
                        if (Storage::exists($tempPath)) {
                            Storage::delete($tempPath);
                        }
                        unset($previewData);
                        unset($cleanedRows);
                        gc_collect_cycles();
                    }
                }
            }
        }

        $this->info("Proses pengisian data dummy selesai.");
        return Command::SUCCESS;
    }

    /**
     * Helper to delete existing laporan and its details
     */
    private function deleteExistingLaporan($laporan)
    {
        switch ($laporan->jenisLaporan->slug) {
            case 'penerimaan_kayu_bulat':
                \App\Models\laporan_penerimaan_kayu_bulat::where('laporan_id', $laporan->id)->delete();
                break;
            case 'mutasi_kayu_bulat':
                \App\Models\laporan_mutasi_kayu_bulat::where('laporan_id', $laporan->id)->delete();
                break;
            case 'penerimaan_kayu_olahan':
                \App\Models\laporan_penerimaan_kayu_olahan::where('laporan_id', $laporan->id)->delete();
                break;
            case 'mutasi_kayu_olahan':
                \App\Models\laporan_mutasi_kayu_olahan::where('laporan_id', $laporan->id)->delete();
                break;
            case 'penjualan_kayu_olahan':
                \App\Models\laporan_penjualan_kayu_olahan::where('laporan_id', $laporan->id)->delete();
                break;
        }
        $laporan->delete();
    }
}
