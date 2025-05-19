<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'peserta_id',
        'mentor_id',
        'creativity_score',
        'program_score',
        'design_score',
        'total_score',
        'feedback',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function peserta()
    {
        return $this->belongsTo(User::class, 'peserta_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}

