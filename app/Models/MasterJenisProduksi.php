<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk Master Jenis Produksi
 * Digunakan untuk industri primer dan sekunder
 */
class MasterJenisProduksi extends Model
{
    protected $table = 'master_jenis_produksi';

    protected $fillable = [
        'nama',
        'kategori',
        'satuan',
        'keterangan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Scope untuk jenis produksi aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope untuk filter by kategori
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where(function($q) use ($kategori) {
            $q->where('kategori', $kategori)
              ->orWhere('kategori', 'both');
        });
    }

    /**
     * Relationship polymorphic ke industri primer
     */
    public function industriPrimer()
    {
        return $this->morphedByMany(
            IndustriPrimer::class,
            'industri',
            'industri_jenis_produksi',
            'jenis_produksi_id',
            'industri_id'
        )->withPivot('kapasitas_izin');
    }

    /**
     * Relationship polymorphic ke industri sekunder
     */
    public function industriSekunder()
    {
        return $this->morphedByMany(
            IndustriSekunder::class,
            'industri',
            'industri_jenis_produksi',
            'jenis_produksi_id',
            'industri_id'
        )->withPivot('kapasitas_izin');
    }
}
