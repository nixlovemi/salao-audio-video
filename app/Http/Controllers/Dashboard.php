<?php

namespace App\Http\Controllers;

class Dashboard extends Controller
{
    public function index()
    {
        return view('site-dashboard', [
            'PAGE_TITLE' => 'Dashboard',
        ]);
    }
}
