<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'judul',
        'konten',
        'video_url1',
        'video_url2',
        'video_url3',
        'file_materi1',
        'file_materi2',
        'file_materi3',
        'file_materi4',
        'file_materi5',
        'pertemuan_ke',
        'attachment_url1',
        'attachment_url2',
        'attachment_url3',
        'pertemuan_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'pertemuan_id');
    }

}
