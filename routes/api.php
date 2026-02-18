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

Route::get('get-confirmed-nesa-request-sum', [AndroidController::class, 'getConfirmedNesaRequestSummed']);

Route::get('get-confirmed-request-by-itemcode', [AndroidController::class, 'getConfirmedRequestbyItemcode']);

Route::post('get-confirmed-request-per-itemcode', [AndroidController::class, 'getConfirmedRequestPerItemcode']);

Route::get('get-cancelled-nesa-request', [AndroidController::class, 'getCancelledNesaRequest']);

Route::get('get-cancelled-request-by-itemcode', [AndroidController::class, 'getCancelledRequestbyItemcode']);


Route::post('updated-request', [AndroidController::class, 'updateRequest']);



Route::get('get-coa', [AndroidController::class, 'getCOA']);


Route::get('get-photo', [AndroidController::class, 'getPhoto']);

Route::post('updateIsOnline', [AndroidController::class, 'updateIsOnline']);

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

Route::post('get-lastweek-noti', [AndroidController::class, 'getPastNoti']);

Route::post('updateNotification', [AndroidController::class, 'updateNotification']);

Route::post('add-notification', [AndroidController::class, 'addNotification']);

Route::post('get-total-unread-noti', [AndroidController::class, 'getTotalUnreadNotification']);

Route::post('add-log', [AndroidController::class, 'addLog']);

Route::post('get-log', [AndroidController::class, 'listLogs']);

Route::post('get-selected-nesa', [AndroidController::class, 'getSelectedNesa']);

Route::get('get-all-course-action', [AndroidController::class, 'getAllCourseAction']);

Route::post('get-all-nesa-with-course-action', [AndroidController::class, 'getConfirmedNesaRequestWithCOA']);

Route::post('get-all-nesa-with-course-action-is-done', [AndroidController::class, 'getConfirmedNesaRequestWithCOAIsDone']);

Route::post('get-inbox', [AndroidController::class, 'getInbox']);

Route::get('chat/messages', [AndroidController::class, 'messages']);

Route::post('chat/send', [AndroidController::class, 'send']);

Route::get('getLatestNesaRequest', [AndroidController::class, 'getLatestNesaRequest']);

Route::post('updateCourseAction', [AndroidController::class, 'updateCourseAction']);

Route::get('countRequestsByStatusNESA', [AndroidController::class, 'countRequestsByStatusNESA']);

Route::get('app-update', [AndroidController::class, 'check']);

Route::get('getUserTypeName', [AndroidController::class, 'getUserTypeName']);

Route::get('checkServer', [AndroidController::class, 'checkServer']);

Route::get('getBusinessUnits', [AndroidController::class, 'getBusinessUnits']);

// #############################
// #######  BAD ORDER  #########
// #############################

Route::get('getBORequestsByCreatedBy', [AndroidController::class, 'getBORequestsByCreatedBy']);

Route::post('uploadBORequest', [AndroidController::class, 'uploadBORequest']);

Route::get('countRequestsByStatus', [AndroidController::class, 'countRequestsByStatus']);

Route::get('getAllBORequestByCreatedBy', [AndroidController::class, 'getAllBORequestByCreatedBy']);

Route::get('countAllRequestsByStatus', [AndroidController::class, 'countAllRequestsByStatus']);


// #############################
// #######  NESA V2  ###########
// #############################

Route::prefix('v2')->group(function () {
    Route::get('getPendingRequest', [AndroidController::class, 'getPendingRequestV2']);
    Route::get('getApprovedRequest', [AndroidController::class, 'getApprovedRequestV2']);
});