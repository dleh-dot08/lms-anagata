<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    
    public function redirectBasedOnRole(Request $request)
    {
        // If admin, redirect directly
        if (Auth::check() && Auth::user()->role_id == 1) {
            return redirect()->route('admin.faq.index');
        }

        // For public or non-admin users
        $query = Faq::where('is_active', 1);

        if ($request->filled('search')) {
            $query->where('question', 'like', '%' . $request->search . '%');
        }

        $faqs = $query->latest()->paginate(10);

        return view('faq.public', compact('faqs'));
    }


    // public function showPublicFaqs(Request $request)
    // {
    //     $query = Faq::query();

    //     if ($request->filled('search')) {
    //         $query->where('question', 'like', '%' . $request->search . '%');
    //     }

    //     $faqs = $query->where('is_active', 1)->latest()->paginate(10);

    //     return view('faq.public', compact('faqs'));
    // }
    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->filled('search')) {
            $query->where('question', 'like', '%' . $request->search . '%');
        }

        $faqs = $query->whereNull('deleted_at')->latest()->paginate(10);

        return view('admin.faq.index', compact('faqs'));
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
            'is_active' => 'nullable|boolean',
        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_active' => $request->is_active ?? 1,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function show($id)
    {
        $faq = Faq::findOrFail($id);

        // Cek apakah user adalah admin
        if (auth()->check() && auth()->user()->role_id == 1) {
            // Jika admin, tampilkan FAQ di tampilan admin
            return view('admin.faq.show', compact('faq'));
        }

        // Cek apakah user adalah peserta
        if (auth()->check() && auth()->user()->role_id == 3) {
            // Jika peserta, tampilkan FAQ di tampilan peserta
            return view('peserta.faq.show', compact('faq'));
        }

        // Jika guest, tampilkan FAQ di tampilan guest
        return view('guest.faq.show', compact('faq'));
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_active' => $request->is_active ?? 1,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->deleted_by = Auth::id();
        $faq->save();

        $faq->delete(); // Soft delete di sini

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil dihapus.');
    }
}
