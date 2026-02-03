<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pejabat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nip',
        'pangkat',
        'jabatan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Helper to get active official
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}
