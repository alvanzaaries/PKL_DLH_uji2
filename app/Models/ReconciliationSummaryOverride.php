<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconciliationSummaryOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'reconciliation_id',
        'metric',
        'satuan',
        'value',
    ];

    public function reconciliation()
    {
        return $this->belongsTo(Reconciliation::class);
    }
}
