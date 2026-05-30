<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'endpoint'       => 'required|string',
            'keys.auth'      => 'required|string',
            'keys.p256dh'    => 'required|string',
        ]);

        auth()->user()->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'] ?? null,
            $request->keys['auth'] ?? null,
            $request->contentEncoding ?? 'aesgcm'
        );

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);
        auth()->user()->deletePushSubscription($request->endpoint);

        return response()->json(['ok' => true]);
    }
}
