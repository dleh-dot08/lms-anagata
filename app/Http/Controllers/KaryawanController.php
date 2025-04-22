<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class KaryawanController extends Controller

{

    public function index()
    {
        return view('layouts.karyawan.dashboard', [
            // 'totalCourses' => Course::count(),
            // 'totalUsers' => User::count(),
            // 'totalMentors' => User::where('role', 'mentor')->count(),
            // 'recentCourses' => Course::with('mentor')->latest()->take(5)->get(),
        ]);
    }
}
