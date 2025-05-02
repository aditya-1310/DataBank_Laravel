<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MarketDataControllerNew;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Market Data Routes
    Route::get('/market-data', [MarketDataControllerNew::class, 'index'])->name('market-data.index');
    Route::get('/market-data/create', [MarketDataControllerNew::class, 'create'])->name('market-data.create');
    Route::post('/market-data', [MarketDataControllerNew::class, 'store'])->name('market-data.store');
    Route::get('/market-data/{marketData}', [MarketDataControllerNew::class, 'show'])->name('market-data.show');
    Route::get('/market-data/{marketData}/edit', [MarketDataControllerNew::class, 'edit'])->name('market-data.edit');
    Route::put('/market-data/{marketData}', [MarketDataControllerNew::class, 'update'])->name('market-data.update');
    Route::delete('/market-data/{marketData}', [MarketDataControllerNew::class, 'destroy'])->name('market-data.destroy');
    
    // Bulk Import Routes
    Route::get('/market-data-import', [MarketDataControllerNew::class, 'importForm'])->name('market-data.import');
    Route::post('/market-data-import', [MarketDataControllerNew::class, 'import'])->name('market-data.import.process');
});

require __DIR__.'/auth.php'; 