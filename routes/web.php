<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketDataController;
use App\Http\Controllers\ProfileController;
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

// Public routes
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])->name('dashboard');

// Market data public routes
Route::get('/market-data', [MarketDataController::class, 'index'])
    ->name('market-data.index');

// Authenticated user routes
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Market data authenticated routes with specific paths first
    Route::get('/market-data/create', [MarketDataController::class, 'create'])
        ->name('market-data.create');
    Route::get('/market-data/export', [MarketDataController::class, 'export'])
        ->middleware('throttle:10,1')
        ->name('market-data.export');
    Route::get('/market-data/export/{format}', [MarketDataController::class, 'exportFormat'])
        ->name('market-data.export.format');
    Route::get('/market-data/import', [MarketDataController::class, 'importForm'])
        ->name('market-data.import');
    Route::post('/market-data/import', [MarketDataController::class, 'importStore'])
        ->middleware('throttle:5,1')
        ->name('market-data.import.store');
    
    // Template download routes - put before parameter routes
    Route::get('/market-data/template/csv', [MarketDataController::class, 'downloadCsvTemplate'])
        ->name('market-data.template.csv');
    Route::get('/market-data/template/excel', [MarketDataController::class, 'downloadExcelTemplate'])
        ->name('market-data.template.excel');
    
    // Market data routes with parameters
    Route::get('/market-data/{id}', [MarketDataController::class, 'show'])
        ->name('market-data.show');
    Route::get('/market-data/{id}/edit', [MarketDataController::class, 'edit'])
        ->name('market-data.edit');
    Route::put('/market-data/{id}', [MarketDataController::class, 'update'])
        ->name('market-data.update');
    Route::delete('/market-data/{id}', [MarketDataController::class, 'destroy'])
        ->name('market-data.destroy');
    Route::post('/market-data/{id}/share', [MarketDataController::class, 'share'])
        ->name('market-data.share');
    Route::post('/market-data', [MarketDataController::class, 'store'])
        ->name('market-data.store');
    
    // Shared route
    Route::get('/shared/market-data/{id}', [MarketDataController::class, 'shared'])
        ->name('market-data.shared');
});

// Admin routes - separate from other authenticated routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/pending-approvals', [AdminController::class, 'pendingApprovals'])->name('admin.pending-approvals');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::patch('/admin/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.toggle-admin');
    Route::patch('/market-data/{id}/approve', [MarketDataController::class, 'approve'])->name('market-data.approve');
});

require __DIR__.'/auth.php';
