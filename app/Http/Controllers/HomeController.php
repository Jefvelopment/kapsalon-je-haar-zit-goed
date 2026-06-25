<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $treatments = Treatment::orderBy('name')->get();

        return view('home.index', compact('treatments'));
    }

    public function privacyPolicy()
    {
        return view('home.privacy-policy');
    }
}