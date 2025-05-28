<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas';
    
    protected $fillable = [
        'nama',
        'id_jenjang'
    ];

    protected $dates = ['deleted_at'];

    protected $appends = ['nama_kelas'];

    public function getNamaKelasAttribute()
    {
        return $this->nama;
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang');
    }
} 