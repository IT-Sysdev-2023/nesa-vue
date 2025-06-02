<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\NesaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::name('admin.')->group(function () {
            Route::get('masterfile', [AdminController::class, 'masterFile'])->name('masterfile');
            Route::get('sync-products', [AdminController::class, 'syncProducts'])->name('sync.products');
            Route::get('search-products', [AdminController::class, 'masterFile'])->name('search.products');
            Route::get('supplier-list', [AdminController::class, 'listOfSupplier'])->name('supplier.list');
            Route::get('sync-suppliers', [AdminController::class, 'syncSupplier'])->name('sync.supplier');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('nesa')->group(function () {
        Route::name('nesa.')->group(function () {
            Route::get('nesa-list', [NesaController::class, 'nesaList'])->name('get.list');
        });
    });
});

require __DIR__ . '/auth.php';
