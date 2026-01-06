<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReconciliationDetail extends Model
{
    protected $fillable = ['reconciliation_id', 'wilayah', 'jenis_sdh', 'raw_data'];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function reconciliation()
    {
        return $this->belongsTo(Reconciliation::class);
    }

    public function facts()
    {
        return $this->hasMany(ReconciliationFact::class, 'reconciliation_detail_id');
    }
}
