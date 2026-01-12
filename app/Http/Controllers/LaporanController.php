<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\laporan_mutasi_kayu_bulat;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\laporan_mutasi_kayu_olahan;
use App\Http\Requests\UpdateLaporanRequest;
use App\Models\laporan_penerimaan_kayu_bulat;
use App\Models\laporan_penjualan_kayu_olahan;
use App\Models\laporan_penerimaan_kayu_olahan;

class LaporanController extends Controller
{
    /**
     * Preview data Excel sebelum disimpan
     */
    public function preview(Request $request)
    {
        $request->validate([
            'industri_id' => 'required|exists:industries,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'jenis_laporan' => 'required|string',
            'file_excel' => 'required|file|mimes:xlsx,xls|max:5120', // Max 5MB
        ]);

        // Validasi unique: cek apakah sudah ada laporan untuk industri, bulan, tahun, dan jenis yang sama
        $tanggalCek = $request->tahun . '-' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . '-01';
        $existingLaporan = Laporan::where('industri_id', $request->industri_id)
            ->where('jenis_laporan', $request->jenis_laporan)
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->first();

        if ($existingLaporan) {
            return back()
                ->withInput()
                ->with('error', 'Laporan jenis "' . $request->jenis_laporan . '" untuk bulan ' . $request->bulan . ' tahun ' . $request->tahun . ' sudah pernah diupload. Setiap jenis laporan hanya bisa diupload sekali per bulan.');
        }

        try {
            // Upload file sementara
            $file = $request->file('file_excel');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('temp', $fileName, 'local');

            // ========================================
            // LOGIKA CUSTOM KAMU MULAI DI SINI
            // ========================================
            
            // Baca Excel dan validasi format sesuai jenis laporan
            $jenisLaporan = $request->jenis_laporan;
            $previewData = $this->readAndValidateExcel($filePath, $jenisLaporan);

            // Contoh struktur return:
            // $previewData = [
            //     'headers' => ['Kolom 1', 'Kolom 2', ...],
            //     'rows' => [
            //         ['value1', 'value2', ...],
            //         ['value1', 'value2', ...],
            //     ],
            //     'total' => 100,
            //     'valid' => 95,
            //     'errors' => [
            //         'Baris 5: Nomor dokumen tidak valid',
            //         'Baris 12: Volume harus angka',
            //     ]
            // ];

            // ========================================
            // LOGIKA CUSTOM SELESAI
            // ========================================

            // Simpan data ke session untuk proses simpan nanti
            session([
                'preview_data' => $previewData,
                'preview_file_path' => $filePath,
                'preview_metadata' => [
                    'industri_id' => $request->industri_id,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'jenis_laporan' => $jenisLaporan,
                ]
            ]);

            // Return view preview dengan data
            return view('laporan/previewLaporan', [
                'data' => $previewData,
                'metadata' => $request->only(['industri_id', 'bulan', 'tahun', 'jenis_laporan'])
            ]);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    /**
     * Method custom untuk baca dan validasi Excel
     */
    private function readAndValidateExcel($filePath, $jenisLaporan)
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
            
            // Skip baris kosong - cek semua cell tidak ada yang berisi nilai
            $isEmpty = true;
            foreach ($row as $cell) {
                if ($cell !== null && $cell !== '' && trim($cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) {
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
                // Coba parse tanggal dari berbagai format
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
            
            // Keterangan (kolom 6) - opsional, tidak perlu validasi
            
            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validRows[] = $row;
            }
        }
        
        return [
            'headers' => $expectedHeaders,
            'rows' => array_slice($validRows, 0, 10), // Preview 10 baris pertama yang valid
            'total' => count($validRows), // Total baris valid (tidak termasuk baris kosong)
            'valid' => count($validRows),
            'errors' => $errors
        ];
    }

    /**
     * Validasi format Laporan Mutasi Kayu Bulat
     * Header di row 7, Data dimulai row 8, kolom A (No) diabaikan
     * Kolom B-F: Jenis Kayu | Persediaan Awal | Penggunaan/Pengurangan | Persediaan Akhir | Keterangan
     */
    private function validateLaporanMutasiKayuBulat($sheet)
    {
        $expectedHeaders = ['Jenis Kayu', 'Persediaan Awal', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'];
        
        // Header di row 7 (index 6), skip kolom A
        $headerRow = $sheet[6] ?? [];
        $headers = array_slice($headerRow, 1);
        
        // Data dimulai dari row 8 (index 7)
        $dataRows = array_slice($sheet, 7);
        $errors = [];
        $validRows = [];
        
        // Validasi header
        if (count($headers) < 5) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 5 kolom: Jenis Kayu, Persediaan Awal, Penggunaan/Pengurangan, Persediaan Akhir, Keterangan');
        }
        
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + 8;
            $row = array_slice($fullRow, 1); // Skip kolom A
            $rowErrors = [];
            
            // Skip baris kosong - cek semua cell tidak ada yang berisi nilai
            $isEmpty = true;
            foreach ($row as $cell) {
                if ($cell !== null && $cell !== '' && trim($cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) {
                continue;
            }
            
            // Validasi Jenis Kayu (kolom 0) - wajib
            if (empty($row[0])) {
                $rowErrors[] = "Baris {$rowNumber}: Jenis Kayu tidak boleh kosong";
            }
            
            // Validasi Persediaan Awal (kolom 1) - wajib, harus angka
            if (empty($row[1]) && $row[1] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh kosong";
            } elseif (!is_numeric($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal harus berupa angka";
            } elseif ($row[1] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh negatif";
            }
            
            // Validasi Penggunaan/Pengurangan (kolom 2) - wajib, harus angka
            if (empty($row[2]) && $row[2] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh kosong";
            } elseif (!is_numeric($row[2])) {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan harus berupa angka";
            } elseif ($row[2] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh negatif";
            }
            
            // Validasi Persediaan Akhir (kolom 3) - wajib, harus angka
            if (empty($row[3]) && $row[3] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh kosong";
            } elseif (!is_numeric($row[3])) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir harus berupa angka";
            } elseif ($row[3] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh negatif";
            }
            
            // Validasi logika: Persediaan Akhir = Persediaan Awal - Penggunaan
            if (is_numeric($row[1]) && is_numeric($row[2]) && is_numeric($row[3])) {
                $expectedAkhir = $row[1] - $row[2];
                if (abs($expectedAkhir - $row[3]) > 0.01) { // Toleransi pembulatan
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
     * Header di row 7, Data dimulai row 8, kolom A (No) diabaikan
     * Kolom B-G: Nomor Dokumen | Tanggal | Asal Kayu | Jenis Produk | Volume | Keterangan
     */
    private function validateLaporanPenerimaanKayuOlahan($sheet)
    {
        $expectedHeaders = ['Nomor Dokumen', 'Tanggal', 'Asal Kayu', 'Jenis Produk', 'Volume', 'Keterangan'];
        
        $headerRow = $sheet[6] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, 7);
        $errors = [];
        $validRows = [];
        
        if (count($headers) < 6) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 6 kolom: Nomor Dokumen, Tanggal, Asal Kayu, Jenis Produk, Volume, Keterangan');
        }
        
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + 8;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];
            
            // Skip baris kosong
            $isEmpty = true;
            foreach ($row as $cell) {
                if ($cell !== null && $cell !== '' && trim($cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) continue;
            
            if (empty($row[0])) $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
            
            if (empty($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
            } elseif (!$this->parseExcelDate($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Format tanggal tidak valid";
            }
            
            if (empty($row[2])) $rowErrors[] = "Baris {$rowNumber}: Asal Kayu tidak boleh kosong";
            if (empty($row[3])) $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
            
            if (empty($row[4]) && $row[4] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
            } elseif (!is_numeric($row[4])) {
                $rowErrors[] = "Baris {$rowNumber}: Volume harus berupa angka";
            } elseif ($row[4] < 0) {
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
            'rows' => array_slice($validRows, 0, 10),
            'total' => count($validRows),
            'valid' => count($validRows),
            'errors' => $errors
        ];
    }
    
    /**
     * Validasi format Laporan Mutasi Kayu Olahan
     * Header di row 7, Data dimulai row 8, kolom A (No) diabaikan
     * Kolom B-F: Jenis Produk | Persediaan Awal | Penggunaan/Pengurangan | Persediaan Akhir | Keterangan
     */
    private function validateLaporanMutasiKayuOlahan($sheet)
    {
        $expectedHeaders = ['Jenis Produk', 'Persediaan Awal', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'];
        
        $headerRow = $sheet[6] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, 7);
        $errors = [];
        $validRows = [];
        
        if (count($headers) < 5) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 5 kolom');
        }
        
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + 8;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];
            
            // Skip baris kosong
            $isEmpty = true;
            foreach ($row as $cell) {
                if ($cell !== null && $cell !== '' && trim($cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) continue;
            
            if (empty($row[0])) $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
            
            if (empty($row[1]) && $row[1] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh kosong";
            } elseif (!is_numeric($row[1]) || $row[1] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal harus angka positif";
            }
            
            if (empty($row[2]) && $row[2] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh kosong";
            } elseif (!is_numeric($row[2]) || $row[2] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan harus angka positif";
            }
            
            if (empty($row[3]) && $row[3] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh kosong";
            } elseif (!is_numeric($row[3]) || $row[3] < 0) {
                $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir harus angka positif";
            }
            
            // Validasi logika
            if (is_numeric($row[1]) && is_numeric($row[2]) && is_numeric($row[3])) {
                $expectedAkhir = $row[1] - $row[2];
                if (abs($expectedAkhir - $row[3]) > 0.01) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak sesuai";
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
     * Header di row 7, Data dimulai row 8, kolom A (No) diabaikan
     * Kolom B-G: Nomor Dokumen | Tanggal | Pembeli | Jenis Produk | Volume | Keterangan
     */
    private function validateLaporanPenjualanKayuOlahan($sheet)
    {
        $expectedHeaders = ['Nomor Dokumen', 'Tanggal', 'Pembeli', 'Jenis Produk', 'Volume', 'Keterangan'];
        
        $headerRow = $sheet[6] ?? [];
        $headers = array_slice($headerRow, 1);
        $dataRows = array_slice($sheet, 7);
        $errors = [];
        $validRows = [];
        
        if (count($headers) < 6) {
            throw new \Exception('Format file tidak sesuai. Header harus memiliki 6 kolom');
        }
        
        foreach ($dataRows as $index => $fullRow) {
            $rowNumber = $index + 8;
            $row = array_slice($fullRow, 1);
            $rowErrors = [];
            
            // Skip baris kosong
            $isEmpty = true;
            foreach ($row as $cell) {
                if ($cell !== null && $cell !== '' && trim($cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) continue;
            
            if (empty($row[0])) $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
            
            if (empty($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
            } elseif (!$this->parseExcelDate($row[1])) {
                $rowErrors[] = "Baris {$rowNumber}: Format tanggal tidak valid";
            }
            
            if (empty($row[2])) $rowErrors[] = "Baris {$rowNumber}: Pembeli tidak boleh kosong";
            if (empty($row[3])) $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
            
            if (empty($row[4]) && $row[4] !== 0) {
                $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
            } elseif (!is_numeric($row[4]) || $row[4] < 0) {
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
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Tampilkan semua laporan dari industri tertentu
     */
    public function showByIndustri($industriId)
    {
        $industri = \App\Models\Industri::findOrFail($industriId);
        
        $laporans = Laporan::where('industri_id', $industriId)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('laporan/laporanByIndustri', [
            'industri' => $industri,
            'laporans' => $laporans
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('laporan.addLaporan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'industri_id' => 'required|exists:industries,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'jenis_laporan' => 'required|string',
            'confirmed_preview' => 'required',
        ]);

        // Cek apakah ada data preview di session
        if (!session()->has('preview_data')) {
            return redirect()->route('laporan.upload')
                ->with('error', 'Data preview tidak ditemukan. Silakan upload ulang.');
        }

        // Validasi unique lagi sebelum menyimpan (double check)
        $existingLaporan = Laporan::where('industri_id', $request->industri_id)
            ->where('jenis_laporan', $request->jenis_laporan)
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->first();

        if ($existingLaporan) {
            return redirect()->route('laporan.upload')
                ->with('error', 'Laporan jenis "' . $request->jenis_laporan . '" untuk bulan ' . $request->bulan . ' tahun ' . $request->tahun . ' sudah pernah diupload.');
        }

        try {
            DB::beginTransaction();

            // Ambil data dari session
            $previewData = session('preview_data');
            $filePath = session('preview_file_path');

            // Buat tanggal dari bulan dan tahun
            $tanggal = $request->tahun . '-' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . '-01';

            // Simpan laporan master
            $laporan = Laporan::create([
                'industri_id' => $request->industri_id,
                'jenis_laporan' => $request->jenis_laporan,
                'tanggal' => $tanggal,
                'path_laporan' => '', // Excel data disimpan ke detail table
            ]);

            // Simpan detail berdasarkan jenis laporan
            $this->saveDetailData($laporan, $request->jenis_laporan, $previewData['rows']);

            DB::commit();

            // Hapus file temporary
            if ($filePath && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            // Clear session data
            session()->forget(['preview_data', 'preview_file_path', 'preview_metadata']);

            return redirect()->route('industri.laporan', ['industri' => $request->industri_id])
                ->with('success', 'Laporan berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('industri.laporan', ['industri' => $request->industri_id])
                ->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage());
        }
    }

    /**
     * Simpan data detail berdasarkan jenis laporan
     * Data dalam format array of arrays sesuai urutan kolom
     */
    private function saveDetailData($laporan, $jenisLaporan, $rows)
    {
        switch ($jenisLaporan) {
            case 'Laporan Penerimaan Kayu Bulat':
                // Kolom: Nomor Dokumen | Tanggal | Asal Kayu | Jenis Kayu | Jumlah Batang | Volume | Keterangan
                foreach ($rows as $row) {
                    laporan_penerimaan_kayu_bulat::create([
                        'laporan_id' => $laporan->id,
                        'nomor_dokumen' => $row[0] ?? '',
                        'tanggal' => $row[1] ?? now(),
                        'asal_kayu' => $row[2] ?? '',
                        'jenis_kayu' => $row[3] ?? '',
                        'jumlah_batang' => $row[4] ?? 0,
                        'volume' => $row[5] ?? 0,
                        'keterangan' => $row[6] ?? '',
                    ]);
                }
                break;

            case 'Laporan Mutasi Kayu Bulat (LMKB)':
                // Kolom: Jenis Kayu | Persediaan Awal | Pengurangan | Persediaan Akhir | Keterangan
                foreach ($rows as $row) {
                    laporan_mutasi_kayu_bulat::create([
                        'laporan_id' => $laporan->id,
                        'jenis_kayu' => $row[0] ?? '',
                        'persediaan_awal_volume' => $row[1] ?? 0,
                        'penggunaan_pengurangan_volume' => $row[2] ?? 0,
                        'persediaan_akhir_volume' => $row[3] ?? 0,
                        'keterangan' => $row[4] ?? '',
                    ]);
                }
                break;

            case 'Laporan Penerimaan Kayu Olahan':
                // Kolom: Nomor Dokumen | Tanggal | Asal Kayu | Jenis Olahan | Jumlah Keping | Volume | Keterangan
                foreach ($rows as $row) {
                    laporan_penerimaan_kayu_olahan::create([
                        'laporan_id' => $laporan->id,
                        'nomor_dokumen' => $row[0] ?? '',
                        'tanggal' => $row[1] ?? now(),
                        'asal_kayu' => $row[2] ?? '',
                        'jenis_olahan' => $row[3] ?? '',
                        'jumlah_keping' => $row[4] ?? 0,
                        'volume' => $row[5] ?? 0,
                        'keterangan' => $row[6] ?? '',
                    ]);
                }
                break;

            case 'Laporan Mutasi Kayu Olahan (LMKO)':
                // Kolom: Jenis Olahan | Persediaan Awal | Pengurangan | Persediaan Akhir | Keterangan
                foreach ($rows as $row) {
                    laporan_mutasi_kayu_olahan::create([
                        'laporan_id' => $laporan->id,
                        'jenis_olahan' => $row[0] ?? '',
                        'persediaan_awal_volume' => $row[1] ?? 0,
                        'penggunaan_pengurangan_volume' => $row[2] ?? 0,
                        'persediaan_akhir_volume' => $row[3] ?? 0,
                        'keterangan' => $row[4] ?? '',
                    ]);
                }
                break;

            case 'Laporan Penjualan Kayu Olahan':
                // Kolom: Nomor Dokumen | Tanggal | Tujuan Kirim | Jenis Olahan | Jumlah Keping | Volume | Keterangan
                foreach ($rows as $row) {
                    laporan_penjualan_kayu_olahan::create([
                        'laporan_id' => $laporan->id,
                        'nomor_dokumen' => $row[0] ?? '',
                        'tanggal' => $row[1] ?? now(),
                        'tujuan_kirim' => $row[2] ?? '',
                        'jenis_olahan' => $row[3] ?? '',
                        'jumlah_keping' => $row[4] ?? 0,
                        'volume' => $row[5] ?? 0,
                        'keterangan' => $row[6] ?? '',
                    ]);
                }
                break;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Laporan $laporan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laporan $laporan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLaporanRequest $request, Laporan $laporan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laporan $laporan)
    {
        //
    }

    /**
     * Store multiple laporan sekaligus dari form upload di halaman industri
     */
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'industri_id' => 'required|exists:industri,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
        ]);

        try {
            DB::beginTransaction();

            $uploaded = 0;
            $errors = [];
            $skipped = [];
            $jenisLaporan = \App\Models\Laporan::JENIS_LAPORAN;

            // Buat tanggal dari bulan dan tahun
            $tanggal = $request->tahun . '-' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . '-01';

            // Loop untuk setiap jenis laporan (0-4)
            for ($i = 0; $i < count($jenisLaporan); $i++) {
                $fileKey = 'laporan_' . $i;
                $jenisKey = 'jenis_laporan_' . $i;

                // Skip jika file tidak diupload
                if (!$request->hasFile($fileKey)) {
                    continue;
                }

                $file = $request->file($fileKey);
                $jenis = $request->input($jenisKey);

                // Validasi file
                if (!$file->isValid()) {
                    $errors[] = $jenis . ': File tidak valid';
                    continue;
                }

                // Cek apakah sudah ada laporan untuk jenis ini di bulan dan tahun yang sama
                $existingLaporan = Laporan::where('industri_id', $request->industri_id)
                    ->where('jenis_laporan', $jenis)
                    ->whereYear('tanggal', $request->tahun)
                    ->whereMonth('tanggal', $request->bulan)
                    ->first();

                if ($existingLaporan) {
                    $skipped[] = $jenis;
                    continue;
                }

                // Upload dan proses file
                try {
                    $fileName = time() . '_' . $i . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('temp', $fileName, 'local');

                    // Baca dan validasi Excel
                    $previewData = $this->readAndValidateExcel($filePath, $jenis);

                    // Simpan laporan master
                    $laporan = Laporan::create([
                        'industri_id' => $request->industri_id,
                        'jenis_laporan' => $jenis,
                        'tanggal' => $tanggal,
                        'path_laporan' => '',
                    ]);

                    // Simpan detail
                    $this->saveDetailData($laporan, $jenis, $previewData['rows']);

                    // Hapus file temporary
                    if (Storage::exists($filePath)) {
                        Storage::delete($filePath);
                    }

                    $uploaded++;
                } catch (\Exception $e) {
                    $errors[] = $jenis . ': ' . $e->getMessage();
                }
            }

            DB::commit();

            // Buat pesan response
            $message = '';
            if ($uploaded > 0) {
                $message .= "Berhasil mengupload $uploaded laporan. ";
            }
            if (count($skipped) > 0) {
                $message .= count($skipped) . ' laporan dilewati karena sudah ada: ' . implode(', ', array_map(function($s) {
                    return '"' . $s . '"';
                }, $skipped)) . '. ';
            }
            if (count($errors) > 0) {
                $message .= count($errors) . ' laporan gagal: ' . implode('; ', $errors);
            }

            if ($uploaded > 0) {
                return redirect()->route('industri.laporan', $request->industri_id)
                    ->with('success', $message);
            } else {
                return redirect()->route('industri.laporan', $request->industri_id)
                    ->with('warning', $message ?: 'Tidak ada laporan yang diupload.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('industri.laporan', $request->industri_id)
                ->with('error', 'Gagal mengupload laporan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman rekap laporan dengan filter bulan dan tahun
     */
    public function rekapLaporan(Request $request)
    {
        // Ambil filter dari request atau gunakan bulan dan tahun saat ini
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Query untuk mendapatkan laporan berdasarkan bulan dan tahun
        $laporans = Laporan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Inisialisasi array untuk menyimpan rekap data
        $rekap = [
            'penerimaan_kayu_bulat' => [
                'total_dokumen' => 0,
                'total_batang' => 0,
                'total_volume' => 0,
                'jenis_kayu_terbanyak' => null,
            ],
            'penerimaan_kayu_olahan' => [
                'total_dokumen' => 0,
                'total_keping' => 0,
                'total_volume' => 0,
                'jenis_olahan_terbanyak' => null,
            ],
            'mutasi_kayu_bulat' => [
                'total_persediaan_awal' => 0,
                'total_penggunaan' => 0,
                'total_persediaan_akhir' => 0,
                'jenis_kayu_terbanyak' => null,
            ],
            'mutasi_kayu_olahan' => [
                'total_persediaan_awal' => 0,
                'total_penggunaan' => 0,
                'total_persediaan_akhir' => 0,
                'jenis_olahan_terbanyak' => null,
            ],
            'penjualan_kayu_olahan' => [
                'total_dokumen' => 0,
                'total_keping' => 0,
                'total_volume' => 0,
                'tujuan_terbanyak' => null,
            ],
        ];

        // Ambil ID laporan untuk periode ini
        $laporanIds = $laporans->pluck('id');

        // Rekap Penerimaan Kayu Bulat
        $penerimaanBulat = laporan_penerimaan_kayu_bulat::whereIn('laporan_id', $laporanIds)->get();
        $rekap['penerimaan_kayu_bulat']['total_dokumen'] = $penerimaanBulat->count();
        $rekap['penerimaan_kayu_bulat']['total_batang'] = $penerimaanBulat->sum('jumlah_batang');
        $rekap['penerimaan_kayu_bulat']['total_volume'] = $penerimaanBulat->sum('volume');
        $jenisKayuBulat = $penerimaanBulat->groupBy('jenis_kayu')->map->count()->sortDesc();
        $rekap['penerimaan_kayu_bulat']['jenis_kayu_terbanyak'] = $jenisKayuBulat->keys()->first();

        // Rekap Penerimaan Kayu Olahan
        $penerimaanOlahan = laporan_penerimaan_kayu_olahan::whereIn('laporan_id', $laporanIds)->get();
        $rekap['penerimaan_kayu_olahan']['total_dokumen'] = $penerimaanOlahan->count();
        $rekap['penerimaan_kayu_olahan']['total_keping'] = $penerimaanOlahan->sum('jumlah_keping');
        $rekap['penerimaan_kayu_olahan']['total_volume'] = $penerimaanOlahan->sum('volume');
        $jenisOlahanPenerimaan = $penerimaanOlahan->groupBy('jenis_olahan')->map->count()->sortDesc();
        $rekap['penerimaan_kayu_olahan']['jenis_olahan_terbanyak'] = $jenisOlahanPenerimaan->keys()->first();

        // Rekap Mutasi Kayu Bulat
        $mutasiBulat = laporan_mutasi_kayu_bulat::whereIn('laporan_id', $laporanIds)->get();
        $rekap['mutasi_kayu_bulat']['total_persediaan_awal'] = $mutasiBulat->sum('persediaan_awal_volume');
        $rekap['mutasi_kayu_bulat']['total_penggunaan'] = $mutasiBulat->sum('penggunaan_pengurangan_volume');
        $rekap['mutasi_kayu_bulat']['total_persediaan_akhir'] = $mutasiBulat->sum('persediaan_akhir_volume');
        $jenisKayuMutasi = $mutasiBulat->groupBy('jenis_kayu')->map->count()->sortDesc();
        $rekap['mutasi_kayu_bulat']['jenis_kayu_terbanyak'] = $jenisKayuMutasi->keys()->first();

        // Rekap Mutasi Kayu Olahan
        $mutasiOlahan = laporan_mutasi_kayu_olahan::whereIn('laporan_id', $laporanIds)->get();
        $rekap['mutasi_kayu_olahan']['total_persediaan_awal'] = $mutasiOlahan->sum('persediaan_awal_volume');
        $rekap['mutasi_kayu_olahan']['total_penggunaan'] = $mutasiOlahan->sum('penggunaan_pengurangan_volume');
        $rekap['mutasi_kayu_olahan']['total_persediaan_akhir'] = $mutasiOlahan->sum('persediaan_akhir_volume');
        $jenisOlahanMutasi = $mutasiOlahan->groupBy('jenis_olahan')->map->count()->sortDesc();
        $rekap['mutasi_kayu_olahan']['jenis_olahan_terbanyak'] = $jenisOlahanMutasi->keys()->first();

        // Rekap Penjualan Kayu Olahan
        $penjualanOlahan = laporan_penjualan_kayu_olahan::whereIn('laporan_id', $laporanIds)->get();
        $rekap['penjualan_kayu_olahan']['total_dokumen'] = $penjualanOlahan->count();
        $rekap['penjualan_kayu_olahan']['total_keping'] = $penjualanOlahan->sum('jumlah_keping');
        $rekap['penjualan_kayu_olahan']['total_volume'] = $penjualanOlahan->sum('volume');
        $tujuanTerbanyak = $penjualanOlahan->groupBy('tujuan_kirim')->map->count()->sortDesc();
        $rekap['penjualan_kayu_olahan']['tujuan_terbanyak'] = $tujuanTerbanyak->keys()->first();

        // Hitung insights tambahan
        $insights = [
            'total_laporan' => $laporans->count(),
            'total_volume_masuk' => $rekap['penerimaan_kayu_bulat']['total_volume'] + $rekap['penerimaan_kayu_olahan']['total_volume'],
            'total_volume_keluar' => $rekap['penjualan_kayu_olahan']['total_volume'],
            'efisiensi_produksi' => 0,
            'total_industri_aktif' => $laporans->groupBy('industri_id')->count(),
        ];

        // Hitung efisiensi produksi (volume keluar / volume masuk kayu bulat)
        if ($rekap['penerimaan_kayu_bulat']['total_volume'] > 0) {
            $insights['efisiensi_produksi'] = ($rekap['penjualan_kayu_olahan']['total_volume'] / $rekap['penerimaan_kayu_bulat']['total_volume']) * 100;
        }

        // Siapkan detail data untuk ditampilkan
        $detailData = [
            'penerimaan_bulat' => $penerimaanBulat,
            'penerimaan_olahan' => $penerimaanOlahan,
            'mutasi_bulat' => $mutasiBulat,
            'mutasi_olahan' => $mutasiOlahan,
            'penjualan_olahan' => $penjualanOlahan,
        ];

        return view('laporan.rekapLaporan', compact('rekap', 'insights', 'bulan', 'tahun', 'detailData'));
    }

    /**
     * Menampilkan halaman detail data laporan (tabel lengkap) berdasarkan jenis, bulan, dan tahun
     */
    public function detailLaporan(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $jenis = (string) $request->input('jenis', '');

        $jenisOptions = [
            'penerimaan_kayu_bulat' => 'Laporan Penerimaan Kayu Bulat',
            'penerimaan_kayu_olahan' => 'Laporan Penerimaan Kayu Olahan',
            'mutasi_kayu_bulat' => 'Laporan Mutasi Kayu Bulat (LMKB)',
            'mutasi_kayu_olahan' => 'Laporan Mutasi Kayu Olahan (LMKO)',
            'penjualan_kayu_olahan' => 'Laporan Penjualan Kayu Olahan',
        ];

        if (!array_key_exists($jenis, $jenisOptions)) {
            return redirect()->route('laporan.rekap', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('error', 'Jenis laporan tidak valid.');
        }

        $laporanIds = Laporan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->pluck('id');

        $items = collect();
        $filterOptions = [];

        switch ($jenis) {
            case 'penerimaan_kayu_bulat':
                $query = laporan_penerimaan_kayu_bulat::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);
                
                // Filter: jenis_kayu
                if ($request->filled('jenis_kayu')) {
                    $query->where('jenis_kayu', $request->jenis_kayu);
                }
                
                // Filter: asal_kayu
                if ($request->filled('asal_kayu')) {
                    $query->where('asal_kayu', $request->asal_kayu);
                }
                
                $items = $query->orderBy('tanggal')->get();
                
                // Siapkan opsi filter
                $filterOptions['jenis_kayu'] = laporan_penerimaan_kayu_bulat::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_kayu')->sort()->values();
                $filterOptions['asal_kayu'] = laporan_penerimaan_kayu_bulat::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('asal_kayu')->sort()->values();
                break;

            case 'penerimaan_kayu_olahan':
                $query = laporan_penerimaan_kayu_olahan::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);
                
                // Filter: jenis_olahan
                if ($request->filled('jenis_olahan')) {
                    $query->where('jenis_olahan', $request->jenis_olahan);
                }
                
                // Filter: asal_kayu
                if ($request->filled('asal_kayu')) {
                    $query->where('asal_kayu', $request->asal_kayu);
                }
                
                $items = $query->orderBy('tanggal')->get();
                
                // Siapkan opsi filter
                $filterOptions['jenis_olahan'] = laporan_penerimaan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_olahan')->sort()->values();
                $filterOptions['asal_kayu'] = laporan_penerimaan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('asal_kayu')->sort()->values();
                break;

            case 'mutasi_kayu_bulat':
                $query = laporan_mutasi_kayu_bulat::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);
                
                // Filter: jenis_kayu
                if ($request->filled('jenis_kayu')) {
                    $query->where('jenis_kayu', $request->jenis_kayu);
                }
                
                $items = $query->orderBy('jenis_kayu')->get();
                
                // Siapkan opsi filter
                $filterOptions['jenis_kayu'] = laporan_mutasi_kayu_bulat::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_kayu')->sort()->values();
                break;

            case 'mutasi_kayu_olahan':
                $query = laporan_mutasi_kayu_olahan::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);
                
                // Filter: jenis_olahan
                if ($request->filled('jenis_olahan')) {
                    $query->where('jenis_olahan', $request->jenis_olahan);
                }
                
                $items = $query->orderBy('jenis_olahan')->get();
                
                // Siapkan opsi filter
                $filterOptions['jenis_olahan'] = laporan_mutasi_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_olahan')->sort()->values();
                break;

            case 'penjualan_kayu_olahan':
                $query = laporan_penjualan_kayu_olahan::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);
                
                // Filter: tujuan_kirim
                if ($request->filled('tujuan_kirim')) {
                    $query->where('tujuan_kirim', $request->tujuan_kirim);
                }
                
                // Filter: jenis_olahan
                if ($request->filled('jenis_olahan')) {
                    $query->where('jenis_olahan', $request->jenis_olahan);
                }
                
                // Filter: ekspor/impor (dari keterangan)
                if ($request->filled('ekspor_impor')) {
                    $query->where('keterangan', 'LIKE', '%' . $request->ekspor_impor . '%');
                }
                
                $items = $query->orderBy('tanggal')->get();
                
                // Siapkan opsi filter
                $filterOptions['tujuan_kirim'] = laporan_penjualan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('tujuan_kirim')->sort()->values();
                $filterOptions['jenis_olahan'] = laporan_penjualan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_olahan')->sort()->values();
                break;
        }

        return view('laporan.detailLaporan', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jenis' => $jenis,
            'jenisLabel' => $jenisOptions[$jenis],
            'items' => $items,
            'filterOptions' => $filterOptions,
        ]);
    }
}
