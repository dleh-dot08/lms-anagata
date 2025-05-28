<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SilabusController extends Controller
{
    /**
     * Menampilkan daftar kursus yang dimiliki oleh sekolah
     */
    public function index()
    {
        // Mendapatkan ID sekolah dari user yang login
        $sekolahId = Auth::user()->sekolah_id;
        
        // Mengambil semua kursus yang dimiliki oleh sekolah tersebut
        $courses = Course::where('sekolah_id', $sekolahId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('sekolah.silabus.index', compact('courses'));
    }
    
    /**
     * Menampilkan silabus dari kursus tertentu
     */
    public function show($id)
    {
        // Mendapatkan ID sekolah dari user yang login
        $sekolahId = Auth::user()->sekolah_id;
        
        // Mengambil kursus yang dimiliki oleh sekolah tersebut
        $course = Course::where('sekolah_id', $sekolahId)
            ->findOrFail($id);
        
        // Ubah URL menjadi embed-ready
        $course->silabus_pdf = convertToPreview($course->silabus_pdf);
        
        return view('sekolah.silabus.show', compact('course'));
    }
}