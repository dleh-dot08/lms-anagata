<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskMessage;
use Illuminate\Http\Request;

class HelpdeskMessageController extends Controller
{
    public function store(Request $request)
    {
        $message = new HelpdeskMessage();
        $message->ticket_id = $request->ticket_id;
        $message->user_id = auth()->id();
        $message->sender_type = 'user';
        $message->message = $request->message;
        $message->save();

        return back();
    }
}
