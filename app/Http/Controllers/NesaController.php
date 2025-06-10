<?php

namespace App\Http\Controllers;

use App\Mail\SupplierEmail;
use App\Models\NesaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NesaController extends Controller
{
    //
    public function nesaList(Request $request)
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
            'nesa_requests.itemcode as item_code',
            'nesa_no',
            'nesa_requests.created_at',
            'suppliers.name as supname',
            'business_units.name as buname',
            'description',
        )
            ->join('suppliers', 'supplier_code', '=', 'supplier')
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('business_units', 'business_units.id', '=', 'bu')
            ->when($request->search, function ($query) use ($request) {
                $query->where('suppliers.name', 'like', '%' . $request->search . '%');
            })->where('nesa_requests.is_consolidated', 0)
            ->whereIn('nesa_requests.id', $nesa)
            ->paginate(10)
            ->withQueryString();

        $nesa->transform(function ($item) {
            $item->nesa_date = $item->created_at->toFormattedDateString();
            return $item;
        });

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
            'quantity',
            'uom'
        )
            ->join('suppliers', 'supplier_code', '=', 'supplier')
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('business_units', 'business_units.id', '=', 'bu')
            ->where('nesa_requests.itemcode', $itemcode)
            ->get();

        $nesa->map(function ($item) {
            $item->expiry = Date::parse($item->expiry)->toFormattedDateString();
            $item->quantity = $item->quantity . ' pcs';
            return $item;
        });

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

    public function consolidateProcess() {}
}
