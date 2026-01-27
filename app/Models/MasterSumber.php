<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk Master Sumber Bahan Baku
 * Digunakan untuk TPTKB (Tempat Pengumpulan Kayu Bulat)
 */
class MasterSumber extends Model
{
    protected $table = 'master_sumber';
    
    protected $fillable = [
        'nama',
        'keterangan',
    ];

    /**
     * Relationship ke TPTKB (many-to-many)
     */
    public function tptkb()
    {
        return $this->belongsToMany(Tptkb::class, 'tptkb_sumber', 'master_sumber_id', 'tptkb_id')
                    ->withPivot('kapasitas')
                    ->withTimestamps();
    }
}
