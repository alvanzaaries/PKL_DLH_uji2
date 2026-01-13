<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\LaporanValidationService;
use App\Services\LaporanDataService;
use App\Http\Requests\UpdateLaporanRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
                'path_laporan' => '',
            ]);

            // Simpan detail berdasarkan jenis laporan menggunakan service
            $this->dataService->saveDetailData($laporan, $request->jenis_laporan, $previewData['rows']);

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

            $result = $this->dataService->storeMultipleLaporan($request, $this->validationService);

            DB::commit();

            if ($result['uploaded'] > 0) {
                return redirect()->route('industri.laporan', $request->industri_id)
                    ->with('success', $result['message']);
            } else {
                return redirect()->route('industri.laporan', $request->industri_id)
                    ->with('warning', $result['message']);
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
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $jenis = (string) $request->input('jenis', 'penerimaan_kayu_bulat');

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

        // Get detail data menggunakan service (sama seperti detailLaporan tapi tanpa filter tambahan)
        $detailData = $this->dataService->getDetailLaporan($bulan, $tahun, $jenis, []);

        return view('laporan.rekapLaporan', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jenis' => $jenis,
            'jenisLabel' => $jenisOptions[$jenis],
            'items' => $detailData['items'],
        ]);
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

        // Build filters array
        $filters = [];
        if ($request->filled('jenis_kayu')) $filters['jenis_kayu'] = $request->jenis_kayu;
        if ($request->filled('asal_kayu')) $filters['asal_kayu'] = $request->asal_kayu;
        if ($request->filled('jenis_olahan')) $filters['jenis_olahan'] = $request->jenis_olahan;
        if ($request->filled('tujuan_kirim')) $filters['tujuan_kirim'] = $request->tujuan_kirim;
        if ($request->filled('ekspor_impor')) $filters['ekspor_impor'] = $request->ekspor_impor;

        // Get detail data menggunakan service
        $detailData = $this->dataService->getDetailLaporan($bulan, $tahun, $jenis, $filters);

        return view('laporan.detailLaporan', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jenis' => $jenis,
            'jenisLabel' => $jenisOptions[$jenis],
            'items' => $detailData['items'],
            'filterOptions' => $detailData['filterOptions'],
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
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $jenisOptions = [
            'penerimaan_kayu_bulat' => 'Laporan Penerimaan Kayu Bulat',
            'penerimaan_kayu_olahan' => 'Laporan Penerimaan Kayu Olahan',
            'mutasi_kayu_bulat' => 'Laporan Mutasi Kayu Bulat (LMKB)',
            'mutasi_kayu_olahan' => 'Laporan Mutasi Kayu Olahan (LMKO)',
            'penjualan_kayu_olahan' => 'Laporan Penjualan Kayu Olahan',
        ];

        // Get data
        $detailData = $this->dataService->getDetailLaporan($bulan, $tahun, $jenis, []);
        $items = $detailData['items'];

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

        // Generate filename
        $filename = 'Rekap_' . str_replace(' ', '_', $jenisOptions[$jenis] ?? 'Laporan') . '_' . $namaBulan[$bulan] . '_' . $tahun . '.xlsx';

        // Output
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
