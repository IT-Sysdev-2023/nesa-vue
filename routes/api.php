<?php

use App\Http\Controllers\AndroidController;
use App\Models\NesaRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/user', [AndroidController::class, 'user']);

Route::get('item-codes', [AndroidController::class, 'ItemCodes']);

Route::get('count-item-codes', [AndroidController::class, 'countItemCodes']);

Route::get('supplier', [AndroidController::class, 'supplier']);

Route::get('count-supplier', [AndroidController::class, 'countSupplier']);

Route::get('getStoreUploads', [AndroidController::class, 'getStoreUploads']);

Route::get('getAllStoreUploads', [AndroidController::class, 'getAllStoreUploads']);

Route::post('uploadRequest', [AndroidController::class, 'uploadRequest']);



