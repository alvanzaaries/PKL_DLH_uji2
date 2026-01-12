<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class laporan_penerimaan_kayu_bulat extends Model
{
    protected $table = 'laporan_penerimaan_kayu_bulat';

    protected $fillable = [
        'laporan_id',
        'nomor_dokumen',
        'tanggal',
        'asal_kayu',
        'jenis_kayu',
        'jumlah_batang',
        'volume',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_batang' => 'integer',
        'volume' => 'float',
    ];
    
    /** @use HasFactory<\Database\Factories\LaporanPenerimaanKayuBulatFactory> */
    use HasFactory;

    /**
     * Relasi ke tabel laporan (master)
     */
    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }
}
