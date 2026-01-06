<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKomoditas extends Model
{
    protected $table = 'master_komoditas';

    protected $fillable = [
        'nama_baku',
        'kategori'
    ];
}
