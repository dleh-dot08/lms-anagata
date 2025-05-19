<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'judul',
        'description',
        'pertemuan',
        'tanggal_pelaksanaan',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
    ];    

    // Relasi ke model Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Meeting.php
    public function lesson()
    {
        return $this->hasOne(Lesson::class, 'pertemuan_id');
    }    

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }


}
