<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk Industri Primer (PBPHH)
 * Child table dengan FK ke industries
 */
class IndustriPrimer extends Model
{
    protected $table = 'industri_primer';
    
    /**
     * Fillable attributes spesifik untuk Industri Primer
     */
    protected $fillable = [
        'industri_id',
        'pemberi_izin',
        'jenis_produksi',
        'kapasitas_izin',
        'pelaporan',
        'dokumen_izin'
    ];

    /**
     * Relationship ke tabel industries (parent)
     * Many-to-One: Many industri_primer records belong to one industry
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

    /**
     * Scope untuk filter berdasarkan jenis produksi
     */
    public function scopeByJenisProduksi($query, $jenisProduksi)
    {
        return $query->where('jenis_produksi', $jenisProduksi);
    }

    /**
     * Check if pelaporan is active
     */
    public function isAktif(): bool
    {
        return $this->pelaporan === 'Aktif';
    }

    /**
     * Get full data dengan join ke parent
     */
    public function getFullData()
    {
        return $this->with('industri')->first();
    }
}
