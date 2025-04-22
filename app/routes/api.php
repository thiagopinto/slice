<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Ep747DataController;
use App\Http\Controllers\Api\V1\ClearingController;

Route::prefix('v1')->group(function () {
    Route::post('/ep747/upload', [Ep747DataController::class, 'store']);
    Route::get('/ep747/{id}', [Ep747DataController::class, 'show']);
    Route::get('/ep747/relatorio', [Ep747DataController::class, 'index']);
    Route::post('/clearing/upload', [ClearingController::class, 'store']);
    Route::get('/clearing/relatorio', [ClearingController::class, 'index']);

});
