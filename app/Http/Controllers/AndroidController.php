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

    public function ItemCodes(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        return response()->json(
            Product::select(
                'id',
                'id',
                'itemcode',
                'description',
                'uom',
                'uom_price',
                'vendor_no'
            )
                ->whereIn('vendor_no', $user->selected_supplier)
                ->cursorPaginate(100)
        );
    }

    public function countItemCodes(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        return response()->json(
            Product::whereIn('vendor_no', $user->selected_supplier)->count()
        );
    }

    public function supplier(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        return response()->json(Supplier::select('id', 'supplier_code', 'name')->whereIn('supplier_code', $user->selected_supplier)->cursorPaginate(100));
    }

    public function countSupplier(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        return response()->json(
            Supplier::whereIn('supplier_code', $user->selected_supplier)->count()
        );
    }

    public function getStoreUploads(Request $request)
    {
        $data = NesaRequest::select('business_units.name')->where('itemcode', $request->itemcode)
            ->join('users', 'users.employee_id', '=', 'nesa_requests.created_by')
            ->join('business_units', 'business_units.id', '=', 'users.bu')
            ->get();

        return response()->json($data);
    }

    public function getAllStoreUploads(Request $request)
    {
        $user = User::find($request->id);
        $selectedSuppliers = $user->selected_supplier;
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
            $filename = Str::random(10) . '.' . $originalExtension;
            NesaRequest::insert([
                'itemcode' => $itemcode,
                'quantity' => $quantity,
                'expiry' => $expirydate,
                'created_by' => $employee_id,
                'signature' => $filename,
                'created_at' => now(),
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

    public function isConsolidated(Request $request)
    {
        $query = NesaRequest::where('itemcode', $request->itemcode)
            ->where('created_by', $request->employee_id)
            ->first();
        return response()->json(['is_consolidated' => $query['is_consolidated'] === 1 ? true : false]);
    }

    public function getPendingRequest(Request $request)
    {
        $usertype = User::where('employee_id', $request->user)
            ->value('usertype');

        $query = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->where('status', 'pending')
            ->select('nesa_requests.*', 'products.description');

        if ($usertype === 3) {
            $query->where('created_by', $request->user)->where('status', 'pending');
        }

        $data = $query->get();

        return response()->json($data);
    }
    public function getConfirmedNesaRequest(Request $request)
    {
        $usertype = User::where('employee_id', $request->employee_id)
            ->value('usertype');

        $query = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('users', 'users.employee_id', '=', 'nesa_requests.created_by')
            ->join('suppliers', 'suppliers.supplier_code', 'products.vendor_no')
            ->where('is_consolidated', 0)
            ->where('coa', null)
            ->where('status', 'approved')
            ->select('nesa_requests.*', 'products.*', 'users.firstname', 'users.lastname', 'suppliers.name as vendor_name');

        if ($usertype === 3) {
            $query->where('created_by', $request->employee_id)
                ->where('is_consolidated', 0)
                ->where('coa', null)
                ->where('status', 'approved');
        }

        $data = $query->get();

        return response()->json($data);
    }

    public function ViewRequestDetails(Request $request)
    {
        $nesa = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->join('users', 'users.employee_id', '=', 'nesa_requests.created_by')
            ->where('nesa_requests.itemcode', $request->itemcode)
            ->select(
                'suppliers.name as vendor',
                'nesa_requests.itemcode',
                'nesa_requests.quantity',
                'nesa_requests.expiry',
                'products.description',
                'products.uom',
                'users.firstname',
                'users.lastname'
            )
            ->first();

        return response()->json($nesa);
    }


    public function ApproveRequest(Request $request)
    {
        $filename = $request->filename;
        $file = $request->file('image');

        if (!$file || !$file->isValid()) {
            return response()->json(['error' => 'Invalid file upload'], 400);
        }

        try {
            NesaRequest::where('id', $request->id)->update([
                'ric_section_head_signature' => $filename,
                'ric_section_head' => $request->employee_id,
                'status' => $request->status,
                'date_approved' => now()
            ]);

            $path = storage_path('app/public/signatures');
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $file->move($path, $filename);
            return response()->json(true);

        } catch (\Exception $e) {
            return response()->json(false);
        }
    }

    public function findItemCode(Request $request)
    {
        $data = Product::where('itemcode', $request->itemcode)
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->select('products.*', 'suppliers.name as vendor')
            ->first();
        return response()->json($data);
    }

    public function updateRequest(Request $request)
    {

        $update = NesaRequest::where('id', $request->id)->update([
            $request->field => $request->newValue
        ]);

        return response()->json($update);
    }




}
