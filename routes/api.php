<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/projects', [\App\Http\Controllers\Api\ProjectApiController::class, 'index']);
Route::get('/projects/{id}', [\App\Http\Controllers\Api\ProjectApiController::class, 'show']);
