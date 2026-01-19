<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk Perajin / End User
 * Child table dengan FK ke industries
 */
class Perajin extends Model
{
    protected $table = 'perajin';
    
    /**
     * Fillable attributes spesifik untuk Perajin
     */
    protected $fillable = [
        'industri_id',
    ];

    /**
     * Relationship ke tabel industries (parent)
     */
    public function industri()
    {
        return $this->belongsTo(IndustriBase::class, 'industri_id');
    }
}
