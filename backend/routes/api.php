<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\ProvisionController;
use App\Http\Controllers\CurioController;

Route::get('/health', fn() => response()->json(['status' => 'ok']));
Route::get('/heroes', [HeroController::class, 'index']);
Route::get('/provisions', [ProvisionController::class, 'calculate']);
Route::get('/curios', [CurioController::class, 'index']);
