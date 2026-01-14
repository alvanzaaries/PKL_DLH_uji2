<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\ReconciliationDetail;
use App\Models\ReconciliationFact;
use App\Models\ReconciliationFile;
use App\Models\ReconciliationSummaryOverride;
use App\Models\MappingAlias;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReconciliationController extends Controller
{
    // =========================================================================
    // FITUR UTAMA
    // =========================================================================

    public function downloadFile(Reconciliation $reconciliation)
    {
        $file = ReconciliationFile::where('reconciliation_id', $reconciliation->id)->latest()->first();
        if (!$file) abort(404, 'File tidak ditemukan');

        return response($file->content)
            ->header('Content-Type', $file->mime_type ?: 'application/octet-stream')
            ->header('Content-Length', (string) $file->size)
            ->header('Content-Disposition', 'attachment; filename="' . $file->original_filename . '"');
    }

    public function index()
    {
        $reconciliations = Reconciliation::with('uploader')->latest()->get();
        return view('admin.reconciliations.index', compact('reconciliations'));
    }

    public function create()
    {
        $kphOptions = Reconciliation::query()
            ->select('kph')
            ->whereNotNull('kph')
            ->where('kph', '!=', '')
            ->distinct()
            ->orderBy('kph')
            ->pluck('kph');

        return view('admin.reconciliations.create', compact('kphOptions'));
    }

    public function show(Request $request, Reconciliation $reconciliation)
    {
        $query = ReconciliationDetail::where('reconciliation_id', $reconciliation->id);

        if ($search = trim((string)$request->input('search'))) {
            $query->where(function($q) use ($search) {
                $q->orWhere('wilayah', 'like', "%$search%")
                  ->orWhere('lhp_no', 'like', "%$search%")
                  ->orWhere('jenis_sdh', 'like', "%$search%")
                  ->orWhere('billing_no', 'like', "%$search%")
                  ->orWhere('setor_ntpn', 'like', "%$search%");
            });
        }

        $sort = $request->input('sort', 'id');
        $dir = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $allowed = ['no_urut','wilayah','lhp_no','lhp_tanggal','jenis_sdh','volume','satuan','lhp_nilai','billing_no','billing_tanggal','billing_nilai','setor_tanggal','setor_bank','setor_ntpn','setor_ntb','setor_nilai'];
        
        if (in_array($sort, $allowed)) {
            $query->orderBy($sort, $dir);
        } else {
            $query->orderBy('id', 'asc');
        }

        $details = $query->paginate(50);

        // Statistik
        $totalPerSatuan = ReconciliationDetail::where('reconciliation_id', $reconciliation->id)
            ->select('satuan', DB::raw('SUM(volume) as total_volume'))
            ->groupBy('satuan')->orderByDesc('total_volume')->get();

        $statsJenis = ReconciliationDetail::where('reconciliation_id', $reconciliation->id)
            ->select('jenis_sdh as label', 'satuan', DB::raw('SUM(volume) as total_volume'), DB::raw('SUM(lhp_nilai) as total_nilai'), DB::raw('COUNT(*) as count'))
            ->groupBy('jenis_sdh', 'satuan')->orderByDesc('total_volume')->get();

        $statsWilayah = ReconciliationDetail::where('reconciliation_id', $reconciliation->id)
            ->select('wilayah as label', DB::raw('SUM(volume) as total_volume'), DB::raw('SUM(lhp_nilai) as total_nilai'), DB::raw('COUNT(*) as count'))
            ->groupBy('wilayah')->orderByDesc('total_volume')->get();

        $statsBank = ReconciliationDetail::where('reconciliation_id', $reconciliation->id)
            ->whereNotNull('setor_bank')->where('setor_bank', '!=', '')
            ->select('setor_bank as label', DB::raw('SUM(setor_nilai) as total_nilai'), DB::raw('COUNT(*) as count'))
            ->groupBy('setor_bank')->orderByDesc('total_nilai')->get();

        // Load Overrides
        $overrides = ReconciliationSummaryOverride::where('reconciliation_id', $reconciliation->id)->get()
            ->keyBy(fn($o) => strtolower(($o->metric ?? '') . '|' . ($o->satuan ?? '')));

        $totalPerSatuan = $totalPerSatuan->map(function ($row) use ($overrides) {
            $key = strtolower('total_volume|' . ($row->satuan ?? ''));
            $o = $overrides->get($key);
            $row->total_volume_final = $o ? (float) $o->value : (float) $row->total_volume;
            $row->is_overridden = (bool) $o;
            return $row;
        });

        $baseTotalNilaiLhp = (float) ReconciliationDetail::where('reconciliation_id', $reconciliation->id)->sum('lhp_nilai');
        $nilaiOverride = $overrides->get(strtolower('total_nilai_lhp|'));
        $totalNilaiLhpFinal = $nilaiOverride ? (float) $nilaiOverride->value : $baseTotalNilaiLhp;
        $baseTotalNilaiSetor = (float) ReconciliationDetail::where('reconciliation_id', $reconciliation->id)->sum('setor_nilai');

        return view('admin.reconciliations.show', compact('reconciliation', 'details', 'totalPerSatuan', 'statsJenis', 'statsWilayah', 'statsBank', 'totalNilaiLhpFinal', 'baseTotalNilaiLhp', 'baseTotalNilaiSetor'));
    }

    public function destroy(Reconciliation $reconciliation)
    {
        $reconciliation->delete();
        return back()->with('success', 'Data dihapus');
    }

    // =========================================================================
    // STORE: FIXED FORMAT PARSER
    // =========================================================================
    public function store(Request $request)
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);

        $request->validate([
            'year' => 'required|integer|min:2000|max:2099',
            'quarter' => 'required|integer|min:1|max:4',
            'kph' => 'required|string|max:255',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');

        $recon = Reconciliation::create([
            'year' => $request->year,
            'quarter' => $request->quarter,
            'kph' => trim((string) $request->input('kph')),
            'original_filename' => $file->getClientOriginalName(),
            'user_id' => $request->user()?->id,
        ]);

        ReconciliationFile::create([
            'reconciliation_id' => $recon->id,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'content' => file_get_contents($file->getPathname()),
        ]);

        $user = $request->user();
        $isUserRole = $user && (($user->role ?? 'user') === 'user');

        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            
            // Loop semua sheet untuk mengakomodir jika ada multiple sheet wilayah
            $count = 0;
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                // Abaikan sheet rekap
                if (str_contains(strtoupper($sheet->getTitle()), 'REKAP')) continue;

                $rows = $sheet->toArray(null, true, true, true);
                $count += $this->parseFixedFormat($rows, $recon, $sheet->getTitle());
            }

            if ($count === 0) {
                throw new \Exception('Tidak ada data yang terbaca. Pastikan Kolom E = Jenis HH dan G = Satuan.');
            }

            DB::commit();

            return $isUserRole
                ? redirect()->route('user.upload')->with('success', 'Upload berhasil.')
                : redirect()->route('reconciliations.show', $recon->id)->with('success', "Sukses! Memproses {$count} baris data.");

        } catch (\Exception $e) {
            DB::rollBack();
            $msg = 'Gagal memproses: ' . $e->getMessage();
            return $isUserRole
                ? redirect()->route('user.upload')->withErrors(['file' => $msg])
                : redirect()->route('reconciliations.show', $recon->id)->withErrors(['file' => $msg]);
        }
    }

    // =========================================================================
    // PARSER LOGIC
    // =========================================================================
    private function parseFixedFormat(array $rows, Reconciliation $recon, string $sheetName): int
    {
        // Start Row Data (Asumsi Header 7-9, Data mulai 10)
        $startIndex = 10;
        
        $activeWilayah = null;
        $activeBulan = null;
        $count = 0;

        // Backup wilayah dari nama sheet
        if (!preg_match('/SHEET/i', $sheetName)) {
            $activeWilayah = strtoupper($sheetName);
        }

        foreach ($rows as $rowIndex => $row) {
            // Cek Header di baris-baris awal untuk update Wilayah
            if ($rowIndex < $startIndex) {
                $rowText = strtoupper(implode(' ', $row));
                if (preg_match('/^\s*?(KABUPATEN|KOTA)\s+[A-Z\s]+/', $rowText, $m)) {
                    $activeWilayah = trim($m[0]);
                }
                continue;
            }

            // Skip baris kosong
            if (trim(($row['A']??'').($row['E']??'').($row['F']??'')) === '') continue;

            $colA = trim($row['A'] ?? ''); // No
            $colB = trim($row['B'] ?? ''); // Uraian

            // Deteksi Bulan di kolom B
            if ($bulan = $this->detectBulan($colB)) {
                $activeBulan = $bulan;
            }

            // --- MAPPING KOLOM FIXED (NEW REQUEST) ---
            // C = No LHP
            // D = Tgl LHP
            // E = JENIS HH
            // F = VOLUME
            // G = SATUAN
            // H = NILAI LHP (RUPIAH)
            
            $colJenis = trim($row['E'] ?? ''); 
            
            // Skip Header/Junk
            if ($colJenis === '' || preg_match('/(JENIS|URAIAN|NO|VOLUME|HH|REKAP|JUMLAH|TOTAL)/i', $colJenis)) continue;
            if ($this->isJunkRow($colJenis) || $this->isJunkRow($colB)) continue;

            $colVol   = trim($row['F'] ?? '');
            $colSat   = trim($row['G'] ?? ''); // Satuan diambil dari G
            $colRpLHP = trim($row['H'] ?? '');

            $volume = $this->parseVolume($colVol);
            $rupiah = $this->parseRupiah($colRpLHP);

            if ($volume <= 0 && $rupiah <= 0) continue;

            // Bersihkan Satuan (Default "-" jika kosong)
            if ($colSat === '') $colSat = '-';

            // --- MAPPING KOLOM BILLING & SETOR (GESER KIRI) ---
            // I = No Billing
            // J = Tgl Billing
            // K = Nilai Billing
            // L = Tgl Setor
            // M = Bank
            // N = NTPN
            // O = NTB
            // P = Nilai Setor

            $billNo  = trim($row['I'] ?? '');
            $billTgl = $row['J'] ?? '';
            $billRp  = $row['K'] ?? '';
            
            $setorTgl  = $row['L'] ?? '';
            $setorBank = trim($row['M'] ?? '');
            $setorNtpn = trim($row['N'] ?? '');
            $setorNtb  = trim($row['O'] ?? '');
            $setorRp   = $row['P'] ?? '';

            // LHP Info (C=Nomor, D=Tanggal)
            $lhpNo  = trim($row['C'] ?? '');
            $lhpTgl = $row['D'] ?? '';

            $this->saveDetail($recon, $activeWilayah, $activeBulan, $colA, $colJenis, $volume, $colSat, $rupiah, $row, 
                $lhpNo, $lhpTgl,
                $billNo, $billTgl, $billRp,
                $setorTgl, $setorBank, $setorNtpn, $setorNtb, $setorRp
            );
            $count++;
        }

        return $count;
    }

    // =========================================================================
    // HELPER FUNCTIONS
    // =========================================================================
    
    private function saveDetail($recon, $wilayah, $bulan, $no, $jenis, $vol, $sat, $lhpNilai, $rawData, 
                                $lhpNo, $lhpTgl, $billNo, $billTgl, $billNilai, 
                                $setorTgl, $setorBank, $setorNtpn, $setorNtb, $setorNilai)
    {
        $detail = ReconciliationDetail::create([
            'reconciliation_id' => $recon->id,
            'wilayah'           => $wilayah ?? 'UNKNOWN',
            'no_urut'           => $no,
            'jenis_sdh'         => $this->cleanJenisHH($jenis),
            'volume'            => $vol,
            'satuan'            => $sat, // Simpan sesuai isi cell G
            'lhp_no'            => $lhpNo,
            'lhp_tanggal'       => $this->parseDate($lhpTgl),
            'lhp_nilai'         => $lhpNilai,
            'billing_no'        => $billNo,
            'billing_tanggal'   => $this->parseDate($billTgl),
            'billing_nilai'     => $this->parseRupiah($billNilai),
            'setor_tanggal'     => $this->parseDate($setorTgl),
            'setor_bank'        => $setorBank,
            'setor_ntpn'        => $setorNtpn,
            'setor_ntb'         => $setorNtb,
            'setor_nilai'       => $this->parseRupiah($setorNilai),
            'raw_data'          => json_encode($rawData),
        ]);

        ReconciliationFact::create([
            'reconciliation_detail_id' => $detail->id,
            'wilayah_id'   => $this->mapAlias('wilayah', $wilayah),
            'komoditas_id' => $this->mapAlias('komoditas', $jenis),
            'volume'       => $vol,
            'bulan'        => $bulan,
        ]);
    }

    private function cleanJenisHH(string $jenis): string
    {
        $clean = strtoupper($jenis);
        $clean = preg_replace('/^[\d\w]+\.\s*/', '', $clean); 
        $clean = preg_replace('/\s+/', ' ', $clean); 
        return trim($clean);
    }

    private function isJunkRow(string $text): bool
    {
        $text = strtoupper($text);
        return preg_match('/(JUMLAH|TOTAL|REKAP|GRAND|SUB\s*TOTAL)/', $text);
    }

    private function detectBulan(string $text): ?string
    {
        $text = strtoupper($text);
        foreach (['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'] as $b) {
            if (str_contains($text, $b)) return ucfirst(strtolower($b));
        }
        return null;
    }

    private function parseVolume($val) {
        if ($val === '') return 0;
        if (strpos($val, ',') !== false && strpos($val, '.') !== false) {
             $c = str_replace('.', '', $val); $c = str_replace(',', '.', $c);
        } elseif (strpos($val, ',') !== false) { $c = str_replace(',', '.', $val);
        } else { $c = $val; }
        return (float) preg_replace('/[^0-9\.-]/', '', $c);
    }

    private function parseRupiah($val) {
        if ($val === '') return 0;
        $c = preg_replace('/[^0-9,]/', '', $val);
        return (float) str_replace(',', '', $c);
    }

    private function parseDate($val) {
        if (empty($val) || $val == '-' || $val == '0') return null;
        try { if (is_numeric($val)) return Date::excelToDateTimeObject($val)->format('Y-m-d');
        return Carbon::parse($val)->format('Y-m-d'); } catch (\Exception $e) { return null; }
    }

    private function mapAlias($t, $v) {
        if (!$v) return null; $v = strtolower(trim($v)); $v = preg_replace('/[^a-z0-9\s]/', '', $v);
        $a = MappingAlias::where('type', $t)->whereRaw('? LIKE CONCAT("%", alias, "%")', [$v])->first();
        return $a?->master_id;
    }

    // Fitur Raw Excel & Overrides tetap dipertahankan
    public function rawExcel(Reconciliation $reconciliation)
    {
        $file = ReconciliationFile::where('reconciliation_id', $reconciliation->id)->latest()->first();
        if (!$file) abort(404, 'File tidak ditemukan');

        $tmp = tempnam(sys_get_temp_dir(), 'recon_');
        $ext = pathinfo($file->original_filename, PATHINFO_EXTENSION);
        $tmpPath = $tmp . ($ext ? '.' . $ext : '');
        file_put_contents($tmpPath, $file->content);

        try {
            $spreadsheet = IOFactory::load($tmpPath);
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $html = ob_get_clean();
        } catch (\Exception $e) {
            @unlink($tmpPath);
            return back()->withErrors(['file' => 'Gagal merender file: ' . $e->getMessage()]);
        }
        @unlink($tmpPath);

        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $m)) $body = $m[1];
        else $body = $html;

        return view('admin.reconciliations.raw', ['reconciliation' => $reconciliation, 'rawHtml' => $body]);
    }

    public function updateSummaryOverrides(Request $request, Reconciliation $reconciliation)
    {
        $data = $request->validate([
            'total_nilai_lhp' => 'nullable|string',
            'total_volume' => 'nullable|array',
            'total_volume.*' => 'nullable|string',
        ]);

        foreach (($data['total_volume'] ?? []) as $satuan => $val) {
            $satuan = (string) $satuan;
            $raw = trim((string) $val);
            if ($raw === '') {
                ReconciliationSummaryOverride::where('reconciliation_id', $reconciliation->id)
                    ->where('metric', 'total_volume')->where('satuan', $satuan)->delete();
                continue;
            }
            ReconciliationSummaryOverride::updateOrCreate(
                ['reconciliation_id' => $reconciliation->id, 'metric' => 'total_volume', 'satuan' => $satuan],
                ['value' => $this->parseVolume($raw)]
            );
        }

        $nilaiRaw = trim((string) ($data['total_nilai_lhp'] ?? ''));
        if ($nilaiRaw === '') {
            ReconciliationSummaryOverride::where('reconciliation_id', $reconciliation->id)
                ->where('metric', 'total_nilai_lhp')->whereNull('satuan')->delete();
        } else {
            ReconciliationSummaryOverride::updateOrCreate(
                ['reconciliation_id' => $reconciliation->id, 'metric' => 'total_nilai_lhp', 'satuan' => null],
                ['value' => $this->parseRupiah($nilaiRaw)]
            );
        }
        return back()->with('success', 'Ringkasan total berhasil diperbarui.');
    }
}