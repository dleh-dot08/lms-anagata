<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardMetricsService;

class PesertaController extends Controller
{
    public function index(Request $request, DashboardMetricsService $metrics)
    {
        $data = $metrics->peserta($request->user()->id);

        return view('layouts.peserta.dashboard', $data);
    }
}
