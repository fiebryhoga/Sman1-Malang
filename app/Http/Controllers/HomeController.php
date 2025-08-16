<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama (landing page).
     */
    public function index()
    {
        // Memberitahu Inertia untuk merender komponen React bernama 'Home.jsx'
        // yang berada di dalam folder 'resources/js/Pages/'.
        return Inertia::render('Home');
    }
}
