<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconciliationFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'reconciliation_id',
        'original_filename',
        'mime_type',
        'size',
        'content',
    ];

    public function reconciliation()
    {
        return $this->belongsTo(Reconciliation::class);
    }
}
