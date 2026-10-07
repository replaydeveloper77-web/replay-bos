<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Foundation for future integrations (RADIUS, payment gateway) to call into REPLAY BOS.
// Every route here requires a Sanctum bearer token — see Settings > API Token.
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ping', function (Request $request) {
        return response()->json([
            'ok' => true,
            'authenticated_as' => $request->user()->username,
            'token_name' => $request->user()->currentAccessToken()->name,
            'server_time' => now()->toIso8601String(),
        ]);
    });
});
