<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Concrete Model untuk tabel industries
 * Digunakan untuk insert data parent tanpa abstract constraint
 */
class IndustriBase extends Model
{
    protected $table = 'industries';

    protected $fillable = [
        'nama',
        'alamat',
        'penanggungjawab',
        'kabupaten',
        'kontak',
        'nomor_izin',
        'tanggal',
        'type',
    ];

    /**
     * Relationship ke IndustriPrimer (one-to-one)
     */
    public function industriPrimer()
    {
        return $this->hasOne(IndustriPrimer::class, 'industri_id');
    }

    /**
     * Relationship ke IndustriSekunder (one-to-one)
     */
    public function industriSekunder()
    {
        return $this->hasOne(IndustriSekunder::class, 'industri_id');
    }

    /**
     * Relationship ke Tptkb (one-to-one)
     */
    public function tptkb()
    {
        return $this->hasOne(Tptkb::class, 'industri_id');
    }

    /**
     * Relationship ke EndUser (one-to-one)
     */
    public function endUser()
    {
        return $this->hasOne(EndUser::class, 'industri_id');
    }

    /**
     * Relationship ke Perajin (one-to-one)
     */
    public function perajin()
    {
        return $this->hasOne(Perajin::class, 'industri_id');
    }
}
