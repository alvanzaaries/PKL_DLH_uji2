<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class laporan_penerimaan_kayu_olahan extends Model
{
    use HasFactory;

    protected $table = 'laporan_penerimaan_kayu_olahan';

    protected $fillable = [
        'laporan_id',
        'nomor_dokumen',
        'tanggal',
        'asal_kayu',
        'jenis_olahan',
        'jumlah_keping',
        'volume',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_keping' => 'integer',
        'volume' => 'float',
    ];

    /**
     * Relasi ke tabel laporan (master)
     */
    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }
}
