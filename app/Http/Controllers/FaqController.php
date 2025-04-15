<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::latest()->paginate(10);
        return view('faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_active' => $request->has('is_active'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('faq.index')->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function edit(Faq $faq)
    {
        return view('faq.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
        ]);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_active' => $request->has('is_active'),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('faq.index')->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $faq->update(['deleted_by' => auth()->id()]);
        $faq->delete();

        return back()->with('success', 'FAQ berhasil dihapus.');
    }
}
