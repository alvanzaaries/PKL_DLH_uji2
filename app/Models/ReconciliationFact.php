<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReconciliationFact extends Model
{
    protected $fillable = [
        'reconciliation_detail_id',
        'wilayah_id',
        'komoditas_id',
        'volume'
    ];

    public function detail()
    {
        return $this->belongsTo(ReconciliationDetail::class, 'reconciliation_detail_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(MasterWilayah::class, 'wilayah_id');
    }

    public function komoditas()
    {
        return $this->belongsTo(MasterKomoditas::class, 'komoditas_id');
    }
}
