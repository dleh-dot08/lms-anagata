<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpdeskTicket extends Model
{
    protected $table = 'helpdesk_tickets';

    protected $fillable = [
        'user_id', 'guest_name', 'guest_email',
        'subject', 'status', 'closed_at',
    ];

    public function messages()
    {
        return $this->hasMany(HelpdeskMessage::class, 'ticket_id');
    }
}
