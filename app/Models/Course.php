<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_unik',
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

    public function projects()
    {
        return $this->hasMany(Project::class, 'course_id');
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

    public static function generateKodeKelas($length = 5)
    {
        do {
            $kode = 'CDM-' . strtoupper(Str::random($length));
        } while (self::where('kode_unik', $kode)->exists());

        return $kode;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withPivot('mentor_id', 'tanggal_daftar', 'tanggal_mulai', 'tanggal_selesai')
                    ->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class); // Assuming you have a Certificate model
    }

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
}
