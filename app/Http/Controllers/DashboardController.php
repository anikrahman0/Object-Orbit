<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $storage_count = auth()->user()->storageConnection()->count();
        return view('dashboard', compact('storage_count'));
    }
}
