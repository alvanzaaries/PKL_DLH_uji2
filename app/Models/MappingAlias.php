<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingAlias extends Model
{
    protected $fillable = [
        'type',
        'alias',
        'master_id'
    ];
}
