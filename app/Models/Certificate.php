<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'activities_id',
        'kode_sertifikat',
        'file_sertifikat',
        'tanggal_terbit',
        'status',
    ];

    // Relasi ke User (Peserta)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi ke Activity
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
