<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/openrouter-models', function () {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . config('services.openrouter.key'),
    ])->get('https://openrouter.ai/api/v1/models');

    return $response->json();
});