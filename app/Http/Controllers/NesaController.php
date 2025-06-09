<?php

namespace App\Http\Controllers;

use App\Mail\SupplierEmail;
use App\Models\NesaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NesaController extends Controller
{
    //
    public function nesaList()
    {
        $nesa = NesaRequest::select(
            'nesa_requests.itemcode',
            DB::raw('MIN(nesa_requests.id) as id') // get the earliest ID per itemcode
        )
            ->groupBy('nesa_requests.itemcode')
            ->get()
            ->pluck('id');

        // Then get the full rows for those selected IDs
        $nesa = NesaRequest::select(
            'nesa_requests.itemcode',
            'nesa_no',
            'expiry',
            'suppliers.name as supname',
            'business_units.name as buname',
            'description'
        )
            ->join('suppliers', 'supplier_code', '=', 'supplier')
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('business_units', 'business_units.id', '=', 'bu')
            ->whereIn('nesa_requests.id', $nesa)
            ->get();

        return inertia('Nesa/NesaList', [
            'records' => $nesa
        ]);
    }

    public function nesaViewing($itemcode)
    {
        $nesa = NesaRequest::select(
            'nesa_requests.itemcode',
            'nesa_no',
            'expiry',
            'suppliers.name as supname',
            'business_units.name as buname',
            'description',
            'quantity'
        )
            ->join('suppliers', 'supplier_code', '=', 'supplier')
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('business_units', 'business_units.id', '=', 'bu')
            ->where('nesa_requests.itemcode', $itemcode)
            ->paginate(10)
            ->withQueryString();

        return inertia('Nesa/NesaViewing', [
            'records' => $nesa
        ]);
    }

    public function sendEmailFunction()
    {
        $sent =  Mail::to('cagasclairejoy1823@gmail.com')
            ->send(new SupplierEmail());

        if ($sent) {
            return 'Email Sent';
        }
    }
}
