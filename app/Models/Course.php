<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mentor_id',
        'nama_kelas',
        'deskripsi',
        'kategori_id',
        'jenjang_id',
        'level',
        'status',
        'waktu_mulai',
        'waktu_akhir',
        'harga',
        'jumlah_peserta'
    ];

    // Relasi ke Mentor (User dengan role_id = 2)
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id')->where('role_id', 2);
    }

    // Relasi ke Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Relasi ke Jenjang
    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
}
