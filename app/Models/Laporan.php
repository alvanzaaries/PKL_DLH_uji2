<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    /** @use HasFactory<\Database\Factories\LaporanFactory> */
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'industri_id',
        'jenis_laporan',
        'tanggal',
    ];

    // Definisi jenis laporan yang tersedia (single source of truth)
    public const JENIS_LAPORAN = [
        "Laporan Mutasi Kayu Bulat (LMKB)",
        "Laporan Mutasi Kayu Olahan (LMKO)",
        "Laporan Penerimaan Kayu Bulat",
        "Laporan Penerimaan Kayu Olahan",
        "Laporan Penjualan Kayu Olahan",
    ];

    /**
     * Mendapatkan semua jenis laporan yang tersedia
     */
    public static function getJenisLaporan()
    {
        return self::JENIS_LAPORAN;
    }

    /**
     * Relasi ke industri
     */
    public function industri()
    {
        return $this->belongsTo(Industri::class);
    }

    /**
     * Relasi One-to-Many ke detail laporan penerimaan kayu bulat
     */
    public function penerimaanKayuBulat()
    {
        return $this->hasMany(laporan_penerimaan_kayu_bulat::class);
    }

    /**
     * Relasi One-to-Many ke detail laporan mutasi kayu bulat
     */
    public function mutasiKayuBulat()
    {
        return $this->hasMany(laporan_mutasi_kayu_bulat::class);
    }

    /**
     * Relasi One-to-Many ke detail laporan penerimaan kayu olahan
     */
    public function penerimaanKayuOlahan()
    {
        return $this->hasMany(laporan_penerimaan_kayu_olahan::class);
    }

    /**
     * Relasi One-to-Many ke detail laporan mutasi kayu olahan
     */
    public function mutasiKayuOlahan()
    {
        return $this->hasMany(laporan_mutasi_kayu_olahan::class);
    }

    /**
     * Relasi One-to-Many ke detail laporan penjualan kayu olahan
     */
    public function penjualanKayuOlahan()
    {
        return $this->hasMany(laporan_penjualan_kayu_olahan::class);
    }
}
