<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->hasRole('allenatore')) {
                return redirect()->route('allenatore.dashboard');
            }
            return redirect()->route('atleta.sedute');
        }
        return redirect()->route('login');
    }
}
