<?php

namespace App\Http\Controllers;

use App\Models\NesaRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Str;

class AndroidController extends Controller
{
    public function user()
    {
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
            'android_password',
            'employee_id'
        )->where('android_password', '<>', null)
            ->get());
    }

    public function ItemCodes()
    {
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
    }

    public function countItemCodes()
    {
        return response()->json(
            Product::count()
        );
    }

    public function supplier()
    {
        return response()->json(Supplier::select('id', 'supplier_code', 'name')->cursorPaginate(100));
    }

    public function countSupplier()
    {
        return response()->json(
            Supplier::count()
        );
    }

    public function getStoreUploads(Request $request)
    {
        $data = NesaRequest::select('name')->where('itemcode', $request->itemcode)
            ->join('business_units', 'business_units.id', '=', 'nesa_requests.bu')
            ->get();
        return response()->json($data ?? []);
    }

    public function getAllStoreUploads(Request $request)
    {
        $user = User::find($request->id);
        $selectedSuppliers = json_decode($user->selected_supplier, true);
        $data = NesaRequest::select('nesa_requests.itemcode', 'products.description')
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->whereIn('products.vendor_no', $selectedSuppliers)
            ->groupBy('nesa_requests.itemcode')
            ->get();
        return response()->json($data ?? []);
    }

    public function uploadRequest(Request $request)
    {
        $file = $request->file('image');

        if (!$file || !$file->isValid()) {
            return response()->json(['error' => 'Invalid file upload'], 400);
        }

        try {
            $itemcode = $request->itemcode;
            $quantity = $request->quantity;
            $expirydate = $request->expirydate;
            $employee_id = $request->employee_id;
            $originalExtension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(10) . '.' . $originalExtension;
            NesaRequest::create([
                'itemcode' => $itemcode,
                'quantity' => $quantity,
                'expiry' => $expirydate,
                'created_by' => $employee_id,
                'signature' => $filename
            ]);
            $path = storage_path('app/public/signatures');

            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $file->move($path, $filename);

            return response()->json(true);
        } catch (\Exception $e) {
            \Log::error('Upload failed: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }


}
