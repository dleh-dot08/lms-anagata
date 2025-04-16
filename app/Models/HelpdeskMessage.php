<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpdeskMessage extends Model
{
    protected $table = 'helpdesk_messages';

    protected $fillable = [
        'ticket_id', 'user_id', 'guest_name',
        'sender_type', 'message',
    ];

    public function ticket()
    {
        return $this->belongsTo(HelpdeskTicket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
