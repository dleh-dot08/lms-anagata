<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'data_before',
        'data_after',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
