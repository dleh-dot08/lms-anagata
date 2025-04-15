<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqFeedback extends Model
{
    protected $table = 'faq_feedback';

    protected $fillable = [
        'faq_id', 'user_id', 'guest_email', 'feedback',
    ];
}
