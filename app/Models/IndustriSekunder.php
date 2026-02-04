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
        'kapasitas_izin',
        'total_nilai_investasi',
        'total_pegawai'
    ];

    /**
     * Relationship ke tabel industries (parent)
     */
    public function industri()
    {
        return $this->belongsTo(IndustriBase::class, 'industri_id');
    }

    /**
     * Relationship polymorphic many-to-many ke master jenis produksi
     */
    public function jenisProduksi()
    {
        return $this->morphToMany(
            MasterJenisProduksi::class,
            'industri',
            'industri_jenis_produksi',
            'industri_id',
            'jenis_produksi_id'
        )->withPivot('kapasitas_izin', 'nama_custom')->withTimestamps();
    }

    /**
     * Scope untuk filter berdasarkan kapasitas izin
     */
    public function scopeByKapasitas($query, $kapasitas)
    {
        return $query->where('kapasitas_izin', $kapasitas);
    }
}
