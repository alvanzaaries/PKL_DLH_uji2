<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\LaporanValidationService;
use App\Services\LaporanDataService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdateLaporanRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanController extends Controller
{
    protected $validationService;
    protected $dataService;
    protected $exportService;

    public function __construct(
        LaporanValidationService $validationService,
        LaporanDataService $dataService,
        \App\Services\LaporanExportService $exportService
    ) {
        $this->validationService = $validationService;
        $this->dataService = $dataService;
        $this->exportService = $exportService;
    }

    /**
     * Show landing page for Laporan module
     */
    public function landing()
    {
        return view('laporan.landingLaporan');
    }

    /**
     * Preview data Excel sebelum disimpan
     */
    public function preview(Request $request)
    {
        // Detect input mode: manual or excel
        $isManualMode = $request->has('manual_data') && is_array($request->manual_data) && count($request->manual_data) > 0;

        // Conditional validation based on mode
        if ($isManualMode) {
            $request->validate([
                'industri_id' => 'required|exists:industries,id',
                'bulan' => 'required|integer|between:1,12',
                'tahun' => 'required|integer|min:2020',
                'jenis_laporan' => 'required|string',
                'manual_data' => 'required|array|min:1',
            ]);
        } else {
            $request->validate([
                'industri_id' => 'required|exists:industries,id',
                'bulan' => 'required|integer|between:1,12',
                'tahun' => 'required|integer|min:2020',
                'jenis_laporan' => 'required|string',
                'file_excel' => 'required|file|mimes:xlsx,xls|max:5120',
            ]);
        }

        // Validasi unique: cek apakah sudah ada laporan untuk industri, bulan, tahun, dan jenis yang sama
        $existingLaporan = Laporan::where('industri_id', $request->industri_id)
            ->where('jenis_laporan', $request->jenis_laporan)
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->first();

        if ($existingLaporan) {
            // Clear session to avoid stale data
            session()->forget(['preview_data', 'preview_file_path', 'preview_metadata']);

            return back()
                ->withInput()
                ->with('error', 'Laporan jenis "' . $request->jenis_laporan . '" untuk bulan ' . $request->bulan . ' tahun ' . $request->tahun . ' sudah pernah diupload. Setiap jenis laporan hanya bisa diupload sekali per bulan.');
        }

        try {
            $jenisLaporan = $request->jenis_laporan;
            $filePath = null;

            if ($isManualMode) {
                // Process manual data
                $previewData = $this->validationService->validateManualData($request->manual_data, $jenisLaporan);
            } else {
                // Upload file sementara
                $file = $request->file('file_excel');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('temp', $fileName, 'local');

                // Baca Excel dan validasi format sesuai jenis laporan menggunakan service
                $previewData = $this->validationService->readAndValidateExcel($filePath, $jenisLaporan);
            }

            // Simpan data ke session untuk proses simpan nanti
            session([
                'preview_data' => $previewData,
                'preview_file_path' => $filePath, // Will be null for manual mode
                'preview_metadata' => [
                    'industri_id' => $request->industri_id,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'jenis_laporan' => $jenisLaporan,
                    'is_manual' => $isManualMode, // Flag to indicate manual mode
                ],
                'redirect_after_save' => url()->previous(), // Simpan URL sebelumnya
                'is_fresh_upload' => true, // Flag untuk clear localStorage di view
            ]);

            // Redirect to GET preview which will read preview data from session and paginate
            return redirect()->route('laporan.preview.show');

        } catch (\Exception $e) {
            Log::error('Laporan preview failed', ['exception' => $e]);

            // Clear session to avoid stale data
            session()->forget(['preview_data', 'preview_file_path', 'preview_metadata']);

            return back()
                ->withInput()
                ->with('error', 'Gagal memproses data. ' . $e->getMessage());
        }
    }
    /**
     * Show preview page by reading preview data from session (GET).
     */
    public function showPreview(Request $request)
    {
        if (!session()->has('preview_data')) {
            return redirect()->route('laporan.upload.form')
                ->with('error', 'Data preview tidak ditemukan. Silakan upload ulang.');
        }

        $previewData = session('preview_data');
        $metadata = session('preview_metadata', []);

        $allRows = $previewData['rows'] ?? [];
        $total = count($allRows);

        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        if ($perPage <= 0)
            $perPage = 10;
        if ($page <= 0)
            $page = 1;

        $offset = ($page - 1) * $perPage;
        $currentItems = array_slice($allRows, $offset, $perPage);

        $paginator = new LengthAwarePaginator($currentItems, $total, $perPage, $page, [
            'path' => url()->current(),
            'query' => $request->query()
        ]);

        // Map validation errors to source_row (if possible) so the view can show highlighted rows
        $errorsByRow = [];
        $generalErrors = [];
        foreach ($previewData['errors'] ?? [] as $err) {
            if (preg_match('/Baris\s+(\d+):/i', $err, $m)) {
                $rowNum = (int) $m[1];
                $errorsByRow[$rowNum][] = $err;
            } else {
                $generalErrors[] = $err;
            }
        }

        // Build displayErrors: enhance error messages to show both Excel row and data row numbers
        $displayErrors = [];
        // map source_row => continuous data row number (1-based) for ALL rows, not just current page
        $sourceToDisplay = [];
        foreach ($allRows as $idx => $item) {
            if (is_array($item) && isset($item['source_row'])) {
                $sourceToDisplay[(int) $item['source_row']] = $idx + 1; // continuous numbering from all data
            }
        }

        foreach ($previewData['errors'] ?? [] as $err) {
            if (preg_match('/Baris\s+(\d+):/i', $err, $m)) {
                $src = (int) $m[1];
                if (isset($sourceToDisplay[$src])) {
                    $disp = $sourceToDisplay[$src];
                    // Show both Excel row and data row: "Baris Excel 16 (Data #1): ..."
                    $displayErrors[] = preg_replace('/Baris\s+' . $src . ':/i', 'Baris Excel ' . $src . ' (Data #' . $disp . '):', $err);
                    continue;
                }
            }
            $displayErrors[] = $err;
        }

        // Check if this is a fresh upload (consume the flag)
        $isFreshUpload = session('is_fresh_upload', false);
        session()->forget('is_fresh_upload');

        return view('laporan/previewLaporan', [
            'data' => $previewData,
            'metadata' => $metadata,
            'paginatedRows' => $paginator,
            'errorsByRow' => $errorsByRow,
            'generalErrors' => $generalErrors,
            'displayErrors' => $displayErrors,
            'isFreshUpload' => $isFreshUpload,
        ]);
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
     * Show upload form with company selection
     */
    public function showUploadForm()
    {
        $industries = \App\Models\Industri::orderBy('nama')->get();

        return view('laporan/uploadLaporan', [
            'industries' => $industries
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
            // Already no session, just redirect
            return redirect()->route('laporan.upload.form')
                ->with('error', 'Data preview tidak ditemukan. Silakan upload ulang.');
        }

        // Ambil preview data dari session
        $previewData = session('preview_data');

        // Jika ini request revalidasi saja (user klik "Perbaiki & Validasi Ulang"),
        // jalankan validasi ulang dan simpan hasilnya ke session, kemudian kembalikan ke preview.
        if ($request->has('revalidate_only') && $request->revalidate_only) {
            $filePath = session('preview_file_path');

            // Default gunakan rows dari session
            $dataRows = $previewData['rows'] ?? [];

            // Jika user mengirim edited_data (halaman saat ini atau full), gunakan itu untuk revalidasi
            $rowNumberMap = [];
            if ($request->has('edited_data') && !empty($request->edited_data)) {
                $editedData = json_decode($request->edited_data, true);
                if (is_array($editedData) && count($editedData) > 0) {
                    $dataRows = $editedData;
                    // Build map from validation index to original source_row (if available in session preview)
                    $sessionRows = $previewData['rows'] ?? [];
                    for ($i = 0; $i < count($dataRows); $i++) {
                        if (isset($sessionRows[$i]) && is_array($sessionRows[$i]) && isset($sessionRows[$i]['source_row'])) {
                            $rowNumberMap[$i] = $sessionRows[$i]['source_row'];
                        }
                    }
                }
            }

            $validationResult = $this->validateEditedDataUsingService($dataRows, $request->jenis_laporan, $rowNumberMap);

            // Wrap rows with source_row metadata so preview shows original Excel row numbers
            $sessionRows = $previewData['rows'] ?? [];
            $wrapped = [];
            foreach ($validationResult['rows'] as $i => $r) {
                $source = $rowNumberMap[$i] ?? ($sessionRows[$i]['source_row'] ?? ($i + 1));
                $wrapped[] = ['cells' => $r, 'source_row' => $source];
            }
            $validationResult['rows'] = $wrapped;
            $validationResult['total'] = count($wrapped);

            // Simpan hasil validasi kembali ke session agar preview menampilkan hasil terbaru
            session([
                'preview_data' => $validationResult,
                'preview_file_path' => $filePath,
                'preview_metadata' => [
                    'industri_id' => $request->industri_id,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'jenis_laporan' => $request->jenis_laporan,
                ],
                'redirect_after_save' => session('redirect_after_save')
            ]);

            if (!empty($validationResult['errors'])) {
                return redirect()->route('laporan.preview.show')
                    ->with('warning', 'Masih ada ' . count($validationResult['errors']) . ' error validasi. Silakan perbaiki data yang ditandai merah.');
            } else {
                // Flash a distinct flag so the view can react to a successful revalidation
                return redirect()->route('laporan.preview.show')
                    ->with('success', 'Validasi ulang berhasil. Data sudah bersih, silakan konfirmasi dan simpan.')
                    ->with('revalidation_ok', true);
            }
        }

        // Jika preview sebelumnya mengandung error dan user tidak mengirim edited_data,
        // jangan coba simpan langsung — minta user melakukan revalidasi.
        // PENTING: Setelah revalidasi berhasil, preview_data['errors'] sudah kosong,
        // tapi kita tetap perlu edited_data untuk memastikan data yang benar tersimpan.
        if (!empty($previewData['errors'])) {
            // Ada error di preview, user harus perbaiki dulu
            if (!$request->has('edited_data') || empty($request->edited_data)) {
                return redirect()->route('laporan.preview.show')
                    ->with('warning', 'Masih ada error validasi. Silakan perbaiki data di tabel lalu tekan "Perbaiki & Validasi Ulang".');
            }
        }

        // Validasi unique lagi sebelum menyimpan (double check)
        $existingLaporan = Laporan::where('industri_id', $request->industri_id)
            ->where('jenis_laporan', $request->jenis_laporan)
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->first();

        if ($existingLaporan) {
            // Clear session to avoid stale data
            session()->forget(['preview_data', 'preview_file_path', 'preview_metadata']);

            return redirect()->route('laporan.upload.form')
                ->with('error', 'Laporan jenis "' . $request->jenis_laporan . '" untuk bulan ' . $request->bulan . ' tahun ' . $request->tahun . ' sudah pernah diupload.');
        }

        try {
            DB::beginTransaction();

            // Ambil data dari session
            $previewData = session('preview_data');
            $filePath = session('preview_file_path');

            // Cek apakah ada edited data dari form (data yang sudah diedit user)
            $dataRows = $previewData['rows'];
            $needsRevalidation = false;

            if ($request->has('edited_data') && !empty($request->edited_data)) {
                $editedData = json_decode($request->edited_data, true);
                if (is_array($editedData) && count($editedData) > 0) {
                    $dataRows = $editedData;
                    // map for final save revalidation
                    $sessionRows = $previewData['rows'] ?? [];
                    $rowNumberMap = [];
                    for ($i = 0; $i < count($dataRows); $i++) {
                        if (isset($sessionRows[$i]) && is_array($sessionRows[$i]) && isset($sessionRows[$i]['source_row'])) {
                            $rowNumberMap[$i] = $sessionRows[$i]['source_row'];
                        }
                    }
                    $needsRevalidation = true;
                }
            }

            // Jika ada data yang diedit, validasi ulang
            if ($needsRevalidation) {
                // Buat temporary file dengan data yang sudah diedit untuk validasi
                // Kita akan validasi manual menggunakan validation service
                $validationResult = $this->validateEditedDataUsingService($dataRows, $request->jenis_laporan, $rowNumberMap ?? []);

                if (!empty($validationResult['errors'])) {
                    // Masih ada error, simpan preview yang sudah divalidasi ulang ke session (wrap rows)
                    $sessionRows = $previewData['rows'] ?? [];
                    $wrapped = [];
                    foreach ($validationResult['rows'] as $i => $r) {
                        $source = $rowNumberMap[$i] ?? ($sessionRows[$i]['source_row'] ?? ($i + 1));
                        $wrapped[] = ['cells' => $r, 'source_row' => $source];
                    }
                    $validationResult['rows'] = $wrapped;
                    $validationResult['total'] = count($wrapped);

                    session([
                        'preview_data' => $validationResult,
                        'preview_file_path' => $filePath,
                        'preview_metadata' => [
                            'industri_id' => $request->industri_id,
                            'bulan' => $request->bulan,
                            'tahun' => $request->tahun,
                            'jenis_laporan' => $request->jenis_laporan,
                        ],
                        'redirect_after_save' => session('redirect_after_save')
                    ]);

                    // Redirect ke route GET preview yang mengambil data dari session
                    return redirect()->route('laporan.preview.show')
                        ->with('warning', 'Masih ada ' . count($validationResult['errors']) . ' error validasi. Silakan perbaiki data yang ditandai merah.');
                }
            }

            // Buat tanggal dari bulan dan tahun
            $tanggal = $request->tahun . '-' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . '-01';

            // Simpan laporan master
            $laporan = Laporan::create([
                'industri_id' => $request->industri_id,
                'jenis_laporan' => $request->jenis_laporan,
                'tanggal' => $tanggal,
                'path_laporan' => '', // String kosong untuk memenuhi constraint NOT NULL
            ]);

            // Normalisasi rows agar konsisten (flat array) sebelum dikirim ke service
            // Meskipun service sudah handle, ini best practice untuk memastikan data bersih
            $cleanedRows = array_map(function ($item) {
                return isset($item['cells']) ? $item['cells'] : $item;
            }, $dataRows);

            // Simpan detail berdasarkan jenis laporan menggunakan service (gunakan edited data jika ada)
            Log::info('Starting detail data save', [
                'laporan_id' => $laporan->id,
                'jenis' => $request->jenis_laporan,
                'rows_count' => count($cleanedRows)
            ]);

            $this->dataService->saveDetailData($laporan, $request->jenis_laporan, $cleanedRows);

            Log::info('Detail data saved successfully', ['laporan_id' => $laporan->id]);

            DB::commit();

            Log::info('Transaction committed successfully', ['laporan_id' => $laporan->id]);

            // PDF now generated on-demand when user views/downloads the receipt

            // Hapus file temporary
            if ($filePath && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            // Ambil URL redirect dari session
            $redirectUrl = session('redirect_after_save');

            // Clear session data
            session()->forget(['preview_data', 'preview_file_path', 'preview_metadata', 'redirect_after_save']);

            Log::info('Redirecting after save', [
                'laporan_id' => $laporan->id,
                'redirect_url' => $redirectUrl ?? 'laporan.industri'
            ]);

            // Redirect ke halaman sebelumnya atau default ke industri laporan
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Laporan berhasil disimpan!')->with('save_ok', true);
            } else {
                return redirect()->route('laporan.industri', ['industri' => $request->industri_id])
                    ->with('success', 'Laporan berhasil disimpan!')->with('save_ok', true);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            // Enhanced logging with more context
            Log::error('Failed to save laporan', [
                'exception' => $e,
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
                'industri_id' => $request->industri_id ?? null,
                'jenis_laporan' => $request->jenis_laporan ?? null,
                'bulan' => $request->bulan ?? null,
                'tahun' => $request->tahun ?? null,
                'has_edited_data' => $request->has('edited_data'),
                'preview_data_exists' => session()->has('preview_data'),
                'preview_data_row_count' => isset($previewData['rows']) ? count($previewData['rows']) : 0,
            ]);

            // Clear session to avoid stale data
            session()->forget(['preview_data', 'preview_file_path', 'preview_metadata']);

            // Gunakan redirect URL dari session atau fallback ke laporan.industri
            $redirectUrl = session('redirect_after_save');

            // More descriptive error message
            $msg = 'Gagal menyimpan laporan. ';

            // Add specific error details based on exception type
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                $msg .= 'Data duplikat terdeteksi. Laporan untuk periode ini mungkin sudah ada.';
            } elseif (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                $msg .= 'Terjadi kesalahan database. Periksa format data Anda.';
            } elseif (strpos($e->getMessage(), 'Undefined') !== false || strpos($e->getMessage(), 'null') !== false) {
                $msg .= 'Data tidak lengkap. Silakan validasi ulang data Anda sebelum menyimpan.';
            } else {
                $msg .= 'Error: ' . $e->getMessage();
            }

            if ($redirectUrl) {
                return redirect($redirectUrl)->with('error', $msg);
            } else {
                return redirect()->route('laporan.industri', ['industri' => $request->industri_id])
                    ->with('error', $msg);
            }
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
     * Menampilkan halaman rekap laporan dengan statistik berdasarkan kategori
     */
    public function rekapLaporan(Request $request)
    {
        // Ambil parameter dari request
        $tahun = $request->input('tahun');
        $kategori = $request->input('kategori', 'produksi_kayu_bulat'); // Default ke produksi_kayu_bulat
        $groupBy = $request->input('groupBy', 'asal_kayu'); // Default ke asal_kayu
        $eksporLokal = $request->input('eksporLokal', 'semua'); // Default ke semua

        // Validasi kategori
        $validKategori = ['produksi_kayu_bulat', 'produksi_kayu_olahan', 'penjualan'];
        if (!in_array($kategori, $validKategori)) {
            $kategori = 'produksi_kayu_bulat';
        }

        // Validasi groupBy berdasarkan kategori
        if ($kategori === 'produksi_kayu_bulat') {
            $validGroupBy = ['kabupaten', 'asal_kayu'];
            $groupBy = in_array($groupBy, $validGroupBy) ? $groupBy : 'kabupaten';
        } elseif ($kategori === 'produksi_kayu_olahan') {
            $validGroupBy = ['asal_kayu', 'jenis_olahan'];
            $groupBy = in_array($groupBy, $validGroupBy) ? $groupBy : 'asal_kayu';
        } elseif ($kategori === 'penjualan') {
            $validGroupBy = ['tujuan_kirim', 'jenis_olahan'];
            $groupBy = in_array($groupBy, $validGroupBy) ? $groupBy : 'tujuan_kirim';
        } else {
            $groupBy = 'asal_kayu'; // Default untuk kategori lain
        }

        // Validasi eksporLokal (hanya untuk kategori penjualan)
        $validEksporLokal = ['ekspor', 'lokal', 'semua'];
        if (!in_array($eksporLokal, $validEksporLokal)) {
            $eksporLokal = 'semua';
        }

        // Ambil tahun paling awal dari database untuk dropdown filter
        $earliestYear = Laporan::selectRaw('MIN(YEAR(tanggal)) as min_year')->value('min_year') ?? 2020;

        // Inisialisasi data rekap
        $rekapData = [];

        // Jika tahun dipilih, ambil data dari service
        if ($tahun) {
            $rekapData = $this->dataService->getRekapTahunan($tahun, $kategori, $groupBy, $eksporLokal);
        }

        return view('laporan.rekapLaporan', compact('tahun', 'kategori', 'earliestYear', 'rekapData', 'groupBy', 'eksporLokal'));
    }

    /**
     * Menampilkan halaman detail data laporan berdasarkan master laporan id dan industri
     * Route: /laporan/{industri}/detail/{id}
     */
    public function detailLaporan(Request $request, $industri, $id)
    {
        // Temukan master laporan
        $laporan = Laporan::findOrFail($id);

        // Pastikan laporan ini milik industri yang diberikan di URL
        if ((int) $laporan->industri_id !== (int) $industri) {
            abort(404);
        }

        // Derive periode dari master laporan
        $bulan = (int) \Carbon\Carbon::parse($laporan->tanggal)->month;
        $tahun = (int) \Carbon\Carbon::parse($laporan->tanggal)->year;
        $perPage = $request->input('per_page', 25); // Default 25 items per page

        $jenisOptions = [
            'penerimaan_kayu_bulat' => 'Laporan Penerimaan Kayu Bulat',
            'penerimaan_kayu_olahan' => 'Laporan Penerimaan Kayu Olahan',
            'mutasi_kayu_bulat' => 'Laporan Mutasi Kayu Bulat (LMKB)',
            'mutasi_kayu_olahan' => 'Laporan Mutasi Kayu Olahan (LMKO)',
            'penjualan_kayu_olahan' => 'Laporan Penjualan Kayu Olahan',
        ];

        // Cari slug jenis berdasarkan label yang tersimpan di master laporan
        $jenis = array_search($laporan->jenis_laporan, $jenisOptions);
        if ($jenis === false) {
            return redirect()->route('laporan.rekap', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('error', 'Jenis laporan tidak valid.');
        }

        // Build filters array dari query (jika ada)
        $filters = [];
        if ($request->filled('jenis_kayu'))
            $filters['jenis_kayu'] = $request->jenis_kayu;
        if ($request->filled('asal_kayu'))
            $filters['asal_kayu'] = $request->asal_kayu;
        if ($request->filled('jenis_olahan'))
            $filters['jenis_olahan'] = $request->jenis_olahan;
        if ($request->filled('tujuan_kirim'))
            $filters['tujuan_kirim'] = $request->tujuan_kirim;
        if ($request->filled('ekspor_impor'))
            $filters['ekspor_impor'] = $request->ekspor_impor;

        // Pastikan service hanya mengembalikan data untuk industri laporan ini
        $filters['industri_id'] = $laporan->industri_id;

        // Ambil data detail via service dengan pagination
        $detailData = $this->dataService->getDetailLaporan($bulan, $tahun, $jenis, $filters, $perPage);

        return view('laporan.detailLaporan', [
            'laporan_id' => $id,
            'industri_id' => $laporan->industri_id,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jenis' => $jenis,
            'jenisLabel' => $jenisOptions[$jenis],
            'items' => $detailData['items'],
            'filterOptions' => $detailData['filterOptions'],
            'perPage' => $perPage,
        ]);
    }

    /**
     * Hapus laporan beserta data terkait
     */
    public function destroy($industri, $id)
    {
        $laporan = Laporan::findOrFail($id);

        // Pastikan laporan ini milik industri yang dimaksud
        if ($laporan->industri_id != $industri) {
            return redirect()->back()->with('error', 'Data tidak valid');
        }

        // Hapus semua data detail terkait laporan ini berdasarkan jenis laporan
        switch ($laporan->jenis_laporan) {
            case 'Laporan Penerimaan Kayu Bulat':
                \App\Models\laporan_penerimaan_kayu_bulat::where('laporan_id', $id)->delete();
                break;
            case 'Laporan Mutasi Kayu Bulat (LMKB)':
                \App\Models\laporan_mutasi_kayu_bulat::where('laporan_id', $id)->delete();
                break;
            case 'Laporan Penerimaan Kayu Olahan':
                \App\Models\laporan_penerimaan_kayu_olahan::where('laporan_id', $id)->delete();
                break;
            case 'Laporan Mutasi Kayu Olahan (LMKO)':
                \App\Models\laporan_mutasi_kayu_olahan::where('laporan_id', $id)->delete();
                break;
            case 'Laporan Penjualan Kayu Olahan':
                \App\Models\laporan_penjualan_kayu_olahan::where('laporan_id', $id)->delete();
                break;
        }

        // Hapus laporan utama
        $laporan->delete();

        return redirect()->route('laporan.industri', ['industri' => $industri])
            ->with('success', 'Laporan berhasil dihapus');
    }

    /**
     * Validate edited data from preview (array of indexed arrays) by converting to associative format
     * and using the validation service (FIXED for Bug #2)
     */
    private function validateEditedDataUsingService($dataRows, $jenisLaporan, $rowNumberMap = [])
    {
        // Get field mapping untuk jenis laporan ini
        $fieldMappings = [
            'Laporan Penerimaan Kayu Bulat' => [
                'fields' => ['nomor_dokumen', 'tanggal', 'asal_kayu', 'jenis_kayu', 'jumlah_batang', 'volume', 'keterangan'],
            ],
            'Laporan Mutasi Kayu Bulat (LMKB)' => [
                'fields' => ['jenis_kayu', 'persediaan_awal_btg', 'persediaan_awal_volume', 'penambahan_btg', 'penambahan_volume', 'penggunaan_pengurangan_btg', 'penggunaan_pengurangan_volume', 'persediaan_akhir_btg', 'persediaan_akhir_volume', 'keterangan'],
            ],
            'Laporan Penerimaan Kayu Olahan' => [
                'fields' => ['nomor_dokumen', 'tanggal', 'asal_kayu', 'jenis_olahan', 'jumlah_keping', 'volume', 'keterangan'],
            ],
            'Laporan Mutasi Kayu Olahan (LMKO)' => [
                'fields' => ['jenis_olahan', 'persediaan_awal_btg', 'persediaan_awal_volume', 'penambahan_btg', 'penambahan_volume', 'penggunaan_pengurangan_btg', 'penggunaan_pengurangan_volume', 'persediaan_akhir_btg', 'persediaan_akhir_volume', 'keterangan'],
            ],
            'Laporan Penjualan Kayu Olahan' => [
                'fields' => ['nomor_dokumen', 'tanggal', 'tujuan_kirim', 'jenis_olahan', 'jumlah_keping', 'volume', 'keterangan'],
            ],
        ];

        if (!isset($fieldMappings[$jenisLaporan])) {
            throw new \Exception('Jenis laporan tidak dikenali: ' . $jenisLaporan);
        }

        $mapping = $fieldMappings[$jenisLaporan];

        // Convert indexed arrays to associative arrays
        $manualData = [];
        foreach ($dataRows as $rowIndex => $row) {
            $assocRow = [];
            foreach ($mapping['fields'] as $fieldIndex => $fieldName) {
                $assocRow[$fieldName] = $row[$fieldIndex] ?? '';
            }
            $manualData[] = $assocRow;
        }

        // Use existing validation service (which has the Bug #1 fix)
        return $this->validationService->validateManualData($manualData, $jenisLaporan);
    }

    /**
     * Validasi single row berdasarkan jenis laporan
     */
    private function validateRowByType($row, $jenisLaporan, $rowNumber)
    {
        $rowErrors = [];

        switch ($jenisLaporan) {
            case 'Laporan Penerimaan Kayu Bulat':
                // Validasi Nomor Dokumen (kolom 0) - wajib
                if (trim((string) ($row[0] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
                }
                // Validasi Tanggal (kolom 1) - wajib
                if (trim((string) ($row[1] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
                }
                // Validasi Asal Kayu (kolom 2) - wajib
                if (trim((string) ($row[2] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Asal Kayu tidak boleh kosong";
                }
                // Validasi Jenis Kayu (kolom 3) - wajib
                if (trim((string) ($row[3] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Jenis Kayu tidak boleh kosong";
                }
                // Validasi Jumlah Batang (kolom 4) - wajib dan numeric
                $val4 = trim((string) ($row[4] ?? ''));
                if ($val4 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Batang tidak boleh kosong";
                } elseif (!is_numeric($val4) || (float) $val4 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Batang harus berupa angka positif";
                }
                // Validasi Volume (kolom 5) - wajib dan numeric
                $val5 = trim((string) ($row[5] ?? ''));
                if ($val5 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
                } elseif (!is_numeric($val5) || (float) $val5 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Volume harus berupa angka positif";
                }
                break;

            case 'Laporan Mutasi Kayu Bulat (LMKB)':
                // Validasi Jenis Kayu (kolom 0) - wajib
                if (trim((string) ($row[0] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Jenis Kayu tidak boleh kosong";
                }
                // Validasi Persediaan Awal (kolom 1) - WAJIB dan numeric
                $val1 = trim((string) ($row[1] ?? ''));
                if ($val1 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh kosong";
                } elseif (!is_numeric($val1) || (float) $val1 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal harus berupa angka positif";
                }
                // Validasi Penambahan (kolom 2) - WAJIB dan numeric
                $val2 = trim((string) ($row[2] ?? ''));
                if ($val2 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Penambahan tidak boleh kosong";
                } elseif (!is_numeric($val2) || (float) $val2 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Penambahan harus berupa angka positif";
                }
                // Validasi Penggunaan/Pengurangan (kolom 3) - WAJIB dan numeric
                $val3 = trim((string) ($row[3] ?? ''));
                if ($val3 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh kosong";
                } elseif (!is_numeric($val3) || (float) $val3 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan harus berupa angka positif";
                }
                // Validasi Persediaan Akhir (kolom 4) - WAJIB dan numeric
                $val4 = trim((string) ($row[4] ?? ''));
                if ($val4 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh kosong";
                } elseif (!is_numeric($val4) || (float) $val4 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir harus berupa angka positif";
                }
                // VALIDASI LOGIKA MUTASI - Persediaan Akhir = Awal + Penambahan - Penggunaan
                if (
                    $val1 !== '' && $val2 !== '' && $val3 !== '' && $val4 !== '' &&
                    is_numeric($val1) && is_numeric($val2) && is_numeric($val3) && is_numeric($val4)
                ) {
                    $persediaanAwal = (float) $val1;
                    $penambahan = (float) $val2;
                    $penggunaan = (float) $val3;
                    $persediaanAkhir = (float) $val4;

                    $expectedAkhir = $persediaanAwal + $penambahan - $penggunaan;
                    if (abs($expectedAkhir - $persediaanAkhir) > 0.01) {
                        $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak sesuai (seharusnya {$expectedAkhir})";
                    }
                }
                break;

            case 'Laporan Penerimaan Kayu Olahan':
                // Validasi Nomor Dokumen (kolom 0) - wajib
                if (trim((string) ($row[0] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
                }
                // Validasi Tanggal (kolom 1) - wajib
                if (trim((string) ($row[1] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
                }
                // Validasi Asal Kayu (kolom 2) - wajib
                if (trim((string) ($row[2] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Asal Kayu tidak boleh kosong";
                }
                // Validasi Jenis Produk (kolom 3) - wajib
                if (trim((string) ($row[3] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
                }
                // Validasi Jumlah Keping (kolom 4) - wajib dan numeric
                $val4 = trim((string) ($row[4] ?? ''));
                if ($val4 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh kosong";
                } elseif (!is_numeric($val4) || (float) $val4 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping harus berupa angka positif";
                }
                // Validasi Volume (kolom 5) - wajib dan numeric
                $val5 = trim((string) ($row[5] ?? ''));
                if ($val5 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
                } elseif (!is_numeric($val5) || (float) $val5 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Volume harus berupa angka positif";
                }
                break;

            case 'Laporan Mutasi Kayu Olahan (LMKO)':
                // Validasi Jenis Produk (kolom 0) - wajib
                if (trim((string) ($row[0] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
                }
                // Validasi Persediaan Awal (kolom 1) - WAJIB dan numeric
                $val1 = trim((string) ($row[1] ?? ''));
                if ($val1 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh kosong";
                } elseif (!is_numeric($val1) || (float) $val1 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Awal harus berupa angka positif";
                }
                // Validasi Penambahan (kolom 2) - WAJIB dan numeric
                $val2 = trim((string) ($row[2] ?? ''));
                if ($val2 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Penambahan tidak boleh kosong";
                } elseif (!is_numeric($val2) || (float) $val2 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Penambahan harus berupa angka positif";
                }
                // Validasi Penggunaan/Pengurangan (kolom 3) - WAJIB dan numeric
                $val3 = trim((string) ($row[3] ?? ''));
                if ($val3 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh kosong";
                } elseif (!is_numeric($val3) || (float) $val3 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan harus berupa angka positif";
                }
                // Validasi Persediaan Akhir (kolom 4) - WAJIB dan numeric
                $val4 = trim((string) ($row[4] ?? ''));
                if ($val4 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh kosong";
                } elseif (!is_numeric($val4) || (float) $val4 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir harus berupa angka positif";
                }
                // VALIDASI LOGIKA MUTASI - Persediaan Akhir = Awal + Penambahan - Penggunaan
                if (
                    $val1 !== '' && $val2 !== '' && $val3 !== '' && $val4 !== '' &&
                    is_numeric($val1) && is_numeric($val2) && is_numeric($val3) && is_numeric($val4)
                ) {
                    $persediaanAwal = (float) $val1;
                    $penambahan = (float) $val2;
                    $penggunaan = (float) $val3;
                    $persediaanAkhir = (float) $val4;

                    $expectedAkhir = $persediaanAwal + $penambahan - $penggunaan;
                    if (abs($expectedAkhir - $persediaanAkhir) > 0.01) {
                        $rowErrors[] = "Baris {$rowNumber}: Persediaan Akhir tidak sesuai (seharusnya {$expectedAkhir})";
                    }
                }
                break;

            case 'Laporan Penjualan Kayu Olahan':
                // Validasi Nomor Dokumen (kolom 0) - wajib
                if (trim((string) ($row[0] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
                }
                // Validasi Tanggal (kolom 1) - wajib
                if (trim((string) ($row[1] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
                }
                // Validasi Tujuan Kirim (kolom 2) - wajib
                if (trim((string) ($row[2] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Tujuan Kirim tidak boleh kosong";
                }
                // Validasi Jenis Produk (kolom 3) - wajib
                if (trim((string) ($row[3] ?? '')) === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
                }
                // Validasi Jumlah Keping (kolom 4) - wajib dan numeric
                $val4 = trim((string) ($row[4] ?? ''));
                if ($val4 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh kosong";
                } elseif (!is_numeric($val4) || (float) $val4 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Jumlah Keping harus berupa angka positif";
                }
                // Validasi Volume (kolom 5) - wajib dan numeric
                $val5 = trim((string) ($row[5] ?? ''));
                if ($val5 === '') {
                    $rowErrors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
                } elseif (!is_numeric($val5) || (float) $val5 < 0) {
                    $rowErrors[] = "Baris {$rowNumber}: Volume harus berupa angka positif";
                }
                break;
        }

        return $rowErrors;
    }

    /**
     * Generate Bukti Tanda Terima PDF and save to storage.
     * Aggregates all report types for the same industry and month.
     * Updates the Laporan record with the path.
     */
    private function generateBuktiPdf(Laporan $laporan): ?string
    {
        try {
            // Load industri
            $industri = $laporan->industri;
            if (!$industri) {
                Log::warning('Cannot generate PDF: industri not found for laporan id ' . $laporan->id);
                return null;
            }

            // Parse periode from tanggal
            $tanggal = \Carbon\Carbon::parse($laporan->tanggal);
            $bulan = $tanggal->month;
            $tahun = $tanggal->year;
            $bulanNama = $tanggal->translatedFormat('F');

            // Get all reports for this industry in the same month
            $laporansInMonth = Laporan::where('industri_id', $industri->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('jenis_laporan')
                ->get();

            // Get unique report types
            $jenisLaporanList = $laporansInMonth->pluck('jenis_laporan')->unique()->values()->toArray();

            // Use the first laporan ID for the receipt number (8 digits with leading zeros)
            $firstLaporan = $laporansInMonth->first();
            $nomorBukti = str_pad($firstLaporan->id, 8, '0', STR_PAD_LEFT);

            // Prepare data for PDF
            $data = [
                'nomorBukti' => $nomorBukti,
                'namaPerusahaan' => $industri->nama,
                'penanggungJawab' => $industri->penanggungjawab ?? '-',
                'periodeLaporan' => $bulanNama . ' ' . $tahun,
                'jenisLaporanList' => $jenisLaporanList,
                'tanggalDiterima' => $laporan->created_at->translatedFormat('d F Y'),
                'tempatTanggal' => 'Kalimantan Timur, ' . $laporan->created_at->translatedFormat('d F Y'),
            ];

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf.bukti_tanda_terima', $data);

            // Ensure directory exists
            $directory = storage_path('app/public/laporan/proofs');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate filename based on industry and period
            $filename = 'bukti_' . $industri->id . '_' . $bulan . '_' . $tahun . '.pdf';
            $relativePath = 'laporan/proofs/' . $filename;
            $fullPath = storage_path('app/public/' . $relativePath);

            // Save PDF (will overwrite if already exists for this industry/month)
            $pdf->save($fullPath);

            // Update all laporans for this industry/month with the same path (batch update)
            Laporan::where('industri_id', $industri->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->update(['path_laporan' => $relativePath]);

            Log::info('Generated PDF for industri ' . $industri->id . ' periode ' . $bulan . '/' . $tahun . ': ' . $relativePath);

            return $relativePath;

        } catch (\Exception $e) {
            Log::error('Failed to generate PDF for laporan id ' . $laporan->id, [
                'exception' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Public page to search for report proofs.
     * User selects industry, month, year and enters penanggung jawab to verify.
     */
    public function publicSearch(Request $request)
    {
        $industries = \App\Models\Industri::orderBy('nama')->get(['id', 'nama']);
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $currentYear = date('Y');
        $years = range($currentYear, $currentYear - 5);

        // Default state
        $verified = session('verified', false);
        $selectedIndustri = session('selectedIndustri', null);
        $selectedBulan = session('selectedBulan') ? (int) session('selectedBulan') : null;
        $selectedTahun = session('selectedTahun') ? (int) session('selectedTahun') : null;
        $jenisLaporanList = session('jenisLaporanList', []);
        $error = session('error', null);

        // Handle POST submission
        if ($request->isMethod('post')) {
            $request->validate([
                'industri_id' => 'required|exists:industries,id',
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:2020|max:2099',
                'nomor_izin' => 'required|string',
            ]);

            $industri = \App\Models\Industri::find($request->industri_id);

            // Case-insensitive authentication check with nomor_izin
            // Clean both strings: remove spaces, slashes, dashes, dots for flexible matching
            $inputIzin = preg_replace('/[^a-zA-Z0-9]/', '', $request->nomor_izin);
            $dbIzin = preg_replace('/[^a-zA-Z0-9]/', '', $industri->nomor_izin ?? '');

            if ($industri && strtolower($dbIzin) === strtolower($inputIzin)) {

                // Get all reports for this industry in the specified month
                $laporans = Laporan::where('industri_id', $industri->id)
                    ->whereMonth('tanggal', $request->bulan)
                    ->whereYear('tanggal', $request->tahun)
                    ->orderBy('jenis_laporan')
                    ->get();

                if ($laporans->isEmpty()) {
                    return redirect()->route('laporan.bukti')
                        ->withInput()
                        ->with('error', 'Tidak ada laporan untuk periode yang dipilih.');
                }

                $jenisLaporanList = $laporans->pluck('jenis_laporan')->unique()->values()->toArray();

                return redirect()->route('laporan.bukti')
                    ->withInput()
                    ->with([
                        'verified' => true,
                        'selectedIndustri' => $industri,
                        'selectedBulan' => (int) $request->bulan,
                        'selectedTahun' => (int) $request->tahun,
                        'jenisLaporanList' => $jenisLaporanList
                    ]);

            } else {
                return redirect()->route('laporan.bukti')
                    ->withInput()
                    ->with('error', 'Nomor Izin tidak sesuai dengan data industri yang dipilih.');
            }
        }

        return view('laporan.public.search', compact(
            'industries',
            'months',
            'years',
            'verified',
            'selectedIndustri',
            'selectedBulan',
            'selectedTahun',
            'jenisLaporanList',
            'error'
        ));
    }

    /**
     * View the Tanda Terima as HTML page (opens in new tab).
     * User can print it with Ctrl+P.
     */
    public function viewReceipt(Request $request)
    {
        $request->validate([
            'industri_id' => 'required|exists:industries,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2099',
        ]);

        $industri = \App\Models\Industri::findOrFail($request->industri_id);

        // Get all reports for this industry in the specified month, ordered by latest
        $laporans = Laporan::where('industri_id', $industri->id)
            ->whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($laporans->isEmpty()) {
            abort(404, 'Tidak ada laporan untuk periode ini.');
        }

        // Get unique report types
        $jenisLaporanList = $laporans->pluck('jenis_laporan')->unique()->values()->toArray();

        // Use the latest laporan ID (8 digits with leading zeros)
        $latestLaporan = $laporans->first();
        $nomorBukti = str_pad($latestLaporan->id, 8, '0', STR_PAD_LEFT);

        // Get month name
        $bulanNama = \Carbon\Carbon::createFromDate(null, (int) $request->bulan, 1)->translatedFormat('F');

        // Get active Pejabat
        $pejabat = \App\Models\Pejabat::where('is_active', true)->first();

        // Prepare data for view
        $data = [
            'industri' => $industri,
            'receiptId' => $nomorBukti,
            'bulan_nama' => $bulanNama,
            'tahun' => $request->tahun,
            'jenisLaporanList' => $jenisLaporanList,
            'tanggalTerakhir' => $latestLaporan->created_at->translatedFormat('d F Y'),
            'pejabat' => $pejabat,
        ];

        return view('laporan.pdf.bukti_tanda_terima', $data);
    }
}
