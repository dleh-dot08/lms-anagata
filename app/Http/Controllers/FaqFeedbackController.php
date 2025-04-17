<?php

namespace App\Http\Controllers;

use App\Models\FaqFeedback;
use Illuminate\Http\Request;

class FaqFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'faq_id' => 'required|exists:faqs,id',
            'feedback' => 'required|in:helpful,not_helpful',
        ]);

        FaqFeedback::create([
            'faq_id' => $request->faq_id,
            'user_id' => auth()->id(),
            'guest_email' => $request->guest_email,
            'feedback' => $request->feedback,
        ]);

        return response()->json(['status' => 'feedback_received']);
    }
}
