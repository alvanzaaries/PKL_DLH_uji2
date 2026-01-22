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

    public function __construct(LaporanValidationService $validationService, LaporanDataService $dataService)
    {
        $this->validationService = $validationService;
        $this->dataService = $dataService;
    }

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
            'file_excel' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        // Validasi unique: cek apakah sudah ada laporan untuk industri, bulan, tahun, dan jenis yang sama
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

            // Baca Excel dan validasi format sesuai jenis laporan menggunakan service
            $jenisLaporan = $request->jenis_laporan;
            $previewData = $this->validationService->readAndValidateExcel($filePath, $jenisLaporan);

            // Simpan data ke session untuk proses simpan nanti
            session([
                'preview_data' => $previewData,
                'preview_file_path' => $filePath,
                'preview_metadata' => [
                    'industri_id' => $request->industri_id,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'jenis_laporan' => $jenisLaporan,
                ],
                'redirect_after_save' => url()->previous(), // Simpan URL sebelumnya
                'is_fresh_upload' => true, // Flag untuk clear localStorage di view
            ]);

            // Redirect to GET preview which will read preview data from session and paginate
            return redirect()->route('laporan.preview.show');

        } catch (\Exception $e) {
            Log::error('Laporan preview failed', ['exception' => $e]);
            return back()
                ->withInput()
                ->with('error', 'Gagal memproses file. Silakan periksa format template.');
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

        // Build displayErrors: replace 'Baris N' with per-page number when the source row N is on this page
        $displayErrors = [];
        // map source_row => on-page index (1-based)
        $sourceToDisplay = [];
        foreach ($currentItems as $idx => $item) {
            if (is_array($item) && isset($item['source_row'])) {
                $sourceToDisplay[(int) $item['source_row']] = $idx + 1; // per-page index
            }
        }

        foreach ($previewData['errors'] ?? [] as $err) {
            if (preg_match('/Baris\s+(\d+):/i', $err, $m)) {
                $src = (int) $m[1];
                if (isset($sourceToDisplay[$src])) {
                    $disp = $sourceToDisplay[$src];
                    $displayErrors[] = preg_replace('/Baris\s+' . $src . ':/i', 'Baris ' . $disp . ':', $err);
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

            $validationResult = $this->validateEditedData($dataRows, $request->jenis_laporan, $rowNumberMap);

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
        if (!empty($previewData['errors']) && (!$request->has('edited_data') || empty($request->edited_data))) {
            return redirect()->route('laporan.preview.show')
                ->with('warning', 'Masih ada error validasi. Silakan perbaiki data di tabel lalu tekan "Perbaiki & Validasi Ulang".');
        }

        // Validasi unique lagi sebelum menyimpan (double check)
        $existingLaporan = Laporan::where('industri_id', $request->industri_id)
            ->where('jenis_laporan', $request->jenis_laporan)
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->first();

        if ($existingLaporan) {
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
                $validationResult = $this->validateEditedData($dataRows, $request->jenis_laporan, $rowNumberMap ?? []);

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
                'path_laporan' => '',
            ]);

            // Simpan detail berdasarkan jenis laporan menggunakan service (gunakan edited data jika ada)
            $this->dataService->saveDetailData($laporan, $request->jenis_laporan, $dataRows);

            DB::commit();

            // Hapus file temporary
            if ($filePath && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            // Ambil URL redirect dari session
            $redirectUrl = session('redirect_after_save');

            // Clear session data
            session()->forget(['preview_data', 'preview_file_path', 'preview_metadata', 'redirect_after_save']);

            // Redirect ke halaman sebelumnya atau default ke industri laporan
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Laporan berhasil disimpan!')->with('save_ok', true);
            } else {
                return redirect()->route('laporan.industri', ['industri' => $request->industri_id])
                    ->with('success', 'Laporan berhasil disimpan!')->with('save_ok', true);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save laporan', [
                'exception' => $e,
                'industri_id' => $request->industri_id ?? null,
                'jenis_laporan' => $request->jenis_laporan ?? null,
                'bulan' => $request->bulan ?? null,
                'tahun' => $request->tahun ?? null,
            ]);

            // Gunakan redirect URL dari session atau fallback ke laporan.industri
            $redirectUrl = session('redirect_after_save');
            $msg = 'Gagal menyimpan laporan. Silakan coba lagi atau hubungi administrator.';
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
     * Remove the specified resource from storage.
     */
    public function destroy(Laporan $laporan)
    {
        //
    }

    /**
     * Menampilkan halaman rekap laporan dengan filter bulan dan tahun
     */
    public function rekapLaporan(Request $request)
    {
        // Ambil filter dari request atau gunakan bulan dan tahun saat ini
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $jenis = (string) $request->input('jenis', 'penerimaan_kayu_bulat');
        $perPage = $request->input('per_page', 25); // Default 25 items per page
        $sortBy = $request->input('sort_by');
        $sortDirection = $request->input('sort_direction', 'asc');

        $jenisOptions = [
            'penerimaan_kayu_bulat' => 'Laporan Penerimaan Kayu Bulat',
            'penerimaan_kayu_olahan' => 'Laporan Penerimaan Kayu Olahan',
            'mutasi_kayu_bulat' => 'Laporan Mutasi Kayu Bulat (LMKB)',
            'mutasi_kayu_olahan' => 'Laporan Mutasi Kayu Olahan (LMKO)',
            'penjualan_kayu_olahan' => 'Laporan Penjualan Kayu Olahan',
        ];

        if (!array_key_exists($jenis, $jenisOptions)) {
            $jenis = 'penerimaan_kayu_bulat';
        }

        // Build filters array (allow filtering on the rekap page)
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

        // Get detail data menggunakan service dengan filter tambahan jika ada, pagination, dan sorting
        $detailData = $this->dataService->getDetailLaporan($bulan, $tahun, $jenis, $filters, $perPage, $sortBy, $sortDirection);

        // Determine earliest year from Laporan.tanggal to populate the year dropdown in the view.
        // Fallback to 2020 if there are no records or parsing fails.
        $firstDate = Laporan::orderBy('tanggal', 'asc')->value('tanggal');
        $earliestYear = 2020;
        if ($firstDate) {
            try {
                $earliestYear = (int) \Carbon\Carbon::parse($firstDate)->year;
            } catch (\Exception $e) {
                // keep default earliestYear
            }
        }

        return view('laporan.rekapLaporan', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jenis' => $jenis,
            'jenisLabel' => $jenisOptions[$jenis],
            'items' => $detailData['items'],
            'filterOptions' => $detailData['filterOptions'],
            'earliestYear' => $earliestYear,
            'perPage' => $perPage,
        ]);
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
     * Export rekap laporan ke Excel
     */
    public function exportRekapLaporan(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $jenis = (string) $request->input('jenis', 'penerimaan_kayu_bulat');

        $namaBulan = [
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

        $jenisOptions = [
            'penerimaan_kayu_bulat' => 'Laporan Penerimaan Kayu Bulat',
            'penerimaan_kayu_olahan' => 'Laporan Penerimaan Kayu Olahan',
            'mutasi_kayu_bulat' => 'Laporan Mutasi Kayu Bulat (LMKB)',
            'mutasi_kayu_olahan' => 'Laporan Mutasi Kayu Olahan (LMKO)',
            'penjualan_kayu_olahan' => 'Laporan Penjualan Kayu Olahan',
        ];

        // Build filters from request (so export respects applied detail filters)
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

        // Get data - use getDetailLaporanForExport to get ALL data without pagination
        $items = $this->dataService->getDetailLaporanForExport($bulan, $tahun, $jenis, $filters);

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(25);

        // Baris 2: Rekap Nama Laporan
        $sheet->setCellValue('A2', 'REKAP ' . strtoupper($jenisOptions[$jenis] ?? ''));
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 3: Bulan / Tahun
        $sheet->setCellValue('A3', ($namaBulan[$bulan] ?? $bulan) . ' / ' . $tahun);
        $sheet->mergeCells('A3:I3');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 5: Warning merah
        $sheet->setCellValue('A5', '* Jangan Ubah Struktur Kolom');
        $sheet->getStyle('A5')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));

        // Header kolom - Baris 6
        $headerRow = 6;
        $columnNumberRow = 7;

        switch ($jenis) {
            case 'penerimaan_kayu_bulat':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Nomor Dokumen');
                $sheet->setCellValue('D' . $headerRow, 'Tanggal');
                $sheet->setCellValue('E' . $headerRow, 'Asal Kayu (Kabupaten)');
                $sheet->setCellValue('F' . $headerRow, 'Jenis Kayu');
                $sheet->setCellValue('G' . $headerRow, 'Jumlah Batang');
                $sheet->setCellValue('H' . $headerRow, 'Volume (m³)');
                $sheet->setCellValue('I' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(25);
                break;

            case 'penerimaan_kayu_olahan':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Nomor Dokumen');
                $sheet->setCellValue('D' . $headerRow, 'Tanggal');
                $sheet->setCellValue('E' . $headerRow, 'Asal Kayu');
                $sheet->setCellValue('F' . $headerRow, 'Jenis Olahan');
                $sheet->setCellValue('G' . $headerRow, 'Jumlah Keping');
                $sheet->setCellValue('H' . $headerRow, 'Volume (m³)');
                $sheet->setCellValue('I' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(25);
                break;

            case 'mutasi_kayu_bulat':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Jenis Kayu');
                $sheet->setCellValue('D' . $headerRow, 'Persediaan Awal (m³)');
                $sheet->setCellValue('E' . $headerRow, 'Penambahan (m³)');
                $sheet->setCellValue('F' . $headerRow, 'Penggunaan (m³)');
                $sheet->setCellValue('G' . $headerRow, 'Persediaan Akhir (m³)');
                $sheet->setCellValue('H' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(25);
                break;

            case 'mutasi_kayu_olahan':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Jenis Olahan');
                $sheet->setCellValue('D' . $headerRow, 'Persediaan Awal (m³)');
                $sheet->setCellValue('E' . $headerRow, 'Penambahan (m³)');
                $sheet->setCellValue('F' . $headerRow, 'Penggunaan (m³)');
                $sheet->setCellValue('G' . $headerRow, 'Persediaan Akhir (m³)');
                $sheet->setCellValue('H' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(25);
                break;

            case 'penjualan_kayu_olahan':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Nomor Dokumen');
                $sheet->setCellValue('D' . $headerRow, 'Tanggal');
                $sheet->setCellValue('E' . $headerRow, 'Tujuan Kirim');
                $sheet->setCellValue('F' . $headerRow, 'Jenis Olahan');
                $sheet->setCellValue('G' . $headerRow, 'Jumlah Keping');
                $sheet->setCellValue('H' . $headerRow, 'Volume (m³)');
                $sheet->setCellValue('I' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(25);
                break;
        }

        // Style header kolom (Baris 6) - Background hitam, teks putih
        $headerRange = 'A' . $headerRow . ':' . $sheet->getHighestColumn() . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Baris 7: Angka kolom (1, 2, 3, dst) dengan background hijau muda
        $lastColumn = $sheet->getHighestColumn();
        $colCount = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);

        for ($col = 1; $col <= $colCount; $col++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($columnLetter . $columnNumberRow, $col);
        }

        // Style baris angka kolom - Background hijau muda
        $columnNumberRange = 'A' . $columnNumberRow . ':' . $lastColumn . $columnNumberRow;
        $sheet->getStyle($columnNumberRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A8D08D']], // Hijau muda
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Data - mulai dari baris 8
        $row = 8;
        $no = 1;

        foreach ($items as $item) {
            $perusahaan = $item->laporan->industri->nama ?? '-';

            switch ($jenis) {
                case 'penerimaan_kayu_bulat':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->nomor_dokumen);
                    $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($item->tanggal)));
                    $sheet->setCellValue('E' . $row, $item->asal_kayu);
                    $sheet->setCellValue('F' . $row, $item->jenis_kayu);
                    $sheet->setCellValue('G' . $row, $item->jumlah_batang);
                    $sheet->setCellValue('H' . $row, $item->volume);
                    $sheet->setCellValue('I' . $row, $item->keterangan ?? '-');
                    break;

                case 'penerimaan_kayu_olahan':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->nomor_dokumen);
                    $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($item->tanggal)));
                    $sheet->setCellValue('E' . $row, $item->asal_kayu);
                    $sheet->setCellValue('F' . $row, $item->jenis_olahan);
                    $sheet->setCellValue('G' . $row, $item->jumlah_keping);
                    $sheet->setCellValue('H' . $row, $item->volume);
                    $sheet->setCellValue('I' . $row, $item->keterangan ?? '-');
                    break;

                case 'mutasi_kayu_bulat':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->jenis_kayu);
                    $sheet->setCellValue('D' . $row, $item->persediaan_awal_volume);
                    $sheet->setCellValue('E' . $row, $item->penambahan_volume);
                    $sheet->setCellValue('F' . $row, $item->penggunaan_pengurangan_volume);
                    $sheet->setCellValue('G' . $row, $item->persediaan_akhir_volume);
                    $sheet->setCellValue('H' . $row, $item->keterangan ?? '-');
                    break;

                case 'mutasi_kayu_olahan':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->jenis_olahan);
                    $sheet->setCellValue('D' . $row, $item->persediaan_awal_volume);
                    $sheet->setCellValue('E' . $row, $item->penambahan_volume);
                    $sheet->setCellValue('F' . $row, $item->penggunaan_pengurangan_volume);
                    $sheet->setCellValue('G' . $row, $item->persediaan_akhir_volume);
                    $sheet->setCellValue('H' . $row, $item->keterangan ?? '-');
                    break;

                case 'penjualan_kayu_olahan':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->nomor_dokumen);
                    $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($item->tanggal)));
                    $sheet->setCellValue('E' . $row, $item->tujuan_kirim);
                    $sheet->setCellValue('F' . $row, $item->jenis_olahan);
                    $sheet->setCellValue('G' . $row, $item->jumlah_keping);
                    $sheet->setCellValue('H' . $row, $item->volume);
                    $sheet->setCellValue('I' . $row, $item->keterangan ?? '-');
                    break;
            }

            // Style data row
            $dataRange = 'A' . $row . ':' . $sheet->getHighestColumn() . $row;
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // Align numbers to right
            if (in_array($jenis, ['penerimaan_kayu_bulat', 'penerimaan_kayu_olahan', 'penjualan_kayu_olahan'])) {
                $sheet->getStyle('G' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            } else {
                $sheet->getStyle('D' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            $row++;
            $no++;
        }

        // Total row
        if ($items->count() > 0) {
            switch ($jenis) {
                case 'penerimaan_kayu_bulat':
                    $sheet->setCellValue('A' . $row, 'TOTAL');
                    $sheet->mergeCells('A' . $row . ':F' . $row);
                    $sheet->setCellValue('G' . $row, $items->sum('jumlah_batang'));
                    $sheet->setCellValue('H' . $row, $items->sum('volume'));
                    $totalRange = 'A' . $row . ':I' . $row;
                    break;

                case 'penerimaan_kayu_olahan':
                case 'penjualan_kayu_olahan':
                    $sheet->setCellValue('A' . $row, 'TOTAL');
                    $sheet->mergeCells('A' . $row . ':F' . $row);
                    $sheet->setCellValue('G' . $row, $items->sum('jumlah_keping'));
                    $sheet->setCellValue('H' . $row, $items->sum('volume'));
                    $totalRange = 'A' . $row . ':I' . $row;
                    break;

                case 'mutasi_kayu_bulat':
                case 'mutasi_kayu_olahan':
                    $sheet->setCellValue('A' . $row, 'TOTAL');
                    $sheet->mergeCells('A' . $row . ':C' . $row);
                    $sheet->setCellValue('D' . $row, $items->sum('persediaan_awal_volume'));
                    $sheet->setCellValue('E' . $row, $items->sum('penambahan_volume'));
                    $sheet->setCellValue('F' . $row, $items->sum('penggunaan_pengurangan_volume'));
                    $sheet->setCellValue('G' . $row, $items->sum('persediaan_akhir_volume'));
                    $totalRange = 'A' . $row . ':H' . $row;
                    break;
            }

            // Style total row
            $sheet->getStyle($totalRange)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // Align TOTAL text to right
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Generate filename (include applied filters if any)
        $base = 'Rekap_' . str_replace(' ', '_', $jenisOptions[$jenis] ?? 'Laporan') . '_' . $namaBulan[$bulan] . '_' . $tahun;
        $filterSuffix = '';
        if (!empty($filters)) {
            $parts = [];
            foreach ($filters as $k => $v) {
                if ($v === null || $v === '')
                    continue;
                // sanitize value for filename
                $safe = preg_replace('/[^A-Za-z0-9\-_.]/', '_', str_replace(' ', '_', (string) $v));
                $parts[] = $k . '-' . $safe;
            }
            if (!empty($parts)) {
                $filterSuffix = '_' . implode('_', $parts);
            }
        }

        $filename = $base . $filterSuffix . '.xlsx';

        // Output
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportDetailLaporan(Request $request, $industri, $id)
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

        $namaBulan = [
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
            return redirect()->route('laporan.industri', ['industri' => $industri])
                ->with('error', 'Jenis laporan tidak valid.');
        }

        // Build filters from request (so export respects applied detail filters)
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

        // Pastikan hanya data industri ini yang diambil
        $filters['industri_id'] = $laporan->industri_id;

        // Get data - use getDetailLaporanForExport to get ALL data without pagination
        $items = $this->dataService->getDetailLaporanForExport($bulan, $tahun, $jenis, $filters);

        // Get company name
        $companyName = $laporan->industri->nama ?? 'Perusahaan';

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(25);

        // Baris 1: Nama Perusahaan
        $sheet->setCellValue('A1', strtoupper($companyName));
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 2: Nama Laporan
        $sheet->setCellValue('A2', strtoupper($jenisOptions[$jenis] ?? ''));
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 3: Bulan / Tahun
        $sheet->setCellValue('A3', ($namaBulan[$bulan] ?? $bulan) . ' / ' . $tahun);
        $sheet->mergeCells('A3:I3');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 5: Warning merah
        $sheet->setCellValue('A5', '* Jangan Ubah Struktur Kolom');
        $sheet->getStyle('A5')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));

        // Header kolom - Baris 6
        $headerRow = 6;
        $columnNumberRow = 7;

        switch ($jenis) {
            case 'penerimaan_kayu_bulat':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Nomor Dokumen');
                $sheet->setCellValue('D' . $headerRow, 'Tanggal');
                $sheet->setCellValue('E' . $headerRow, 'Asal Kayu (Kabupaten)');
                $sheet->setCellValue('F' . $headerRow, 'Jenis Kayu');
                $sheet->setCellValue('G' . $headerRow, 'Jumlah Batang');
                $sheet->setCellValue('H' . $headerRow, 'Volume (m³)');
                $sheet->setCellValue('I' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(25);
                break;

            case 'penerimaan_kayu_olahan':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Nomor Dokumen');
                $sheet->setCellValue('D' . $headerRow, 'Tanggal');
                $sheet->setCellValue('E' . $headerRow, 'Asal Kayu');
                $sheet->setCellValue('F' . $headerRow, 'Jenis Olahan');
                $sheet->setCellValue('G' . $headerRow, 'Jumlah Keping');
                $sheet->setCellValue('H' . $headerRow, 'Volume (m³)');
                $sheet->setCellValue('I' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(25);
                break;

            case 'mutasi_kayu_bulat':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Jenis Kayu');
                $sheet->setCellValue('D' . $headerRow, 'Persediaan Awal (m³)');
                $sheet->setCellValue('E' . $headerRow, 'Penambahan (m³)');
                $sheet->setCellValue('F' . $headerRow, 'Penggunaan (m³)');
                $sheet->setCellValue('G' . $headerRow, 'Persediaan Akhir (m³)');
                $sheet->setCellValue('H' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(25);
                break;

            case 'mutasi_kayu_olahan':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Jenis Olahan');
                $sheet->setCellValue('D' . $headerRow, 'Persediaan Awal (m³)');
                $sheet->setCellValue('E' . $headerRow, 'Penambahan (m³)');
                $sheet->setCellValue('F' . $headerRow, 'Penggunaan (m³)');
                $sheet->setCellValue('G' . $headerRow, 'Persediaan Akhir (m³)');
                $sheet->setCellValue('H' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(25);
                break;

            case 'penjualan_kayu_olahan':
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Perusahaan');
                $sheet->setCellValue('C' . $headerRow, 'Nomor Dokumen');
                $sheet->setCellValue('D' . $headerRow, 'Tanggal');
                $sheet->setCellValue('E' . $headerRow, 'Tujuan Kirim');
                $sheet->setCellValue('F' . $headerRow, 'Jenis Olahan');
                $sheet->setCellValue('G' . $headerRow, 'Jumlah Keping');
                $sheet->setCellValue('H' . $headerRow, 'Volume (m³)');
                $sheet->setCellValue('I' . $headerRow, 'Keterangan');

                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(25);
                break;
        }

        // Style header kolom (Baris 6) - Background hitam, teks putih
        $headerRange = 'A' . $headerRow . ':' . $sheet->getHighestColumn() . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Baris 7: Angka kolom (1, 2, 3, dst) dengan background hijau muda
        $lastColumn = $sheet->getHighestColumn();
        $colCount = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);

        for ($col = 1; $col <= $colCount; $col++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($columnLetter . $columnNumberRow, $col);
        }

        // Style baris angka kolom - Background hijau muda
        $columnNumberRange = 'A' . $columnNumberRow . ':' . $lastColumn . $columnNumberRow;
        $sheet->getStyle($columnNumberRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A8D08D']], // Hijau muda
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Data - mulai dari baris 8
        $row = 8;
        $no = 1;

        foreach ($items as $item) {
            $perusahaan = $item->laporan->industri->nama ?? '-';

            switch ($jenis) {
                case 'penerimaan_kayu_bulat':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->nomor_dokumen);
                    $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($item->tanggal)));
                    $sheet->setCellValue('E' . $row, $item->asal_kayu);
                    $sheet->setCellValue('F' . $row, $item->jenis_kayu);
                    $sheet->setCellValue('G' . $row, $item->jumlah_batang);
                    $sheet->setCellValue('H' . $row, $item->volume);
                    $sheet->setCellValue('I' . $row, $item->keterangan ?? '-');
                    break;

                case 'penerimaan_kayu_olahan':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->nomor_dokumen);
                    $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($item->tanggal)));
                    $sheet->setCellValue('E' . $row, $item->asal_kayu);
                    $sheet->setCellValue('F' . $row, $item->jenis_olahan);
                    $sheet->setCellValue('G' . $row, $item->jumlah_keping);
                    $sheet->setCellValue('H' . $row, $item->volume);
                    $sheet->setCellValue('I' . $row, $item->keterangan ?? '-');
                    break;

                case 'mutasi_kayu_bulat':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->jenis_kayu);
                    $sheet->setCellValue('D' . $row, $item->persediaan_awal_volume);
                    $sheet->setCellValue('E' . $row, $item->penambahan_volume);
                    $sheet->setCellValue('F' . $row, $item->penggunaan_pengurangan_volume);
                    $sheet->setCellValue('G' . $row, $item->persediaan_akhir_volume);
                    $sheet->setCellValue('H' . $row, $item->keterangan ?? '-');
                    break;

                case 'mutasi_kayu_olahan':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->jenis_olahan);
                    $sheet->setCellValue('D' . $row, $item->persediaan_awal_volume);
                    $sheet->setCellValue('E' . $row, $item->penambahan_volume);
                    $sheet->setCellValue('F' . $row, $item->penggunaan_pengurangan_volume);
                    $sheet->setCellValue('G' . $row, $item->persediaan_akhir_volume);
                    $sheet->setCellValue('H' . $row, $item->keterangan ?? '-');
                    break;

                case 'penjualan_kayu_olahan':
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $perusahaan);
                    $sheet->setCellValue('C' . $row, $item->nomor_dokumen);
                    $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($item->tanggal)));
                    $sheet->setCellValue('E' . $row, $item->tujuan_kirim);
                    $sheet->setCellValue('F' . $row, $item->jenis_olahan);
                    $sheet->setCellValue('G' . $row, $item->jumlah_keping);
                    $sheet->setCellValue('H' . $row, $item->volume);
                    $sheet->setCellValue('I' . $row, $item->keterangan ?? '-');
                    break;
            }

            // Style data row
            $dataRange = 'A' . $row . ':' . $sheet->getHighestColumn() . $row;
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // Align numbers to right
            if (in_array($jenis, ['penerimaan_kayu_bulat', 'penerimaan_kayu_olahan', 'penjualan_kayu_olahan'])) {
                $sheet->getStyle('G' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            } else {
                $sheet->getStyle('D' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            $row++;
            $no++;
        }

        // Total row
        if ($items->count() > 0) {
            switch ($jenis) {
                case 'penerimaan_kayu_bulat':
                    $sheet->setCellValue('A' . $row, 'TOTAL');
                    $sheet->mergeCells('A' . $row . ':F' . $row);
                    $sheet->setCellValue('G' . $row, $items->sum('jumlah_batang'));
                    $sheet->setCellValue('H' . $row, $items->sum('volume'));
                    $totalRange = 'A' . $row . ':I' . $row;
                    break;

                case 'penerimaan_kayu_olahan':
                case 'penjualan_kayu_olahan':
                    $sheet->setCellValue('A' . $row, 'TOTAL');
                    $sheet->mergeCells('A' . $row . ':F' . $row);
                    $sheet->setCellValue('G' . $row, $items->sum('jumlah_keping'));
                    $sheet->setCellValue('H' . $row, $items->sum('volume'));
                    $totalRange = 'A' . $row . ':I' . $row;
                    break;

                case 'mutasi_kayu_bulat':
                case 'mutasi_kayu_olahan':
                    $sheet->setCellValue('A' . $row, 'TOTAL');
                    $sheet->mergeCells('A' . $row . ':C' . $row);
                    $sheet->setCellValue('D' . $row, $items->sum('persediaan_awal_volume'));
                    $sheet->setCellValue('E' . $row, $items->sum('penambahan_volume'));
                    $sheet->setCellValue('F' . $row, $items->sum('penggunaan_pengurangan_volume'));
                    $sheet->setCellValue('G' . $row, $items->sum('persediaan_akhir_volume'));
                    $totalRange = 'A' . $row . ':H' . $row;
                    break;
            }

            // Style total row
            $sheet->getStyle($totalRange)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // Align TOTAL text to right
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Generate filename (include company name and applied filters if any)
        $safeCompanyName = preg_replace('/[^A-Za-z0-9\-_.]/', '_', str_replace(' ', '_', $companyName));
        $base = 'Detail_' . $safeCompanyName . '_' . str_replace(' ', '_', $jenisOptions[$jenis] ?? 'Laporan') . '_' . $namaBulan[$bulan] . '_' . $tahun;
        $filterSuffix = '';
        if (!empty($filters)) {
            $parts = [];
            foreach ($filters as $k => $v) {
                if ($v === null || $v === '' || $k === 'industri_id')
                    continue;
                // sanitize value for filename
                $safe = preg_replace('/[^A-Za-z0-9\-_.]/', '_', str_replace(' ', '_', (string) $v));
                $parts[] = $k . '-' . $safe;
            }
            if (!empty($parts)) {
                $filterSuffix = '_' . implode('_', $parts);
            }
        }

        $filename = $base . $filterSuffix . '.xlsx';

        // Output
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Validasi data yang sudah diedit oleh user
     */
    private function validateEditedData($dataRows, $jenisLaporan, $rowNumberMap = [])
    {
        // Tentukan expected headers berdasarkan jenis laporan
        $headersMap = [
            'Laporan Penerimaan Kayu Bulat' => ['Nomor Dokumen', 'Tanggal', 'Asal Kayu', 'Jenis Kayu', 'Jumlah Batang', 'Volume', 'Keterangan'],
            'Laporan Mutasi Kayu Bulat (LMKB)' => ['Jenis Kayu', 'Persediaan Awal', 'Penambahan', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'],
            'Laporan Penerimaan Kayu Olahan' => ['Nomor Dokumen', 'Tanggal', 'Asal Kayu', 'Jenis Produk', 'Jumlah Keping', 'Volume', 'Keterangan'],
            'Laporan Mutasi Kayu Olahan (LMKO)' => ['Jenis Produk', 'Persediaan Awal', 'Penambahan', 'Penggunaan/Pengurangan', 'Persediaan Akhir', 'Keterangan'],
            'Laporan Penjualan Kayu Olahan' => ['Nomor Dokumen', 'Tanggal', 'Tujuan Kirim', 'Jenis Produk', 'Jumlah Keping', 'Volume', 'Keterangan'],
        ];

        $expectedHeaders = $headersMap[$jenisLaporan] ?? [];
        $errors = [];
        $validCount = 0;

        // Validasi setiap baris
        foreach ($dataRows as $index => $row) {
            // If caller provided a map from index to original source row number, use it for messages
            $rowNumber = $rowNumberMap[$index] ?? ($index + 1); // Row number untuk error message
            $rowErrors = [];

            // Skip baris kosong
            $isEmpty = true;
            foreach ($row as $cell) {
                if ($cell !== null && $cell !== '' && trim($cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty)
                continue;

            // Validasi berdasarkan jenis laporan
            switch ($jenisLaporan) {
                case 'Laporan Penerimaan Kayu Bulat':
                    $rowErrors = $this->validatePenerimaanKayuBulatRow($row, $rowNumber);
                    break;
                case 'Laporan Mutasi Kayu Bulat (LMKB)':
                    $rowErrors = $this->validateMutasiKayuBulatRow($row, $rowNumber);
                    break;
                case 'Laporan Penerimaan Kayu Olahan':
                    $rowErrors = $this->validatePenerimaanKayuOlahanRow($row, $rowNumber);
                    break;
                case 'Laporan Mutasi Kayu Olahan (LMKO)':
                    $rowErrors = $this->validateMutasiKayuOlahanRow($row, $rowNumber);
                    break;
                case 'Laporan Penjualan Kayu Olahan':
                    $rowErrors = $this->validatePenjualanKayuOlahanRow($row, $rowNumber);
                    break;
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validCount++;
            }
        }

        return [
            'headers' => $expectedHeaders,
            'rows' => $dataRows,
            'total' => count($dataRows),
            'valid' => $validCount,
            'errors' => $errors
        ];
    }

    // Helper methods untuk validasi setiap jenis laporan
    private function validatePenerimaanKayuBulatRow($row, $rowNumber)
    {
        $errors = [];

        if (trim((string) ($row[0] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
        }

        if (trim((string) ($row[1] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
        }

        if (trim((string) ($row[2] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Asal Kayu tidak boleh kosong";
        }

        if (trim((string) ($row[3] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Jenis Kayu tidak boleh kosong";
        }

        // Validasi Jumlah Batang
        if (trim((string) ($row[4] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Jumlah Batang tidak boleh kosong";
        } else {
            $jumlahBatang = $this->parseNumber($row[4]);
            if ($jumlahBatang === null) {
                $errors[] = "Baris {$rowNumber}: Jumlah Batang harus berupa angka";
            } elseif ($jumlahBatang < 0) {
                $errors[] = "Baris {$rowNumber}: Jumlah Batang tidak boleh negatif";
            }
        }

        // Validasi Volume
        if (trim((string) ($row[5] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
        } else {
            $volume = $this->parseNumber($row[5]);
            if ($volume === null) {
                $errors[] = "Baris {$rowNumber}: Volume harus berupa angka";
            } elseif ($volume < 0) {
                $errors[] = "Baris {$rowNumber}: Volume tidak boleh negatif";
            }
        }

        return $errors;
    }

    private function validateMutasiKayuBulatRow($row, $rowNumber)
    {
        $errors = [];

        if (trim((string) ($row[0] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Jenis Kayu tidak boleh kosong";
        }

        $persediaanAwal = $this->parseNumber($row[1] ?? '');
        $penambahan = $this->parseNumber($row[2] ?? '');
        $penggunaan = $this->parseNumber($row[3] ?? '');
        $persediaanAkhir = $this->parseNumber($row[4] ?? '');

        if ($persediaanAwal === null) {
            $errors[] = "Baris {$rowNumber}: Persediaan Awal harus berupa angka";
        } elseif ($persediaanAwal < 0) {
            $errors[] = "Baris {$rowNumber}: Persediaan Awal tidak boleh negatif";
        }

        if ($penambahan === null) {
            $errors[] = "Baris {$rowNumber}: Penambahan harus berupa angka";
        } elseif ($penambahan < 0) {
            $errors[] = "Baris {$rowNumber}: Penambahan tidak boleh negatif";
        }

        if ($penggunaan === null) {
            $errors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan harus berupa angka";
        } elseif ($penggunaan < 0) {
            $errors[] = "Baris {$rowNumber}: Penggunaan/Pengurangan tidak boleh negatif";
        }

        if ($persediaanAkhir === null) {
            $errors[] = "Baris {$rowNumber}: Persediaan Akhir harus berupa angka";
        } elseif ($persediaanAkhir < 0) {
            $errors[] = "Baris {$rowNumber}: Persediaan Akhir tidak boleh negatif";
        }

        // Validasi logika
        if ($persediaanAwal !== null && $penambahan !== null && $penggunaan !== null && $persediaanAkhir !== null) {
            $expectedAkhir = $persediaanAwal + $penambahan - $penggunaan;
            if (abs($expectedAkhir - $persediaanAkhir) > 0.01) {
                $errors[] = "Baris {$rowNumber}: Persediaan Akhir tidak sesuai (seharusnya {$expectedAkhir})";
            }
        }

        return $errors;
    }

    private function validatePenerimaanKayuOlahanRow($row, $rowNumber)
    {
        $errors = [];

        if (trim((string) ($row[0] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
        }

        if (trim((string) ($row[1] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
        }

        if (trim((string) ($row[2] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Asal Kayu tidak boleh kosong";
        }

        if (trim((string) ($row[3] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
        }

        if (trim((string) ($row[4] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh kosong";
        } else {
            $jumlahKeping = $this->parseNumber($row[4]);
            if ($jumlahKeping === null) {
                $errors[] = "Baris {$rowNumber}: Jumlah Keping harus berupa angka";
            } elseif ($jumlahKeping < 0) {
                $errors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh negatif";
            }
        }

        if (trim((string) ($row[5] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
        } else {
            $volume = $this->parseNumber($row[5]);
            if ($volume === null) {
                $errors[] = "Baris {$rowNumber}: Volume harus berupa angka";
            } elseif ($volume < 0) {
                $errors[] = "Baris {$rowNumber}: Volume tidak boleh negatif";
            }
        }

        return $errors;
    }

    private function validateMutasiKayuOlahanRow($row, $rowNumber)
    {
        return $this->validateMutasiKayuBulatRow($row, $rowNumber); // Same validation logic
    }

    private function validatePenjualanKayuOlahanRow($row, $rowNumber)
    {
        $errors = [];

        if (trim((string) ($row[0] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Nomor Dokumen tidak boleh kosong";
        }

        if (trim((string) ($row[1] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Tanggal tidak boleh kosong";
        }

        if (trim((string) ($row[2] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Tujuan Kirim tidak boleh kosong";
        }

        if (trim((string) ($row[3] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Jenis Produk tidak boleh kosong";
        }

        if (trim((string) ($row[4] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh kosong";
        } else {
            $jumlahKeping = $this->parseNumber($row[4]);
            if ($jumlahKeping === null) {
                $errors[] = "Baris {$rowNumber}: Jumlah Keping harus berupa angka";
            } elseif ($jumlahKeping < 0) {
                $errors[] = "Baris {$rowNumber}: Jumlah Keping tidak boleh negatif";
            }
        }

        if (trim((string) ($row[5] ?? '')) === '') {
            $errors[] = "Baris {$rowNumber}: Volume tidak boleh kosong";
        } else {
            $volume = $this->parseNumber($row[5]);
            if ($volume === null) {
                $errors[] = "Baris {$rowNumber}: Volume harus berupa angka";
            } elseif ($volume < 0) {
                $errors[] = "Baris {$rowNumber}: Volume tidak boleh negatif";
            }
        }

        return $errors;
    }

    /**
     * Parse angka dengan format Excel (koma untuk ribuan, titik untuk desimal)
     */
    private function parseNumber($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $stringValue = trim((string) $value);
        $normalized = str_replace(',', '', $stringValue);

        if (is_numeric($normalized)) {
            return (float) $normalized;
        }

        return null;
    }
}


