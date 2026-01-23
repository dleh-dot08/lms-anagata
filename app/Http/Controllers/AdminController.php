<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;

class AdminController extends Controller
{
    public function index(DashboardMetricsService $metrics)
    {
        $data = $metrics->admin();

        return view('layouts.admin.dashboard', $data);
    }
}
