<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisLaporan extends Model
{
    use HasFactory;

    protected $table = 'master_jenis_laporan';

    protected $fillable = [
        'nama',
        'slug',
    ];

    /**
     * Relasi ke Laporan
     */
    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'jenis_laporan_id');
    }
}
