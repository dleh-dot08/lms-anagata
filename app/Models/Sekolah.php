<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sekolah extends Model
{
    use SoftDeletes;

    protected $table = 'sekolah';
    protected $fillable = ['nama_sekolah', 'alamat', 'jenjang_id'];

    // Relationship with Jenjang
    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class);
    }

    // Relationship with PIC (User with role_id 6)
    public function pic()
    {
        return $this->hasOne(User::class, 'sekolah_id')->where('role_id', 6);
    }

    // Relationship with Peserta (Users with role_id 3)
    public function peserta()
    {
        return $this->hasMany(User::class, 'sekolah_id')->where('role_id', 3);
    }

    public function documents()
    {
        return $this->hasMany(SchoolDocument::class);
    }
} 