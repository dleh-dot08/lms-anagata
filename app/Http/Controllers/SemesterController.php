<?php
namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::all();
        return view('semester.index', compact('semesters'));
    }

    public function create()
    {
        return view('semester.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'year' => 'required',
            'is_active' => 'required|boolean',
        ]);
        Semester::create($request->all());
        return redirect()->route('semester.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit(Semester $semester)
    {
        return view('semester.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'name' => 'required',
            'year' => 'required',
            'is_active' => 'required|boolean',
        ]);

        // Jika semester diaktifkan, nonaktifkan semester lain
        if ($request->is_active == 1) {
            Semester::where('is_active', 1)->where('id', '!=', $semester->id)->update(['is_active' => 0]);
        }

        $semester->update($request->all());
        return redirect()->route('semester.index')->with('success', 'Semester berhasil diupdate.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();
        return redirect()->route('semester.index')->with('success', 'Semester berhasil dihapus.');
    }
}