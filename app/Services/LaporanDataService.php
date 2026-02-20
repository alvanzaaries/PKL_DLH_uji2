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
                // Kolom baru: Jenis Kayu | Pers.Awal Btg | Pers.Awal Vol | Penambahan Btg | Penambahan Vol | 
                //             Penggunaan Btg | Penggunaan Vol | Pers.Akhir Btg | Pers.Akhir Vol | Keterangan
                foreach ($rows as $item) {
                    $row = isset($item['cells']) ? $item['cells'] : $item;

                    $dataToInsert = [
                        'laporan_id' => $laporan->id,
                        'jenis_kayu' => $row[0] ?? '',
                        'persediaan_awal_btg' => (int) $this->toFloat($row[1] ?? 0),
                        'persediaan_awal_volume' => $this->toFloat($row[2] ?? 0),
                        'penambahan_btg' => (int) $this->toFloat($row[3] ?? 0),
                        'penambahan_volume' => $this->toFloat($row[4] ?? 0),
                        'penggunaan_pengurangan_btg' => (int) $this->toFloat($row[5] ?? 0),
                        'penggunaan_pengurangan_volume' => $this->toFloat($row[6] ?? 0),
                        'persediaan_akhir_btg' => (int) $this->toFloat($row[7] ?? 0),
                        'persediaan_akhir_volume' => $this->toFloat($row[8] ?? 0),
                        'keterangan' => $row[9] ?? '',
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
                        throw $e;
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
                // Kolom baru: Jenis Olahan | Pers.Awal Btg | Pers.Awal Vol | Penambahan Btg | Penambahan Vol | 
                //             Penggunaan Btg | Penggunaan Vol | Pers.Akhir Btg | Pers.Akhir Vol | Keterangan
                foreach ($rows as $item) {
                    $row = isset($item['cells']) ? $item['cells'] : $item;

                    laporan_mutasi_kayu_olahan::create([
                        'laporan_id' => $laporan->id,
                        'jenis_olahan' => $row[0] ?? '',
                        'persediaan_awal_btg' => (int) $this->toFloat($row[1] ?? 0),
                        'persediaan_awal_volume' => $this->toFloat($row[2] ?? 0),
                        'penambahan_btg' => (int) $this->toFloat($row[3] ?? 0),
                        'penambahan_volume' => $this->toFloat($row[4] ?? 0),
                        'penggunaan_pengurangan_btg' => (int) $this->toFloat($row[5] ?? 0),
                        'penggunaan_pengurangan_volume' => $this->toFloat($row[6] ?? 0),
                        'persediaan_akhir_btg' => (int) $this->toFloat($row[7] ?? 0),
                        'persediaan_akhir_volume' => $this->toFloat($row[8] ?? 0),
                        'keterangan' => $row[9] ?? '',
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

                // Calculate Grand Total
                $grandTotal = [
                    'jumlah_batang' => $query->clone()->sum('jumlah_batang'),
                    'volume' => $query->clone()->sum('volume'),
                ];

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

                // Calculate Grand Total
                $grandTotal = [
                    'jumlah_keping' => $query->clone()->sum('jumlah_keping'),
                    'volume' => $query->clone()->sum('volume'),
                ];

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

                // Calculate Grand Total
                $grandTotal = [
                    'persediaan_awal_btg' => $query->clone()->sum('persediaan_awal_btg'),
                    'persediaan_awal_volume' => $query->clone()->sum('persediaan_awal_volume'),
                    'penambahan_btg' => $query->clone()->sum('penambahan_btg'),
                    'penambahan_volume' => $query->clone()->sum('penambahan_volume'),
                    'penggunaan_pengurangan_btg' => $query->clone()->sum('penggunaan_pengurangan_btg'),
                    'penggunaan_pengurangan_volume' => $query->clone()->sum('penggunaan_pengurangan_volume'),
                    'persediaan_akhir_btg' => $query->clone()->sum('persediaan_akhir_btg'),
                    'persediaan_akhir_volume' => $query->clone()->sum('persediaan_akhir_volume'),
                ];

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

                // Calculate Grand Total
                $grandTotal = [
                    'persediaan_awal_btg' => $query->clone()->sum('persediaan_awal_btg'),
                    'persediaan_awal_volume' => $query->clone()->sum('persediaan_awal_volume'),
                    'penambahan_btg' => $query->clone()->sum('penambahan_btg'),
                    'penambahan_volume' => $query->clone()->sum('penambahan_volume'),
                    'penggunaan_pengurangan_btg' => $query->clone()->sum('penggunaan_pengurangan_btg'),
                    'penggunaan_pengurangan_volume' => $query->clone()->sum('penggunaan_pengurangan_volume'),
                    'persediaan_akhir_btg' => $query->clone()->sum('persediaan_akhir_btg'),
                    'persediaan_akhir_volume' => $query->clone()->sum('persediaan_akhir_volume'),
                ];

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

                $query->orderBy($sortColumn, $sortDirection);

                // Calculate Grand Total
                $grandTotal = [
                    'jumlah_keping' => $query->clone()->sum('jumlah_keping'),
                    'volume' => $query->clone()->sum('volume'),
                ];

                $items = ($perPage === 'all') ? $query->get() : $query->paginate($perPage)->withQueryString();

                $filterOptions['tujuan_kirim'] = laporan_penjualan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('tujuan_kirim')->sort()->values();
                $filterOptions['jenis_olahan'] = laporan_penjualan_kayu_olahan::whereIn('laporan_id', $laporanIds)
                    ->distinct()->pluck('jenis_olahan')->sort()->values();
                break;
        }

        return [
            'items' => $items,
            'filterOptions' => $filterOptions,
            'grandTotal' => $grandTotal ?? [],
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

        // Jika format ISO year first (DD/MM/YYYY or YYYY/MM/DD)
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

    /**
     * Get Rekap Tahunan dengan breakdown bulanan per industri
     * 
     * @param int $tahun Tahun yang akan direkap
     * @param string $kategori Kategori rekap (produksi_kayu_bulat, produksi_kayu_olahan, penjualan, pemenuhan_bahan_baku)
     * @param string $groupBy Grouping dimension (untuk kategori yang support)
     * @param string $eksporLokal Filter ekspor/lokal (untuk kategori penjualan)
     * @return array Array berisi data rekap per industri dengan breakdown bulanan
     */
    public function getRekapTahunan($tahun, $kategori, $groupBy = 'asal_kayu', $eksporLokal = 'semua')
    {
        switch ($kategori) {
            case 'produksi_kayu_bulat':
                return $this->rekapProduksiKayuBulat($tahun, $groupBy);
            case 'produksi_kayu_olahan':
                return $this->rekapProduksiKayuOlahan($tahun, $groupBy);
            case 'penjualan':
                return $this->rekapPenjualan($tahun, $groupBy, $eksporLokal);
            default:
                return [];
        }
    }

    /**
     * Rekap Tahunan: Pemenuhan Kayu Bulat
     * 
     * Menghitung total volume penerimaan kayu bulat dengan breakdown per bulan.
     * 
     * @param int $tahun Tahun yang akan direkap
     * @param string $groupBy Grouping dimension: 'kabupaten' (asal industri), 'asal_kayu'
     * @return array Format: [
     *   'data' => [
     *     group_key => [
     *       'nama' => 'Group Name',
     *       'bulan' => [1 => 100.5, 2 => 200.3, ..., 12 => 150.0],
     *       'total' => 1500.0
     *     ]
     *   ],
     *   'grand_total' => [1 => 500.0, 2 => 600.0, ..., 12 => 450.0, 'total' => 6000.0]
     * ]
     */
    private function rekapProduksiKayuBulat($tahun, $groupBy = 'kabupaten')
    {
        // Query laporan untuk tahun yang dipilih
        $laporanIds = Laporan::whereYear('tanggal', $tahun)
            ->where('jenis_laporan', 'Laporan Penerimaan Kayu Bulat')
            ->pluck('id');

        // Initialize with empty data structure
        $data = [];
        $grandTotal = array_fill(1, 12, 0);
        $grandTotalSum = 0;

        // If grouping by kabupaten (asal industri), initialize all Jawa Tengah kabupaten
        if ($groupBy === 'kabupaten') {
            $allKabupaten = $this->getAllJawaTengahKabupaten();
            foreach ($allKabupaten as $kabupaten) {
                $key = strtolower(trim($kabupaten));
                $data[$key] = [
                    'nama' => strtoupper($kabupaten),
                    'bulan' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
        }

        // If no data, return early with initialized structure
        if ($laporanIds->isEmpty()) {
            return [
                'data' => $data,
                'grand_total' => $grandTotal + ['total' => 0]
            ];
        }

        // Query data penerimaan kayu bulat
        $query = laporan_penerimaan_kayu_bulat::query()
            ->select(
                DB::raw('MONTH(laporan.tanggal) as bulan'),
                DB::raw('SUM(laporan_penerimaan_kayu_bulat.volume) as total_volume')
            )
            ->join('laporan', 'laporan_penerimaan_kayu_bulat.laporan_id', '=', 'laporan.id')
            ->whereIn('laporan_penerimaan_kayu_bulat.laporan_id', $laporanIds);

        // Add grouping based on selected dimension
        switch ($groupBy) {
            case 'kabupaten':
                // Join dengan industries untuk mendapatkan kabupaten (asal industri)
                $query->join('industries', 'laporan.industri_id', '=', 'industries.id')
                    ->addSelect(DB::raw('LOWER(TRIM(industries.kabupaten)) as group_key'))
                    ->addSelect(DB::raw('UPPER(industries.kabupaten) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
                break;

            case 'asal_kayu':
            default:
                $query->addSelect(DB::raw('LOWER(TRIM(laporan_penerimaan_kayu_bulat.asal_kayu)) as group_key'))
                    ->addSelect(DB::raw('UPPER(laporan_penerimaan_kayu_bulat.asal_kayu) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
                break;
        }

        $results = $query->get();

        // Process results and merge with initialized data
        foreach ($results as $row) {
            $key = $row->group_key;
            $bulan = (int) $row->bulan;
            $volume = (float) $row->total_volume;

            // Initialize group if not exists (for asal_kayu grouping)
            if (!isset($data[$key])) {
                $data[$key] = [
                    'nama' => $row->group_name ?? 'N/A',
                    'bulan' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }

            // Add volume to specific month
            $data[$key]['bulan'][$bulan] = $volume;
            $data[$key]['total'] += $volume;

            // Add to grand total
            $grandTotal[$bulan] += $volume;
            $grandTotalSum += $volume;
        }

        $grandTotal['total'] = $grandTotalSum;

        // Sort data by name
        uasort($data, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        return [
            'data' => $data,
            'grand_total' => $grandTotal
        ];
    }

    /**
     * Rekap Tahunan: Produksi Kayu Olahan
     * 
     * Menghitung total volume penerimaan kayu olahan dengan breakdown per bulan.
     * 
     * @param int $tahun Tahun yang akan direkap
     * @param string $groupBy Grouping dimension: 'asal_kayu', 'jenis_olahan'
     * @return array Format sama dengan rekapProduksiKayuBulat
     */
    private function rekapProduksiKayuOlahan($tahun, $groupBy = 'asal_kayu')
    {
        // Query laporan untuk tahun yang dipilih
        $laporanIds = Laporan::whereYear('tanggal', $tahun)
            ->where('jenis_laporan', 'Laporan Mutasi Kayu Olahan (LMKO)')
            ->pluck('id');

        // Initialize with empty data structure
        $data = [];
        $grandTotal = array_fill(1, 12, 0);
        $grandTotalSum = 0;

        // If grouping by asal_kayu (kabupaten), initialize all Jawa Tengah kabupaten
        if ($groupBy === 'asal_kayu') {
            $allKabupaten = $this->getAllJawaTengahKabupaten();
            foreach ($allKabupaten as $kabupaten) {
                $key = strtolower(trim($kabupaten));
                $data[$key] = [
                    'nama' => strtoupper($kabupaten),
                    'bulan' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
        }

        // If no data, return early with initialized structure
        if ($laporanIds->isEmpty()) {
            return [
                'data' => $data,
                'grand_total' => $grandTotal + ['total' => 0]
            ];
        }

        // Query data mutasi kayu olahan
        $query = laporan_mutasi_kayu_olahan::query()
            ->select(
                DB::raw('MONTH(laporan.tanggal) as bulan'),
                DB::raw('SUM(laporan_mutasi_kayu_olahan.penambahan_volume) as total_volume')
            )
            ->join('laporan', 'laporan_mutasi_kayu_olahan.laporan_id', '=', 'laporan.id')
            ->whereIn('laporan_mutasi_kayu_olahan.laporan_id', $laporanIds);

        // Add grouping based on selected dimension
        switch ($groupBy) {
            case 'asal_kayu':
                // Join dengan industries untuk mendapatkan kabupaten
                $query->join('industries', 'laporan.industri_id', '=', 'industries.id')
                    ->addSelect(DB::raw('LOWER(TRIM(industries.kabupaten)) as group_key'))
                    ->addSelect(DB::raw('UPPER(industries.kabupaten) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
                break;

            case 'jenis_olahan':
            default:
                $query->addSelect(DB::raw('LOWER(TRIM(laporan_mutasi_kayu_olahan.jenis_olahan)) as group_key'))
                    ->addSelect(DB::raw('UPPER(laporan_mutasi_kayu_olahan.jenis_olahan) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
                break;
        }

        $results = $query->get();

        // Process results and merge with initialized data
        foreach ($results as $row) {
            $key = $row->group_key;
            $bulan = (int) $row->bulan;
            $volume = (float) $row->total_volume;

            // Initialize group if not exists (for jenis_olahan grouping)
            if (!isset($data[$key])) {
                $data[$key] = [
                    'nama' => $row->group_name ?? 'N/A',
                    'bulan' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }

            // Add volume to specific month
            $data[$key]['bulan'][$bulan] = $volume;
            $data[$key]['total'] += $volume;

            // Add to grand total
            $grandTotal[$bulan] += $volume;
            $grandTotalSum += $volume;
        }

        $grandTotal['total'] = $grandTotalSum;

        // Sort data by name
        uasort($data, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        return [
            'data' => $data,
            'grand_total' => $grandTotal
        ];
    }

    /**
     * Get all kabupaten/kota in Jawa Tengah
     * Fetches from API with caching, falls back to hardcoded list if API fails
     * Source: https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json
     * 
     * @return array
     */
    private function getAllJawaTengahKabupaten()
    {
        $cacheKey = 'jateng_kabupaten_list';

        // Try to get from cache (24 hours)
        $cached = \Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        // Fetch from API
        try {
            $response = \Http::timeout(5)->get('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json');

            if ($response->successful()) {
                $data = $response->json();
                $kabupatenList = array_map(function ($item) {
                    return strtoupper($item['name']);
                }, $data);

                // Cache for 24 hours
                \Cache::put($cacheKey, $kabupatenList, now()->addHours(24));

                return $kabupatenList;
            }
        } catch (\Exception $e) {
            // Log error and use fallback
            \Log::warning('Failed to fetch kabupaten from API: ' . $e->getMessage());
        }

        // Fallback: hardcoded list (in case API is down)
        $fallbackList = [
            'KABUPATEN CILACAP',
            'KABUPATEN BANYUMAS',
            'KABUPATEN PURBALINGGA',
            'KABUPATEN BANJARNEGARA',
            'KABUPATEN KEBUMEN',
            'KABUPATEN PURWOREJO',
            'KABUPATEN WONOSOBO',
            'KABUPATEN MAGELANG',
            'KABUPATEN BOYOLALI',
            'KABUPATEN KLATEN',
            'KABUPATEN SUKOHARJO',
            'KABUPATEN WONOGIRI',
            'KABUPATEN KARANGANYAR',
            'KABUPATEN SRAGEN',
            'KABUPATEN GROBOGAN',
            'KABUPATEN BLORA',
            'KABUPATEN REMBANG',
            'KABUPATEN PATI',
            'KABUPATEN KUDUS',
            'KABUPATEN JEPARA',
            'KABUPATEN DEMAK',
            'KABUPATEN SEMARANG',
            'KABUPATEN TEMANGGUNG',
            'KABUPATEN KENDAL',
            'KABUPATEN BATANG',
            'KABUPATEN PEKALONGAN',
            'KABUPATEN PEMALANG',
            'KABUPATEN TEGAL',
            'KABUPATEN BREBES',
            'KOTA MAGELANG',
            'KOTA SURAKARTA',
            'KOTA SALATIGA',
            'KOTA SEMARANG',
            'KOTA PEKALONGAN',
            'KOTA TEGAL',
        ];

        // Cache fallback too (shorter duration - 1 hour)
        \Cache::put($cacheKey, $fallbackList, now()->addHour());

        return $fallbackList;
    }

    /**
     * Rekap Tahunan: Penjualan
     * 
     * Menghitung total penjualan per industri dengan breakdown per bulan.
     * Mendukung filter ekspor/lokal dan grouping by tujuan_kirim atau jenis_olahan.
     * 
     * @param int $tahun Tahun yang akan direkap
     * @param string $groupBy Grouping dimension: 'tujuan_kirim', 'jenis_olahan'
     * @param string $eksporLokal Filter: 'ekspor', 'lokal', 'semua'
     * @return array Format sama dengan rekapProduksiKayuBulat
     */
    private function rekapPenjualan($tahun, $groupBy = 'tujuan_kirim', $eksporLokal = 'semua')
    {
        // Query laporan untuk tahun yang dipilih
        $laporanIds = Laporan::whereYear('tanggal', $tahun)
            ->where('jenis_laporan', 'Laporan Penjualan Kayu Olahan')
            ->pluck('id');

        if ($laporanIds->isEmpty()) {
            return [
                'data' => [],
                'grand_total' => array_fill(1, 12, 0) + ['total' => 0]
            ];
        }

        // Query data penjualan kayu olahan
        $query = laporan_penjualan_kayu_olahan::query()
            ->select(
                DB::raw('MONTH(laporan.tanggal) as bulan'),
                DB::raw('SUM(laporan_penjualan_kayu_olahan.volume) as total_volume')
            )
            ->join('laporan', 'laporan_penjualan_kayu_olahan.laporan_id', '=', 'laporan.id')
            ->whereIn('laporan_penjualan_kayu_olahan.laporan_id', $laporanIds);

        // Filter ekspor/lokal berdasarkan keterangan (case-insensitive)
        if ($eksporLokal === 'ekspor') {
            $query->whereRaw('LOWER(laporan_penjualan_kayu_olahan.keterangan) LIKE ?', ['%ekspor%']);
        } elseif ($eksporLokal === 'lokal') {
            // Lokal = keterangan tidak mengandung 'ekspor' atau null
            $query->whereRaw('(laporan_penjualan_kayu_olahan.keterangan IS NULL OR LOWER(laporan_penjualan_kayu_olahan.keterangan) NOT LIKE ?)', ['%ekspor%']);
        }
        // Jika 'semua', tidak ada filter tambahan

        // Add grouping based on selected dimension
        switch ($groupBy) {
            case 'tujuan_kirim':
                $query->addSelect(DB::raw('LOWER(TRIM(laporan_penjualan_kayu_olahan.tujuan_kirim)) as group_key'))
                    ->addSelect(DB::raw('UPPER(laporan_penjualan_kayu_olahan.tujuan_kirim) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
                break;

            case 'jenis_olahan':
            default:
                $query->addSelect(DB::raw('LOWER(TRIM(laporan_penjualan_kayu_olahan.jenis_olahan)) as group_key'))
                    ->addSelect(DB::raw('UPPER(laporan_penjualan_kayu_olahan.jenis_olahan) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
                break;
        }

        $results = $query->get();

        // Process results into structured format
        $data = [];
        $grandTotal = array_fill(1, 12, 0);
        $grandTotalSum = 0;

        foreach ($results as $row) {
            $key = $row->group_key;
            $bulan = (int) $row->bulan;
            $volume = (float) $row->total_volume;

            // Initialize group if not exists
            if (!isset($data[$key])) {
                $data[$key] = [
                    'nama' => $row->group_name ?? 'N/A',
                    'bulan' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }

            // Add volume to specific month
            $data[$key]['bulan'][$bulan] = $volume;
            $data[$key]['total'] += $volume;

            // Add to grand total
            $grandTotal[$bulan] += $volume;
            $grandTotalSum += $volume;
        }

        $grandTotal['total'] = $grandTotalSum;

        // Sort data by name
        uasort($data, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        return [
            'data' => $data,
            'grand_total' => $grandTotal
        ];
    }

    /**
     * Rekap Tahunan: Pemenuhan Bahan Baku
     * 
     * Menggabungkan data penerimaan kayu bulat dan kayu olahan.
     * Mendukung grouping by asal_kayu (dari kolom) atau kabupaten (dari industries).
     * 
     * @param int $tahun Tahun yang akan direkap
     * @param string $groupBy Grouping dimension: 'asal_kayu', 'kabupaten'
     * @return array Format sama dengan rekapProduksiKayuBulat
     */
    private function rekapPemenuhanBahanBaku($tahun, $groupBy = 'asal_kayu')
    {
        // Query untuk penerimaan kayu bulat
        $laporanBulatIds = Laporan::whereYear('tanggal', $tahun)
            ->where('jenis_laporan', 'Laporan Penerimaan Kayu Bulat')
            ->pluck('id');

        // Query untuk penerimaan kayu olahan
        $laporanOlahanIds = Laporan::whereYear('tanggal', $tahun)
            ->where('jenis_laporan', 'Laporan Penerimaan Kayu Olahan')
            ->pluck('id');

        // Jika tidak ada data sama sekali
        if ($laporanBulatIds->isEmpty() && $laporanOlahanIds->isEmpty()) {
            return [
                'data' => [],
                'grand_total' => array_fill(1, 12, 0) + ['total' => 0]
            ];
        }

        $data = [];
        $grandTotal = array_fill(1, 12, 0);
        $grandTotalSum = 0;

        // Process Kayu Bulat
        if (!$laporanBulatIds->isEmpty()) {
            $queryBulat = laporan_penerimaan_kayu_bulat::query()
                ->select(
                    DB::raw('MONTH(laporan.tanggal) as bulan'),
                    DB::raw('SUM(laporan_penerimaan_kayu_bulat.volume) as total_volume')
                )
                ->join('laporan', 'laporan_penerimaan_kayu_bulat.laporan_id', '=', 'laporan.id')
                ->whereIn('laporan_penerimaan_kayu_bulat.laporan_id', $laporanBulatIds);

            // Add grouping based on selected dimension
            if ($groupBy === 'kabupaten') {
                // Group by kabupaten industri penerima
                $queryBulat->join('industries', 'laporan.industri_id', '=', 'industries.id')
                    ->addSelect(DB::raw('LOWER(TRIM(industries.kabupaten)) as group_key'))
                    ->addSelect(DB::raw('UPPER(industries.kabupaten) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
            } else {
                // Group by asal kayu (default)
                $queryBulat->addSelect(DB::raw('LOWER(TRIM(laporan_penerimaan_kayu_bulat.asal_kayu)) as group_key'))
                    ->addSelect(DB::raw('UPPER(laporan_penerimaan_kayu_bulat.asal_kayu) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
            }

            $resultsBulat = $queryBulat->get();

            foreach ($resultsBulat as $row) {
                $key = $row->group_key;
                $bulan = (int) $row->bulan;
                $volume = (float) $row->total_volume;

                if (!isset($data[$key])) {
                    $data[$key] = [
                        'nama' => $row->group_name ?? 'N/A',
                        'bulan' => array_fill(1, 12, 0),
                        'total' => 0
                    ];
                }

                $data[$key]['bulan'][$bulan] += $volume;
                $data[$key]['total'] += $volume;
                $grandTotal[$bulan] += $volume;
                $grandTotalSum += $volume;
            }
        }

        // Process Kayu Olahan
        if (!$laporanOlahanIds->isEmpty()) {
            $queryOlahan = laporan_penerimaan_kayu_olahan::query()
                ->select(
                    DB::raw('MONTH(laporan.tanggal) as bulan'),
                    DB::raw('SUM(laporan_penerimaan_kayu_olahan.volume) as total_volume')
                )
                ->join('laporan', 'laporan_penerimaan_kayu_olahan.laporan_id', '=', 'laporan.id')
                ->whereIn('laporan_penerimaan_kayu_olahan.laporan_id', $laporanOlahanIds);

            // Add grouping based on selected dimension
            if ($groupBy === 'kabupaten') {
                // Group by kabupaten industri penerima
                $queryOlahan->join('industries', 'laporan.industri_id', '=', 'industries.id')
                    ->addSelect(DB::raw('LOWER(TRIM(industries.kabupaten)) as group_key'))
                    ->addSelect(DB::raw('UPPER(industries.kabupaten) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
            } else {
                // Group by asal kayu (default)
                $queryOlahan->addSelect(DB::raw('LOWER(TRIM(laporan_penerimaan_kayu_olahan.asal_kayu)) as group_key'))
                    ->addSelect(DB::raw('UPPER(laporan_penerimaan_kayu_olahan.asal_kayu) as group_name'))
                    ->groupBy('group_key', 'group_name', 'bulan');
            }

            $resultsOlahan = $queryOlahan->get();

            foreach ($resultsOlahan as $row) {
                $key = $row->group_key;
                $bulan = (int) $row->bulan;
                $volume = (float) $row->total_volume;

                if (!isset($data[$key])) {
                    $data[$key] = [
                        'nama' => $row->group_name ?? 'N/A',
                        'bulan' => array_fill(1, 12, 0),
                        'total' => 0
                    ];
                }

                $data[$key]['bulan'][$bulan] += $volume;
                $data[$key]['total'] += $volume;
                $grandTotal[$bulan] += $volume;
                $grandTotalSum += $volume;
            }
        }

        $grandTotal['total'] = $grandTotalSum;

        // Sort data by name
        uasort($data, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        return [
            'data' => $data,
            'grand_total' => $grandTotal
        ];
    }
}
