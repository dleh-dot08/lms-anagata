<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ActivityUser extends Pivot
{
    protected $table = 'activity_user';

    protected $fillable = [
        'activity_id',
        'user_id',
        'tanggal_mulai',
        'tanggal_daftar',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_daftar'  => 'date',
        'tanggal_selesai' => 'date',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    // Relasi ke Activity
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
