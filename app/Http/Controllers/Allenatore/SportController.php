<?php

namespace App\Http\Controllers\Allenatore;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SportController extends Controller
{
    public function index()
    {
        $sports = Sport::all();
        return view('allenatore.impostazioni.sports', compact('sports'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nome' => 'required|string|max:100|unique:sports,nome']);
        Sport::create(['nome' => $data['nome'], 'slug' => Str::slug($data['nome'])]);

        return back()->with('success', 'Sport aggiunto.');
    }

    public function update(Request $request, Sport $sport)
    {
        $data = $request->validate(['nome' => 'required|string|max:100', 'attivo' => 'boolean']);
        $sport->update([...$data, 'attivo' => $request->boolean('attivo')]);

        return back()->with('success', 'Sport aggiornato.');
    }

    public function destroy(Sport $sport)
    {
        $sport->delete();
        return back()->with('success', 'Sport eliminato.');
    }
}
