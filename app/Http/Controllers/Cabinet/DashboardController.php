<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('cabinet.dashboard.index');
    }
}
