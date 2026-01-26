<?php

namespace App\Services;

use App\Models\Laporan;
use App\Models\laporan_mutasi_kayu_bulat;
use App\Models\laporan_mutasi_kayu_olahan;
use App\Models\laporan_penerimaan_kayu_bulat;
use App\Models\laporan_penjualan_kayu_olahan;
use App\Models\laporan_penerimaan_kayu_olahan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LaporanDataService
{
    /**
     * Simpan data detail berdasarkan jenis laporan
     * Data dalam format array of arrays sesuai urutan kolom
     */
    public function saveDetailData($laporan, $jenisLaporan, $rows)
    {
        switch ($jenisLaporan) {
            case 'Laporan Penerimaan Kayu Bulat':
                // Kolom: Nomor Dokumen | Tanggal | Asal Kayu | Jenis Kayu | Jumlah Batang | Volume | Keterangan
                foreach ($rows as $item) {
                    // Handle both wrapped format (['cells' => [...]]) and flat format ([...])
                    $row = isset($item['cells']) ? $item['cells'] : $item;

                    laporan_penerimaan_kayu_bulat::create([
                        'laporan_id' => $laporan->id,
                        'nomor_dokumen' => $row[0] ?? '',
                        'tanggal' => $this->parseDate($row[1]) ?? now(),
                        'asal_kayu' => $row[2] ?? '',
                        'jenis_kayu' => $row[3] ?? '',
                        'jumlah_batang' => $this->toFloat($row[4] ?? 0),
                        'volume' => $this->toFloat($row[5] ?? 0),
                        'keterangan' => $row[6] ?? '',
                    ]);
                }
                break;

            case 'Laporan Mutasi Kayu Bulat (LMKB)':
                // Kolom: Jenis Kayu | Persediaan Awal | Penambahan | Penggunaan | Persediaan Akhir | Keterangan
                foreach ($rows as $item) {
                    $row = isset($item['cells']) ? $item['cells'] : $item;

                    // Debug logging untuk melihat data sebelum insert
                    $dataToInsert = [
                        'laporan_id' => $laporan->id,
                        'jenis_kayu' => $row[0] ?? '',
                        'persediaan_awal_volume' => $this->toFloat($row[1] ?? 0),
                        'penambahan_volume' => $this->toFloat($row[2] ?? 0),
                        'penggunaan_pengurangan_volume' => $this->toFloat($row[3] ?? 0),
                        'persediaan_akhir_volume' => $this->toFloat($row[4] ?? 0),
                        'keterangan' => $row[5] ?? '',
                    ];

                    Log::debug('Attempting to insert laporan_mutasi_kayu_bulat', [
                        'data' => $dataToInsert,
                        'raw_row' => $row,
                    ]);

                    try {
                        laporan_mutasi_kayu_bulat::create($dataToInsert);
                    } catch (\Exception $e) {
                        Log::error('Failed to insert laporan_mutasi_kayu_bulat row', [
                            'exception' => $e->getMessage(),
                            'data' => $dataToInsert,
                            'raw_row' => $row,
                        ]);
                        throw $e; // Re-throw untuk ditangkap di controller
                    }
                }
                break;

            case 'Laporan Penerimaan Kayu Olahan':
                // Kolom: Nomor Dokumen | Tanggal | Asal Kayu | Jenis Olahan | Jumlah Keping | Volume | Keterangan
                foreach ($rows as $item) {
                    $row = isset($item['cells']) ? $item['cells'] : $item;

                    laporan_penerimaan_kayu_olahan::create([
                        'laporan_id' => $laporan->id,
                        'nomor_dokumen' => $row[0] ?? '',
                        'tanggal' => $this->parseDate($row[1]) ?? now(),
                        'asal_kayu' => $row[2] ?? '',
                        'jenis_olahan' => $row[3] ?? '',
                        'jumlah_keping' => $this->toFloat($row[4] ?? 0),
                        'volume' => $this->toFloat($row[5] ?? 0),
                        'keterangan' => $row[6] ?? '',
                    ]);
                }
                break;

            case 'Laporan Mutasi Kayu Olahan (LMKO)':
                // Kolom: Jenis Olahan | Persediaan Awal | Penambahan | Penggunaan | Persediaan Akhir | Keterangan
                foreach ($rows as $item) {
                    $row = isset($item['cells']) ? $item['cells'] : $item;

                    laporan_mutasi_kayu_olahan::create([
                        'laporan_id' => $laporan->id,
                        'jenis_olahan' => $row[0] ?? '',
                        'persediaan_awal_volume' => $this->toFloat($row[1] ?? 0),
                        'penambahan_volume' => $this->toFloat($row[2] ?? 0),
                        'penggunaan_pengurangan_volume' => $this->toFloat($row[3] ?? 0),
                        'persediaan_akhir_volume' => $this->toFloat($row[4] ?? 0),
                        'keterangan' => $row[5] ?? '',
                    ]);
                }
                break;

            case 'Laporan Penjualan Kayu Olahan':
                // Kolom: Nomor Dokumen | Tanggal | Tujuan Kirim | Jenis Olahan | Jumlah Keping | Volume | Keterangan
                foreach ($rows as $item) {
                    $row = isset($item['cells']) ? $item['cells'] : $item;

                    laporan_penjualan_kayu_olahan::create([
                        'laporan_id' => $laporan->id,
                        'nomor_dokumen' => $row[0] ?? '',
                        'tanggal' => $this->parseDate($row[1]) ?? now(),
                        'tujuan_kirim' => $row[2] ?? '',
                        'jenis_olahan' => $row[3] ?? '',
                        'jumlah_keping' => $this->toFloat($row[4] ?? 0),
                        'volume' => $this->toFloat($row[5] ?? 0),
                        'keterangan' => $row[6] ?? '',
                    ]);
                }
                break;
        }
    }

    /**
     * Normalisasi nilai numeric yang mungkin mengandung pemisah ribuan atau koma desimal.
     * Format: Titik (.) untuk desimal, Koma (,) untuk ribuan
     * Contoh valid: 1,234.56 atau 1234.56 atau 2.27 atau 2,000
     */
    private function toFloat($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $s = trim((string) $value);

        // Format yang diterima:
        // - Koma (,) hanya sebagai pemisah ribuan
        // - Titik (.) hanya sebagai pemisah desimal
        // - Format valid: 1,234.56 atau 1234.56 atau 1,234 atau 1234

        // Jika ada koma DAN titik, validasi posisi:
        // Titik harus di belakang koma (format US: 1,234.56)
        if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
            $lastComma = strrpos($s, ',');
            $lastDot = strrpos($s, '.');

            if ($lastDot > $lastComma) {
                // Format valid: 1,234.56 -> Hapus koma (ribuan)
                $s = str_replace(',', '', $s);
            } else {
                // Format tidak valid: 1.234,56 (format Indonesia tidak diterima)
                return 0.0;
            }
        }
        // Jika HANYA ada Koma (1,234)
        elseif (strpos($s, ',') !== false) {
            // Koma sebagai ribuan -> hapus koma
            $s = str_replace(',', '', $s);
        }
        // Jika HANYA ada Titik (1.5 atau 1.234)
        // Titik dianggap desimal (format valid)

        // Bersihkan karakter non-numeric lain
        $s = preg_replace('/[^0-9\.\-]/', '', $s);

        return is_numeric($s) ? (float) $s : 0.0;
    }


    /**
     * Generate rekap data untuk periode tertentu
     */
    public function generateRekap($bulan, $tahun)
    {
        // Query untuk mendapatkan laporan berdasarkan bulan dan tahun
        $laporans = Laporan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $laporanIds = $laporans->pluck('id');

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

        return [
            'rekap' => $rekap,
            'insights' => $insights,
            'detailData' => $detailData
        ];
    }

    /**
     * Get detail laporan data dengan filter, pagination, dan sorting
     */
    public function getDetailLaporan($bulan, $tahun, $jenis, $filters = [], $perPage = 25, $sortBy = null, $sortDirection = 'asc')
    {
        // Map jenis slug to jenis_laporan label
        $jenisLaporanMap = [
            'penerimaan_kayu_bulat' => 'Laporan Penerimaan Kayu Bulat',
            'penerimaan_kayu_olahan' => 'Laporan Penerimaan Kayu Olahan',
            'mutasi_kayu_bulat' => 'Laporan Mutasi Kayu Bulat (LMKB)',
            'mutasi_kayu_olahan' => 'Laporan Mutasi Kayu Olahan (LMKO)',
            'penjualan_kayu_olahan' => 'Laporan Penjualan Kayu Olahan',
        ];

        $jenisLaporanLabel = $jenisLaporanMap[$jenis] ?? null;

        $laporanQuery = Laporan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        // Filter by jenis_laporan to get only the specific report type
        if ($jenisLaporanLabel) {
            $laporanQuery->where('jenis_laporan', $jenisLaporanLabel);
        }

        // Jika ada filter industri_id, batasi laporan hanya untuk industri tersebut
        if (isset($filters['industri_id']) && $filters['industri_id']) {
            $laporanQuery->where('industri_id', $filters['industri_id']);
        }

        $laporanIds = $laporanQuery->pluck('id');

        $items = collect();
        $filterOptions = [];

        switch ($jenis) {
            case 'penerimaan_kayu_bulat':
                $query = laporan_penerimaan_kayu_bulat::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['jenis_kayu'])) {
                    $query->where('jenis_kayu', $filters['jenis_kayu']);
                }
                if (isset($filters['asal_kayu'])) {
                    $query->where('asal_kayu', $filters['asal_kayu']);
                }

                // Apply sorting
                $sortColumn = $sortBy ?? 'tanggal';
                $validSortColumns = ['tanggal', 'nomor_dokumen', 'asal_kayu', 'jenis_kayu', 'jumlah_batang', 'volume'];
                if (!in_array($sortColumn, $validSortColumns)) {
                    $sortColumn = 'tanggal';
                }
                $query->orderBy($sortColumn, $sortDirection);

                $items = ($perPage === 'all') ? $query->get() : $query->paginate($perPage)->withQueryString();

                $filterOptions['jenis_kayu'] = laporan_penerimaan_kayu_bulat::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_kayu')->sort()->values();
                $filterOptions['asal_kayu'] = laporan_penerimaan_kayu_bulat::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('asal_kayu')->sort()->values();
                break;

            case 'penerimaan_kayu_olahan':
                $query = laporan_penerimaan_kayu_olahan::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['jenis_olahan'])) {
                    $query->where('jenis_olahan', $filters['jenis_olahan']);
                }
                if (isset($filters['asal_kayu'])) {
                    $query->where('asal_kayu', $filters['asal_kayu']);
                }

                // Apply sorting
                $sortColumn = $sortBy ?? 'tanggal';
                $validSortColumns = ['tanggal', 'nomor_dokumen', 'asal_kayu', 'jenis_olahan', 'jumlah_keping', 'volume'];
                if (!in_array($sortColumn, $validSortColumns)) {
                    $sortColumn = 'tanggal';
                }
                $query->orderBy($sortColumn, $sortDirection);

                $items = ($perPage === 'all') ? $query->get() : $query->paginate($perPage)->withQueryString();

                $filterOptions['jenis_olahan'] = laporan_penerimaan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_olahan')->sort()->values();
                $filterOptions['asal_kayu'] = laporan_penerimaan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('asal_kayu')->sort()->values();
                break;

            case 'mutasi_kayu_bulat':
                $query = laporan_mutasi_kayu_bulat::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['jenis_kayu'])) {
                    $query->where('jenis_kayu', $filters['jenis_kayu']);
                }

                // Apply sorting
                $sortColumn = $sortBy ?? 'jenis_kayu';
                $validSortColumns = ['jenis_kayu', 'persediaan_awal_volume', 'penambahan_volume', 'penggunaan_pengurangan_volume', 'persediaan_akhir_volume'];
                if (!in_array($sortColumn, $validSortColumns)) {
                    $sortColumn = 'jenis_kayu';
                }
                $query->orderBy($sortColumn, $sortDirection);

                $items = ($perPage === 'all') ? $query->get() : $query->paginate($perPage)->withQueryString();

                $filterOptions['jenis_kayu'] = laporan_mutasi_kayu_bulat::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_kayu')->sort()->values();
                break;

            case 'mutasi_kayu_olahan':
                $query = laporan_mutasi_kayu_olahan::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['jenis_olahan'])) {
                    $query->where('jenis_olahan', $filters['jenis_olahan']);
                }

                // Apply sorting
                $sortColumn = $sortBy ?? 'jenis_olahan';
                $validSortColumns = ['jenis_olahan', 'persediaan_awal_volume', 'penambahan_volume', 'penggunaan_pengurangan_volume', 'persediaan_akhir_volume'];
                if (!in_array($sortColumn, $validSortColumns)) {
                    $sortColumn = 'jenis_olahan';
                }
                $query->orderBy($sortColumn, $sortDirection);

                $items = ($perPage === 'all') ? $query->get() : $query->paginate($perPage)->withQueryString();

                $filterOptions['jenis_olahan'] = laporan_mutasi_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_olahan')->sort()->values();
                break;

            case 'penjualan_kayu_olahan':
                $query = laporan_penjualan_kayu_olahan::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['tujuan_kirim'])) {
                    $query->where('tujuan_kirim', $filters['tujuan_kirim']);
                }
                if (isset($filters['jenis_olahan'])) {
                    $query->where('jenis_olahan', $filters['jenis_olahan']);
                }
                if (isset($filters['ekspor_impor'])) {
                    // Case-insensitive matching: use LOWER(keterangan)
                    if ($filters['ekspor_impor'] === 'ekspor') {
                        $query->whereRaw('LOWER(keterangan) LIKE ?', ['%ekspor%']);
                    } elseif ($filters['ekspor_impor'] === 'lokal') {
                        // lokal => keterangan does NOT contain 'ekspor' (case-insensitive) or is null
                        $query->whereRaw("(keterangan IS NULL OR LOWER(keterangan) NOT LIKE ?)", ['%ekspor%']);
                    } else {
                        $val = strtolower($filters['ekspor_impor']);
                        $query->whereRaw('LOWER(keterangan) LIKE ?', ['%' . $val . '%']);
                    }
                }

                // Apply sorting
                $sortColumn = $sortBy ?? 'tanggal';
                $validSortColumns = ['tanggal', 'nomor_dokumen', 'tujuan_kirim', 'jenis_olahan', 'jumlah_keping', 'volume'];
                if (!in_array($sortColumn, $validSortColumns)) {
                    $sortColumn = 'tanggal';
                }
                $query->orderBy($sortColumn, $sortDirection);

                $items = ($perPage === 'all') ? $query->get() : $query->paginate($perPage)->withQueryString();

                $filterOptions['tujuan_kirim'] = laporan_penjualan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('tujuan_kirim')->sort()->values();
                $filterOptions['jenis_olahan'] = laporan_penjualan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_olahan')->sort()->values();
                break;
        }

        return [
            'items' => $items,
            'filterOptions' => $filterOptions
        ];
    }

    /**
     * Get detail laporan data untuk export (tanpa pagination)
     */
    public function getDetailLaporanForExport($bulan, $tahun, $jenis, $filters = [])
    {
        // Map jenis slug to jenis_laporan label
        $jenisLaporanMap = [
            'penerimaan_kayu_bulat' => 'Laporan Penerimaan Kayu Bulat',
            'penerimaan_kayu_olahan' => 'Laporan Penerimaan Kayu Olahan',
            'mutasi_kayu_bulat' => 'Laporan Mutasi Kayu Bulat (LMKB)',
            'mutasi_kayu_olahan' => 'Laporan Mutasi Kayu Olahan (LMKO)',
            'penjualan_kayu_olahan' => 'Laporan Penjualan Kayu Olahan',
        ];

        $jenisLaporanLabel = $jenisLaporanMap[$jenis] ?? null;

        $laporanQuery = Laporan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        // Filter by jenis_laporan to get only the specific report type
        if ($jenisLaporanLabel) {
            $laporanQuery->where('jenis_laporan', $jenisLaporanLabel);
        }

        // Jika ada filter industri_id, batasi laporan hanya untuk industri tersebut
        if (isset($filters['industri_id']) && $filters['industri_id']) {
            $laporanQuery->where('industri_id', $filters['industri_id']);
        }

        $laporanIds = $laporanQuery->pluck('id');

        $items = collect();

        switch ($jenis) {
            case 'penerimaan_kayu_bulat':
                $query = laporan_penerimaan_kayu_bulat::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['jenis_kayu'])) {
                    $query->where('jenis_kayu', $filters['jenis_kayu']);
                }
                if (isset($filters['asal_kayu'])) {
                    $query->where('asal_kayu', $filters['asal_kayu']);
                }

                $items = $query->orderBy('tanggal')->get();
                break;

            case 'penerimaan_kayu_olahan':
                $query = laporan_penerimaan_kayu_olahan::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['jenis_olahan'])) {
                    $query->where('jenis_olahan', $filters['jenis_olahan']);
                }
                if (isset($filters['asal_kayu'])) {
                    $query->where('asal_kayu', $filters['asal_kayu']);
                }

                $items = $query->orderBy('tanggal')->get();
                break;

            case 'mutasi_kayu_bulat':
                $query = laporan_mutasi_kayu_bulat::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['jenis_kayu'])) {
                    $query->where('jenis_kayu', $filters['jenis_kayu']);
                }

                $items = $query->orderBy('jenis_kayu')->get();
                break;

            case 'mutasi_kayu_olahan':
                $query = laporan_mutasi_kayu_olahan::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['jenis_olahan'])) {
                    $query->where('jenis_olahan', $filters['jenis_olahan']);
                }

                $items = $query->orderBy('jenis_olahan')->get();
                break;

            case 'penjualan_kayu_olahan':
                $query = laporan_penjualan_kayu_olahan::with(['laporan.industri'])
                    ->whereIn('laporan_id', $laporanIds);

                if (isset($filters['tujuan_kirim'])) {
                    $query->where('tujuan_kirim', $filters['tujuan_kirim']);
                }
                if (isset($filters['jenis_olahan'])) {
                    $query->where('jenis_olahan', $filters['jenis_olahan']);
                }
                if (isset($filters['ekspor_impor'])) {
                    // Case-insensitive matching: use LOWER(keterangan)
                    if ($filters['ekspor_impor'] === 'ekspor') {
                        $query->whereRaw('LOWER(keterangan) LIKE ?', ['%ekspor%']);
                    } elseif ($filters['ekspor_impor'] === 'lokal') {
                        // lokal => keterangan does NOT contain 'ekspor' (case-insensitive) or is null
                        $query->whereRaw("(keterangan IS NULL OR LOWER(keterangan) NOT LIKE ?)", ['%ekspor%']);
                    } else {
                        $val = strtolower($filters['ekspor_impor']);
                        $query->whereRaw('LOWER(keterangan) LIKE ?', ['%' . $val . '%']);
                    }
                }

                $items = $query->orderBy('tanggal')->get();
                break;
        }

        return $items;
    }

    /**
     * Parse tanggal dari berbagai format Excel ke format Y-m-d
     * Menggunakan logika yang sama dengan ValidationService untuk konsistensi
     */
    private function parseDate($value): ?string
    {
        if (empty($value))
            return null;

        // Jika sudah dalam format tanggal Excel (numeric)
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $s = trim((string) $value);

        // Jika format ISO year first (YYYY-MM-DD or YYYY/MM/DD)
        if (preg_match('/^\d{4}[\-\/]\d{1,2}[\-\/]\d{1,2}$/', $s)) {
            $d = \DateTime::createFromFormat('Y-m-d', str_replace('/', '-', $s));
            if ($d !== false)
                return $d->format('Y-m-d');
        }

        // Split by common separators (cek logika d/m/y vs m/d/y)
        if (preg_match('/^[0-9]{1,4}[\/\-\.][0-9]{1,2}[\/\-\.][0-9]{1,4}$/', $s)) {
            $parts = preg_split('/[\/\-\.]/', $s);
            if (count($parts) === 3) {
                $p0 = (int) $parts[0];
                $p1 = (int) $parts[1];
                $p2 = (int) $parts[2];

                // Normalize 2-digit year
                if ($p2 < 100) {
                    $p2 += ($p2 >= 70) ? 1900 : 2000;
                }

                // Logika Heuristik (sama dengan ValidationService):
                // - Jika part pertama > 12 -> pasti day (d/m/y)
                // - Jika part kedua > 12 -> pasti day (m/d/y) jarang terjadi di Indo
                // - Jika keduanya <= 12 -> Ambigoe, default ke d/m/y (Format Indo)
                if ($p0 > 12) {
                    $day = $p0;
                    $month = $p1;
                    $year = $p2;
                } elseif ($p1 > 12) {
                    $day = $p1;
                    $month = $p0;
                    $year = $p2;
                } else {
                    // Ambiguous - default to day/month/year (standard Indonesia)
                    $day = $p0;
                    $month = $p1;
                    $year = $p2;
                }

                if (checkdate($month, $day, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }
        }

        // Coba parse string tanggal dengan format umum lainnya
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'm/d/Y'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $s);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }
}
