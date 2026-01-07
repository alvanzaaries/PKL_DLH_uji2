<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReconciliationDetail extends Model
{
    protected $fillable = [
        'reconciliation_id', 
        'wilayah', 
        'bulan',
        'no_urut',
        'jenis_sdh', 
        'volume',
        'satuan',
        'lhp_no',
        'lhp_tanggal',
        'lhp_nilai',
        'billing_no',
        'billing_tanggal',
        'billing_nilai',
        'setor_tanggal',
        'setor_bank',
        'setor_ntpn',
        'setor_ntb',
        'setor_nilai',
        'raw_data'
    ];

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
