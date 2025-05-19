<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'judul',
        'deskripsi',
        'file_attachment',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function submissionBy($userId)
    {
        return $this->submissions()->where('user_id', $userId)->first();
    }
}
