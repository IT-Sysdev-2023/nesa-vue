<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
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


