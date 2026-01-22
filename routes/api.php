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

Route::post('cancelled-request', [AndroidController::class, 'CancelledRequest']);

Route::get('find-item-code', [AndroidController::class, 'findItemCode']);

Route::get('get-confirmed-nesa-request', [AndroidController::class, 'getConfirmedNesaRequest']);

Route::get('get-confirmed-request-by-itemcode', [AndroidController::class, 'getConfirmedRequestbyItemcode']);

Route::get('get-cancelled-nesa-request', [AndroidController::class, 'getCancelledNesaRequest']);

Route::get('get-cancelled-request-by-itemcode', [AndroidController::class, 'getCancelledRequestbyItemcode']);


Route::post('updated-request', [AndroidController::class, 'updateRequest']);



Route::get('get-coa', [AndroidController::class, 'getCOA']);


Route::get('get-photo', [AndroidController::class, 'getPhoto']);

Route::get('update-password', [AndroidController::class, 'updatePassword']);

Route::get('count-all-products', [AndroidController::class, 'countItemCodes']);


Route::get('count-all-suppliers', [AndroidController::class, 'countSuppliers']);

Route::get('getAllProducts', [AndroidController::class, 'ItemCodes']);

Route::get('getAllSuppliers', [AndroidController::class, 'getSuppliers']);

Route::get('countAllNesaRequest', [AndroidController::class, 'countAllNesaRequest']);

Route::get('countAllApprovedRequest', [AndroidController::class, 'countAllApprovedRequest']);

Route::get('countAllCancelledRequest', [AndroidController::class, 'countAllCancelledRequest']);

Route::get('exportProductsJson', [AndroidController::class, 'exportProductsJson']);

Route::post('add-review', [AndroidController::class, 'addReview']);

Route::post('get-today-noti', [AndroidController::class, 'getTodayNoti']);

Route::post('get-yesterday-noti', [AndroidController::class, 'getYesterdayNoti']);

Route::post('get-lastweek-noti', [AndroidController::class, 'getLastWeekNoti']);

Route::post('add-notification', [AndroidController::class, 'addNotification']);

Route::post('get-total-unread-noti', [AndroidController::class, 'getTotalUnreadNotification']);

Route::post('add-log', [AndroidController::class, 'addLog']);

Route::post('get-log', [AndroidController::class, 'listLogs']);

Route::post('get-selected-nesa', [AndroidController::class, 'getSelectedNesa']);

Route::get('get-all-course-action', [AndroidController::class, 'getAllCourseAction']);

Route::post('get-all-nesa-with-course-action', [AndroidController::class, 'getConfirmedNesaRequestWithCOA']);

Route::post('get-all-nesa-with-course-action-is-done', [AndroidController::class, 'getConfirmedNesaRequestWithCOAIsDone']);
Route::post('get-inbox', [AndroidController::class, 'getInbox']);


