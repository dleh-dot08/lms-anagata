<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    protected $table = 'chat_logs';

    protected $fillable = [
        'user_id', 'guest_name', 'guest_email',
        'question', 'matched_answer', 'is_satisfied',
    ];

    protected $casts = [
        'is_satisfied' => 'boolean',
    ];
}
