<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        // Web Push non disponibile — funzionalità pianificata
        return response()->json(['ok' => true, 'note' => 'push non attivo']);
    }

    public function destroy(Request $request)
    {
        return response()->json(['ok' => true]);
    }
}
