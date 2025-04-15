<?php

namespace App\Http\Controllers;

use App\Models\ChatLog;
use Illuminate\Http\Request;

class ChatLogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
        ]);

        ChatLog::create([
            'user_id' => auth()->id(),
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'question' => $request->question,
            'matched_answer' => $request->matched_answer,
            'is_satisfied' => $request->is_satisfied,
        ]);

        return response()->json(['status' => 'success']);
    }
}
