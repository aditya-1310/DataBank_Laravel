<?php

use App\Http\Controllers\Api\MarketDataApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::get('/market-data', [MarketDataApiController::class, 'index']);
Route::get('/market-data/{id}', [MarketDataApiController::class, 'show']);
Route::get('/filters', [MarketDataApiController::class, 'getFilters']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/market-data', [MarketDataApiController::class, 'store']);
    Route::put('/market-data/{id}', [MarketDataApiController::class, 'update']);
    Route::delete('/market-data/{id}', [MarketDataApiController::class, 'destroy']);
}); 