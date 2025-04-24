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
        $user = auth()->user();
        $query = Faq::query();
    
        if ($request->filled('search')) {
            $query->where('question', 'like', '%' . $request->search . '%');
        }
        $faqs = $query->whereNull('deleted_at')->latest()->paginate(10);
        // Only fetch non-deleted
        $query->whereNull('deleted_at');
    
        // Optional: customize visibility based on division
        if ($user->role->name === 'Admin') {
            $faqs = $query->latest()->paginate(10);
            return view('admin.faq.index', compact('faqs'));
        } elseif ($user->divisi === 'MRC') {
            // If MRC-specific filtering is needed, you can add conditions here
            return view('layouts.karyawan.faq.index', compact('faqs'));
        }
    
        abort(403, 'Unauthorized access');
    }
    

    public function create()
    {
        $user = auth()->user();

        if ($user->role->name === 'Admin') {
            return view('admin.faq.create');
        } elseif ($user->divisi === 'MRC') {
            return view('layouts.karyawan.faq.create');
        }

        abort(403, 'Unauthorized access');
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

        return $this->redirectToIndexWithSuccess('FAQ berhasil ditambahkan.');
    }

    public function show($id)
    {
        $faq = Faq::findOrFail($id);

        // Cek role user
        if (auth()->check()) {
            $user = auth()->user();
            // Sesuaikan view berdasarkan role
            if ($user->role_id == 1) { // Admin
                return view('admin.faq.show', compact('faq'));
            } elseif ($user->divisi === 'MRC') {
                return view('layouts.karyawan.faq.show', compact('faq'));
            } elseif ($user->role_id == 3) { // Peserta
                return view('peserta.faq.show', compact('faq'));
            }
        }

        // Default untuk Guest
        return view('guest.faq.show', compact('faq'));
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        $user = auth()->user();

        if ($user->role->name === 'Admin') {
            return view('admin.faq.edit', compact('faq'));
        } elseif ($user->divisi === 'MRC') {
            return view('layouts.karyawan.faq.edit', compact('faq'));
        }

        abort(403, 'Unauthorized access');
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

        return $this->redirectToIndexWithSuccess('FAQ berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->deleted_by = Auth::id();
        $faq->save();

        $faq->delete(); // Soft delete di sini

        return $this->redirectToIndexWithSuccess('FAQ berhasil dihapus.');
    }

    private function redirectToIndexWithSuccess($message)
    {
        $user = auth()->user();

        if ($user->role->name === 'Admin') {
            return redirect()->route('admin.faq.index')->with('success', $message);
        } elseif ($user->divisi === 'MRC') {
            return redirect()->route('faq.mrc.index')->with('success', $message);
        }

        abort(403, 'Unauthorized access');
    }
}
