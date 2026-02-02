<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class laporan_mutasi_kayu_bulat extends Model
{
    use HasFactory;

    protected $table = 'laporan_mutasi_kayu_bulat';

    protected $fillable = [
        'laporan_id',
        'jenis_kayu',
        'persediaan_awal_btg',
        'persediaan_awal_volume',
        'penambahan_btg',
        'penambahan_volume',
        'penggunaan_pengurangan_btg',
        'penggunaan_pengurangan_volume',
        'persediaan_akhir_btg',
        'persediaan_akhir_volume',
        'keterangan'
    ];

    protected $casts = [
        'persediaan_awal_btg' => 'integer',
        'persediaan_awal_volume' => 'float',
        'penambahan_btg' => 'integer',
        'penambahan_volume' => 'float',
        'penggunaan_pengurangan_btg' => 'integer',
        'penggunaan_pengurangan_volume' => 'float',
        'persediaan_akhir_btg' => 'integer',
        'persediaan_akhir_volume' => 'float',
    ];

    /**
     * Relasi ke tabel laporan (master)
     */
    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }
}
