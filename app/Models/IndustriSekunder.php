<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk Industri Sekunder (PBUI)
 * Child table dengan FK ke industries
 */
class IndustriSekunder extends Model
{
    protected $table = 'industri_sekunder';
    
    /**
     * Fillable attributes spesifik untuk Industri Sekunder
     */
    protected $fillable = [
        'industri_id',
        'pemberi_izin',
        'jenis_produksi',
        'kapasitas_izin',
    ];

    /**
     * Relationship ke tabel industries (parent)
     */
    public function industri()
    {
        return $this->belongsTo(IndustriBase::class, 'industri_id');
    }

    /**
     * Scope untuk filter berdasarkan kapasitas izin
     */
    public function scopeByKapasitas($query, $kapasitas)
    {
        return $query->where('kapasitas_izin', $kapasitas);
    }
}
