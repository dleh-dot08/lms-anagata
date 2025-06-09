<?php

namespace App\Models;

use App\Models\MentorNote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'judul',
        'description',
        'pertemuan',
        'tanggal_pelaksanaan',
        'jam_mulai',  
        'jam_selesai',  
        'schedule_history',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime', 
        'schedule_history' => 'array',
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

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function note()
    {
        return $this->hasOne(MentorNote::class);
    }

    public function schedule_history()
    {
        return $this->hasMany(Meeting::class, 'id'); // Adjust 'meeting_id' to your actual foreign key in the schedule_histories table
    }


}
