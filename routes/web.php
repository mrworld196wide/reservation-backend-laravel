<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeatController;

Route::post('/reserve-seats', [SeatController::class, 'reserveSeats']);
Route::get('/seats/count', [SeatController::class, 'countSeats']);
Route::get('/seats', [SeatController::class, 'getAllSeats']);

