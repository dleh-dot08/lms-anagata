<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MentorController extends Controller
{
    public function index()
    {
        return view('layouts.mentor.dashboard', [
            // 'totalCourses' => Course::count(),
            // 'totalUsers' => User::count(),
            // 'totalMentors' => User::where('role', 'mentor')->count(),
            // 'recentCourses' => Course::with('mentor')->latest()->take(5)->get(),
        ]);
    }
}
