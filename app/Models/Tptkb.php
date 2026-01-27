<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk TPT-KB (Tempat Pengumpulan Kayu Bulat)
 * Child table dengan FK ke industries
 */
class Tptkb extends Model
{
    protected $table = 'tpt_kb';
    
    /**
     * Fillable attributes spesifik untuk TPT-KB
     */
    protected $fillable = [
        'industri_id',
        'pemberi_izin',
        'sumber_bahan_baku',
        'kapasitas_izin',
        'masa_berlaku',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'masa_berlaku' => 'date',
    ];

    /**
     * Relationship ke tabel industries (parent)
     */
    public function industri()
    {
        return $this->belongsTo(IndustriBase::class, 'industri_id');
    }

    /**
     * Relationship ke Master Sumber (many-to-many)
     */
    public function sumberBahanBaku()
    {
        return $this->belongsToMany(MasterSumber::class, 'tptkb_sumber', 'tptkb_id', 'master_sumber_id')
                    ->withPivot('kapasitas')
                    ->withTimestamps();
    }

    /**
     * Scope untuk filter berdasarkan jenis kayu
     */
    public function scopeBySumberBahanBaku($query, $sumber)
    {
        return $query->where('sumber_bahan_baku', $sumber);
    }

    /**
     * Check if masa berlaku masih aktif (berdasarkan waktu Indonesia/WIB)
     */
    public function isMasaBerlakuAktif(): bool
    {
        if (!$this->masa_berlaku) {
            return false;
        }
        
        // Set timezone ke Asia/Jakarta (WIB)
        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $masaBerlaku = \Carbon\Carbon::parse($this->masa_berlaku, 'Asia/Jakarta')->startOfDay();
        
        return $masaBerlaku->greaterThanOrEqualTo($today);
    }

    /**
     * Get status masa berlaku sebagai string
     */
    public function getStatusMasaBerlaku(): string
    {
        return $this->isMasaBerlakuAktif() ? 'Aktif' : 'Kadaluarsa';
    }

    /**
     * Get sisa hari masa berlaku
     */
    public function getSisaHariMasaBerlaku(): int
    {
        if (!$this->masa_berlaku) {
            return 0;
        }
        
        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $masaBerlaku = \Carbon\Carbon::parse($this->masa_berlaku, 'Asia/Jakarta')->startOfDay();
        
        return $today->diffInDays($masaBerlaku, false);
    }
}
