<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk End User / Perajin
 * Child table dengan FK ke industries
 * Paling sederhana karena untuk usaha mikro
 */
class EndUser extends Model
{
    protected $table = 'end_user';
    
    /**
     * Fillable attributes spesifik untuk End User/Perajin
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
