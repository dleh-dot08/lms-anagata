<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->filled('search')) {
            $query->where('question', 'like', '%' . $request->search . '%');
        }

        $faqs = $query->latest()->paginate(10);

        return view('admin.helpdesk.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_active' => $request->is_active ?? 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.helpdesk.faq.index')->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_active' => $request->is_active ?? 1,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.helpdesk.faq.index')->with('success', 'FAQ berhasil diubah.');
    }
    
    public function show($id)
    {
        $faq = Faq::findOrFail($id);

        return view('admin.helpdesk.faq.show', compact('faq'));
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->deleted_by = Auth::id();
        $faq->save();
        $faq->delete();

        return redirect()->route('admin.helpdesk.faq.index')->with('success', 'FAQ berhasil dihapus.');
    }
}
