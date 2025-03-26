<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jenjang extends Model
{
    use SoftDeletes;

    protected $table = 'jenjang';

    protected $fillable = [
        'nama_jenjang',
        'description',
    ];

    protected $dates = ['deleted_at'];
}
