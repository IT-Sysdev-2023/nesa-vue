<?php

use App\Models\NesaRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/user', function () {
    return response()->json(User::select(
        'id',
        'bu',
        'usertype',
        'firstname',
        'middlename',
        'lastname',
        'name_extention',
        'username',
        'password',
        'employee_id'
    )->get());
});

Route::get('item-codes', function () {
    return response()->json(
        Product::select(
            'id',
            'id',
            'itemcode',
            'description',
            'uom',
            'uom_price',
            'vendor_no'
        )->cursorPaginate(100)
    );
});
Route::get('count-item-codes', function () {
    return response()->json(
        Product::count()
    );
});

Route::get('supplier', function () {
    return response()->json(Supplier::select('id', 'supplier_code', 'name')->cursorPaginate(100));
});
Route::get('count-supplier', function () {
    return response()->json(
        Supplier::count()
    );
});

Route::get('getStoreUploads', function (Request $request) {
    $data = NesaRequest::select('name')->where('itemcode', $request->itemcode)
        ->join('business_units', 'business_units.id', '=', 'nesa_requests.bu')
        ->get();
    return response()->json($data ?? []);
});

Route::get('getAllStoreUploads', function () {
    $data = NesaRequest::select('nesa_requests.itemcode', 'products.description')
        ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
        ->groupBy('nesa_requests.itemcode')
        ->get();
    return response()->json($data ?? []);
});


