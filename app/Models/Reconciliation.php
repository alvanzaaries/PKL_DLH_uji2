<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reconciliation extends Model
{
    protected $fillable = [
        'year',
        'quarter',
        'kph',
        'original_filename',
        'user_id',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(ReconciliationDetail::class);
    }

    public function facts()
    {
        return $this->hasManyThrough(ReconciliationFact::class, ReconciliationDetail::class);
    }
}
