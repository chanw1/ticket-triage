<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\StatsController;

Route::apiResource('tickets', TicketController::class)->except(['destroy']);  

// Route::post('tickets/{ticket}/classify', [TicketController::class, 'classify']);  
// Route::get('/stats', [StatsController::class, 'index']);


// Stricter: only 20 classify requests per minute
Route::post('tickets/{ticket}/classify', [TicketController::class, 'classify'])
    ->middleware('throttle:20,1'); 

// Stats can stay at 60/min
Route::get('/stats', [StatsController::class, 'index'])->middleware('throttle:60,1');

Route::get('tickets/export/csv', [TicketController::class, 'exportCsv']);