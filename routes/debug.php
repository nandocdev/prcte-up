<?php

use Illuminate\Support\Facades\Route;

Route::post('/debug-form', function (Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('Debug form data received', [
        'all_data' => $request->all(),
        'has_title' => $request->has('title'),
        'title_value' => $request->input('title'),
        'input_keys' => array_keys($request->all())
    ]);

    return response()->json([
        'status' => 'received',
        'data' => $request->all()
    ]);
})->middleware('auth');
