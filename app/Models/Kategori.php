<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes;

    protected $table = 'kategoris';

    protected $fillable = [
        'nama_kategori',
        'description',
    ];

    protected $dates = ['deleted_at'];
}
