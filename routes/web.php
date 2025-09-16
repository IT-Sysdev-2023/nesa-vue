<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NesaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeatherController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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
    Route::get('dashboard-info', [DashboardController::class, 'dashboardInfo'])->name('dashboardInfo');
    Route::get('weather-forecast', [WeatherController::class, 'fetchWeather'])->name('fetchWeather');
});

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::name('admin.')->group(function () {
            Route::get('masterfile', [AdminController::class, 'masterFile'])->name('masterfile');
            Route::get('sync-products', [AdminController::class, 'syncProducts'])->name('sync.products');
            Route::get('search-products', [AdminController::class, 'masterFile'])->name('search.products');
            Route::get('supplier-list', [AdminController::class, 'listOfSupplier'])->name('supplier.list');
            Route::get('sync-suppliers', [AdminController::class, 'syncSupplier'])->name('sync.supplier');

            // adding users routes
            Route::get('add-user', [AdminController::class, 'addUser'])->name('addUser');
            Route::get('get-suppliers', [AdminController::class, 'getSuppliers'])->name('getSuppliers');
            Route::get('selected_supplier', [AdminController::class, 'selectedSupplier'])->name('selectedSupplier');
            Route::post('submit-user', [AdminController::class, 'submitUser'])->name('submitUser');

            // usertype and user profile routes
            Route::get('userType', [AdminController::class, 'userType'])->name('userType');
            Route::get('view-profile', [AdminController::class, 'viewProfile'])->name('viewProfile');
            Route::post('update-credentials', [AdminController::class, 'updatePassword'])->name('updatePassword');
            Route::post('update-username', [AdminController::class, 'updateUsername'])->name('updateUsername');

            // setup user routes
            Route::get('setup-user', [AdminController::class, 'setupUser'])->name('setupUser');
            Route::post('delete-user-account', [AdminController::class, 'deleteUserAccount'])->name('deleteUserAccount');
            Route::post('update-user-details', [AdminController::class, 'updateUserDetails'])->name('updateUserDetails');

            // about route
            Route::get('about', [AdminController::class, 'about'])->name('about');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('nesa')->group(function () {
        Route::name('nesa.')->group(function () {
            Route::get('nesa-dashboard', [NesaController::class, 'dashboard'])->name('get.dashboard');
            Route::get('nesa-list', [NesaController::class, 'nesaList'])->name('get.list');
            Route::get('nesa-view-{itemcode}', [NesaController::class, 'nesaViewing'])->name('view.list');
            Route::get('send-email', [NesaController::class, 'sendEmailFunction'])->name('send.email');
            Route::get('search-nesa-supplier', [NesaController::class, 'nesaList'])->name('search.supplier');
            Route::get('consolidate', [NesaController::class, 'consolidateProcess'])->name('consolidate');
            Route::get('consolidate-list', [NesaController::class, 'consolidatedList'])->name('get.consolidated');
            Route::get('nesa-history', [NesaController::class, 'nesaHistory'])->name('get.history');
            Route::get('nesa-history-details', [NesaController::class, 'nesaHistoryDetails'])->name('get.details');
            Route::get('nesa-pending-details', [NesaController::class, 'pendingDetails'])->name('get.pending.details');
            Route::put('nesa-update-course-of-course', [NesaController::class, 'updateCourseOfAction'])->name('update.course_of_action');
            Route::get('nesa-pending-for-approval', [NesaController::class, 'pendingApproval'])->name('get.pending.for.approval');
            Route::get('nesa-get-approved', [NesaController::class, 'getApprovedNesa'])->name('get.approved.nesa');
            Route::put('nesa-tag-coa', [NesaController::class, 'tagCoa'])->name('tag.coa');
            Route::put('approve-pending-nesa', [NesaController::class, 'approvePendingNesa'])->name('approve.pending.nesa');
            Route::get('approve-details', [NesaController::class, 'approvedDetails'])->name('get.approved.details');
            Route::put('tag-approved-details', [NesaController::class, 'taggedApprovedDetails'])->name('tag.approved.nesa');
            Route::get('tag-approved-index', [NesaController::class, 'tagApprovedIndex'])->name('tag.approved');
            Route::get('/download/{filename}', function ($filename) {
                $filePath = storage_path('app/public/' . $filename);
                return response()->download($filePath);
            })->where('filename', '.*')->name('download');


            Route::name('count.')->group(function () {
                Route::get('count-nesa', [NesaController::class, 'countNesa'])->name('data');
            });
        });
    });
});

require __DIR__ . '/auth.php';
