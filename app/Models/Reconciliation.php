<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reconciliation extends Model
{
    protected $fillable = ['year', 'quarter', 'original_filename'];

    public function details()
    {
        return $this->hasMany(ReconciliationDetail::class);
    }
}
