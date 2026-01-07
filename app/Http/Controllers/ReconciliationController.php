<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\ReconciliationDetail;
use App\Models\ReconciliationFact;
use App\Models\MappingAlias;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReconciliationController extends Controller
{
    public function index()
    {
        $reconciliations = Reconciliation::latest()->get();
        return view('reconciliations.index', compact('reconciliations'));
    }

    public function create()
    {
        return view('reconciliations.create');
    }

    public function show(Reconciliation $reconciliation)
    {
        // Grouping Total Kuantitas PER SATUAN
        $totalPerSatuan = ReconciliationDetail::where('reconciliation_id', $reconciliation->id)
            ->select('satuan', DB::raw('SUM(volume) as total_volume'))
            ->groupBy('satuan')
            ->orderByDesc('total_volume')
            ->get();

        // Group by Jenis HH
        $statsJenis = ReconciliationDetail::where('reconciliation_id', $reconciliation->id)
            ->select('jenis_sdh as label', 'satuan', DB::raw('SUM(volume) as total_volume'), DB::raw('SUM(lhp_nilai) as total_nilai'), DB::raw('COUNT(*) as count'))
            ->groupBy('jenis_sdh', 'satuan')
            ->orderByDesc('total_volume')
            ->get();

        // Group by Wilayah
        $statsWilayah = ReconciliationDetail::where('reconciliation_id', $reconciliation->id)
            ->select('wilayah as label', DB::raw('SUM(volume) as total_volume'), DB::raw('SUM(lhp_nilai) as total_nilai'), DB::raw('COUNT(*) as count'))
            ->groupBy('wilayah')
            ->orderByDesc('total_volume')
            ->get();

        // Group by Bank
        $statsBank = ReconciliationDetail::where('reconciliation_id', $reconciliation->id)
            ->whereNotNull('setor_bank')
            ->where('setor_bank', '!=', '')
            ->select('setor_bank as label', DB::raw('SUM(setor_nilai) as total_nilai'), DB::raw('COUNT(*) as count'))
            ->groupBy('setor_bank')
            ->orderByDesc('total_nilai')
            ->get();

        $details = $reconciliation->details()->orderBy('id')->paginate(50);

        return view('reconciliations.show', compact('reconciliation', 'totalPerSatuan', 'statsJenis', 'statsWilayah', 'statsBank', 'details'));
    }

    public function destroy(Reconciliation $reconciliation)
    {
        $reconciliation->delete();
        return back()->with('success', 'Data dihapus');
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2099',
            'quarter' => 'required|integer|min:1|max:4',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            
            // Ambil semua data (format kolom A, B, C...)
            $rows = $sheet->toArray(null, true, true, true);

            $recon = Reconciliation::create([
                'year' => $request->year,
                'quarter' => $request->quarter,
                'original_filename' => $file->getClientOriginalName(),
            ]);

            $activeWilayah = null;
            $activeBulan = null;
            $count = 0;

            foreach ($rows as $rowIndex => $row) {
                // 1. Cek Baris Kosong Total
                if (trim($row['A'].$row['B'].$row['F']) === '') {
                    continue; 
                }

                $colA = trim($row['A'] ?? ''); // No
                $colB = trim($row['B'] ?? ''); // Uraian
                
                // MAPPING PASTI (HARDCODED) SESUAI REQUEST ANDA
                // F=Jenis, G=Vol, H=Satuan, I=Nilai LHP
                $colJenis = trim($row['F'] ?? ''); 
                $colVol   = trim($row['G'] ?? '');
                $colSat   = trim($row['H'] ?? ''); 
                $colNilai = trim($row['I'] ?? '');
                
                // Gabungkan teks A dan B untuk deteksi Konteks
                $rowText = strtoupper($colA . ' ' . $colB);

                // A. DETEKSI WILAYAH
                if (preg_match('/(KABUPATEN|KOTA)\s+[A-Z\s]+/', $rowText, $m)) {
                    $activeWilayah = trim($m[0]);
                    continue;
                }

                // B. DETEKSI BULAN
                if ($bulan = $this->detectBulan($colB)) {
                    $activeBulan = $bulan;
                }

                // C. FILTER BARIS NON-DATA (SAMPAH)
                if ($colJenis === '' || preg_match('/(JENIS|URAIAN|NO|VOLUME|HH|REKAP|KETERANGAN)/i', $colJenis)) {
                    continue;
                }
                // Skip Baris Jumlah/Total di kolom B
                if (preg_match('/(JUMLAH|TOTAL|REKAP)/i', $colB)) {
                    continue;
                }
                // Skip baris nomor kolom (1, 2, 3...)
                if (is_numeric(str_replace(['.',','], '', $colJenis)) && (float)$colJenis < 50) {
                    continue;
                }

                // D. PROSES DATA
                $volume = $this->parseVolume($colVol);
                $rupiah = $this->parseRupiah($colNilai);
                
                // LOGIC PENTING: Simpan jika Volume > 0 ATAU Rupiah > 0
                // (Menangani kasus Jasling/Wisata yang volumenya 0 tapi ada uangnya)
                if ($volume <= 0 && $rupiah <= 0) {
                    continue;
                }

                // E. SIMPAN
                $detail = ReconciliationDetail::create([
                    'reconciliation_id' => $recon->id,
                    'wilayah'           => $activeWilayah ?? 'UNKNOWN',
                    'no_urut'           => $colA,
                    'jenis_sdh'         => $colJenis,
                    'volume'            => $volume,
                    'satuan'            => $colSat ?: '-', // Ambil dari kolom H, default '-'
                    
                    'lhp_no'            => trim($row['D'] ?? ''), // Kolom D
                    'lhp_tanggal'       => $this->parseDate($row['E'] ?? ''), // Kolom E
                    'lhp_nilai'         => $rupiah, // Kolom I (Parsed)
                    
                    'billing_no'        => trim($row['J'] ?? ''), // Geser ke J
                    'billing_tanggal'   => $this->parseDate($row['K'] ?? ''), // Geser ke K
                    'billing_nilai'     => $this->parseRupiah($row['L'] ?? ''), // Geser ke L
                    
                    'setor_tanggal'     => $this->parseDate($row['M'] ?? ''), // Geser ke M
                    'setor_bank'        => trim($row['N'] ?? ''), // Geser ke N
                    'setor_ntpn'        => trim($row['O'] ?? ''), // Geser ke O
                    'setor_ntb'         => trim($row['P'] ?? ''), // Geser ke P
                    'setor_nilai'       => $this->parseRupiah($row['Q'] ?? ''), // Geser ke Q
                    
                    'raw_data'          => json_encode($row),
                ]);

                // Simpan Fact (Opsional)
                ReconciliationFact::create([
                    'reconciliation_detail_id' => $detail->id,
                    'wilayah_id'   => $this->mapAlias('wilayah', $activeWilayah),
                    'komoditas_id' => $this->mapAlias('komoditas', $colJenis),
                    'volume'       => $volume,
                    'bulan'        => $activeBulan,
                ]);

                $count++;
            }

            if ($count === 0) {
                throw new \Exception('Tidak ada data valid terbaca. Pastikan format kolom: [F:Jenis] [G:Volume] [H:Satuan].');
            }

            DB::commit();

            return redirect()
                ->route('reconciliations.show', $recon->id)
                ->with('success', "Berhasil memproses {$count} baris data.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['file' => $e->getMessage()]);
        }
    }

    // --- HELPER FUNCTIONS ---

    private function detectBulan(string $text): ?string
    {
        $text = strtoupper($text);
        foreach ([
            'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
            'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
        ] as $b) {
            if (str_contains($text, $b)) {
                return ucfirst(strtolower($b));
            }
        }
        return null;
    }

    private function parseVolume($val): float
    {
        if ($val === '') return 0;
        if (strpos($val, ',') !== false && strpos($val, '.') !== false) {
             $clean = str_replace('.', '', $val);
             $clean = str_replace(',', '.', $clean);
        } elseif (strpos($val, ',') !== false) {
             $clean = str_replace(',', '.', $val);
        } else {
             $clean = $val;
        }
        return (float) preg_replace('/[^0-9\.-]/', '', $clean);
    }

    private function parseRupiah($val): float
    {
        if ($val === '') return 0;
        $clean = preg_replace('/[^0-9,]/', '', $val);
        return (float) str_replace(',', '', $clean);
    }

    private function parseDate($val): ?string
    {
        if (empty($val) || $val == '-' || $val == '0') return null;
        try {
            if (is_numeric($val)) {
                return Date::excelToDateTimeObject($val)->format('Y-m-d');
            }
            return Carbon::parse($val)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function mapAlias(string $type, ?string $value): ?int
    {
        if (!$value) return null;
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\s]/', '', $value);

        $alias = MappingAlias::where('type', $type)
            ->whereRaw('? LIKE CONCAT("%", alias, "%")', [$value])
            ->first();

        return $alias?->master_id;
    }
}