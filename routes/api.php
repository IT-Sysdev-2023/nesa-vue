<?php

use App\Http\Controllers\AndroidController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return response()->json(
            [
                'uri' => $route->uri(),
                'methods' => $route->methods()
            ]
        );
    });

    return response()->json($routes->values());
});


Route::get('/users', [AndroidController::class, 'user']);




Route::prefix('products')->group(function () {
    Route::get('/', [AndroidController::class, 'ItemCodes']);
    Route::get('count-item-codes', [AndroidController::class, 'countItemCodes']);
});


Route::prefix('suppliers')->group(function () {
    Route::get('/', [AndroidController::class, 'suppliers']);
    Route::get('count-all-suppliers', [AndroidController::class, 'countSuppliers']);
});



Route::get('getOtherStoreUploads', [AndroidController::class, 'getOtherStoreUploads']);

Route::get('getProductsNotYetUploaded', [AndroidController::class, 'getProductsNotYetUploaded']);

Route::post('uploadRequest', [AndroidController::class, 'uploadRequest']);

Route::get('isConsolidated', [AndroidController::class, 'isConsolidated']);

Route::get('get-pending-request', [AndroidController::class, 'getPendingRequest']);

Route::get('get-pending-request-by-itemcode', [AndroidController::class, 'getPendingRequestbyItemcode']);



Route::get('view-request-details', [AndroidController::class, 'ViewRequestDetails']);

Route::get('ViewRequestDetailsFirstSubmited', [AndroidController::class, 'ViewRequestDetailsFirstSubmited']);

Route::post('approve-request', [AndroidController::class, 'ApproveRequest']);

Route::get('find-item-code', [AndroidController::class, 'findItemCode']);

Route::get('get-confirmed-nesa-request', [AndroidController::class, 'getConfirmedNesaRequest']);

Route::get('get-confirmed-request-by-itemcode', [AndroidController::class, 'getConfirmedRequestbyItemcode']);


Route::post('updated-request', [AndroidController::class, 'updateRequest']);



Route::get('get-coa', [AndroidController::class, 'getCOA']);



