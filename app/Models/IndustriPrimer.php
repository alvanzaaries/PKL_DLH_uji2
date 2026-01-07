<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustriPrimer extends Model
{
    protected $table = 'industri_primer';
    
    protected $fillable = [
        'nama',
        'alamat',
        'penanggungjawab',
        'kabupaten',
        'kontak',
        'pemberi_izin',
        'jenis_produksi',
        'kapasitas_izin',
        'nomor_izin',
        'pelaporan',
        'dokumen_izin'
    ];
}
