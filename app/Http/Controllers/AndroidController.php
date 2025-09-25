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
        $user = User::findOrFail($request->userId);
        $vendorNumbers = is_array($user->selected_supplier)
            ? $user->selected_supplier
            : array_keys((array) $user->selected_supplier);
        $limit = $request->get('limit', 100);
        $offset = $request->get('offset', 0);
        $products = Product::select(
            'id',
            'itemcode',
            'description',
            'uom',
            'uom_price',
            'vendor_no'
        )
            ->whereIn('vendor_no', $vendorNumbers)
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json($products);
    }


    public function countItemCodes(Request $request)
    {
        $user = User::findOrFail($request->userId);
        return response()->json(
            Product::whereIn('vendor_no', $user->selected_supplier)->count()
        );
    }

    public function suppliers(Request $request)
    {
        $user = User::findOrFail($request->userId);

        $vendorNumbers = is_array($user->selected_supplier)
            ? $user->selected_supplier
            : array_keys((array) $user->selected_supplier);

        $limit = $request->get('limit', 100);
        $offset = $request->get('offset', 0);

        $suppliers = Supplier::select(
            'id',
            'supplier_code',
            'name'
        )
            ->whereIn('supplier_code', $vendorNumbers)
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json($suppliers);
    }




    public function countSuppliers(Request $request)
    {
        $user = User::findOrFail($request->userId);

        $vendorNumbers = is_array($user->selected_supplier)
            ? $user->selected_supplier
            : array_keys((array) $user->selected_supplier);

        $count = Supplier::whereIn('supplier_code', $vendorNumbers)->count();

        return response()->json($count);
    }


    public function getOtherStoreUploads(Request $request)
    {

        $data = NesaRequest::with([
            'user.businessUnit' => function ($q) {
                $q->select('id', 'name');
            }
        ])
            ->where('itemcode', $request->itemcode)
            ->get(['id', 'created_by']);

        $result = $data->map(function ($item) {
            return [
                'name' => optional($item->user->businessUnit)->name
            ];
        });

        if ($data->contains(fn($item) => $item->user && $item->user->id == $request->id)) {
            return response()->json([]);
        }

        return response()->json($result);
    }


    public function getProductsNotYetUploaded(Request $request)
    {
        $user = User::find($request->id);
        $selectedSuppliers = $user->selected_supplier;

        $existingItemCodes = NesaRequest::where('created_by', $user->id)
            ->pluck('itemcode')
            ->toArray();

        $query = NesaRequest::select('nesa_requests.itemcode', 'products.description', 'business_units.name as business_unit')
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('users', 'users.id', '=', 'nesa_requests.created_by')
            ->join('business_units', 'business_units.id', '=', 'users.bu')
            ->whereIn('products.vendor_no', $selectedSuppliers)
            ->groupBy('nesa_requests.itemcode', 'products.description');

        if (!empty($existingItemCodes)) {
            $query->whereNotIn('nesa_requests.itemcode', $existingItemCodes);
        }

        $data = $query->get();

        return response()->json($data);
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
        $usertype = User::where('id', $request->id)
            ->value('usertype');

        $query = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->where('status', 'pending')
            ->select('nesa_requests.*', 'products.description');

        if ($usertype == 3) {
            $query->where('created_by', $request->id)->where('status', 'pending');
        } else {
            $query->groupBy('nesa_requests.itemcode');
        }

        $data = $query->get();

        return response()->json($data);
    }
    public function getConfirmedNesaRequest(Request $request)
    {
        $usertype = User::where('id', $request->id)
            ->value('usertype');

        $query = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('users', 'users.id', '=', 'nesa_requests.created_by')
            ->join('suppliers', 'suppliers.supplier_code', 'products.vendor_no')
            ->where('is_consolidated', 0)
            ->where('coa', null)
            ->where('status', 'approved')
            ->select('nesa_requests.*', 'products.*', 'users.firstname', 'users.lastname', 'suppliers.name as vendor_name');

        if ($usertype === 3) {
            $query->where('created_by', $request->id)
                ->where('is_consolidated', 0)
                ->where('coa', null)
                ->where('status', 'approved');
        } else {
            $query->groupBy('nesa_requests.itemcode');
        }

        return response()->json($query->get());
    }

    public function getConfirmedRequestbyItemcode(Request $request)
    {
        $query = NesaRequest::join('users', 'users.id', '=', 'nesa_requests.created_by')
            ->join('business_units', 'business_units.id', '=', 'users.bu')
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('suppliers', 'suppliers.supplier_code', 'products.vendor_no')
            ->where('nesa_requests.itemcode', $request->itemcode)
            ->select('business_units.name as bu_name', 'nesa_requests.*', 'products.*', 'users.firstname', 'users.lastname', 'suppliers.name as vendor_name')
            ->get();

        return response()->json($query);
    }


    public function getPendingRequestbyItemcode(Request $request)
    {
        $data = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('users', 'users.id', '=', 'nesa_requests.created_by')
            ->join('business_units', 'business_units.id', '=', 'users.bu')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->select(
                'nesa_requests.*',
                'products.description',
                'business_units.name as bu_name',
                'suppliers.name as vendor'
            )
            ->where('nesa_requests.status', 'pending')
            ->where('nesa_requests.itemcode', $request->itemcode)
            ->get();

        return response()->json($data);

    }

    public function ViewRequestDetails(Request $request)
    {
        $nesa = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->join('users', 'users.id', '=', 'nesa_requests.created_by')
            ->join('business_units', 'business_units.id', '=', 'users.bu')
            ->where('nesa_requests.id', $request->id)
            ->select(
                'suppliers.name as vendor',
                'nesa_requests.itemcode',
                'nesa_requests.quantity',
                'nesa_requests.expiry',
                'products.description',
                'products.uom',
                'users.firstname',
                'users.lastname',
                'business_units.name as bu_name',
            )
            ->first();

        return response()->json($nesa);
    }

    public function ViewRequestDetailsFirstSubmited(Request $request)
    {
        $nesa = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->join('users', 'users.employee_id', '=', 'nesa_requests.created_by')
            ->where('nesa_requests.itemcode', $request->itemcode)
            ->orderBy('nesa_requests.created_at', 'asc')
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


    public function getCOA(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $query = NesaRequest::join('course_of_actions', 'course_of_actions.id', '=', 'nesa_requests.coa')
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->whereNot('is_consolidated', 0)
            ->whereNot('coa', null)
            ->select('nesa_requests.*', 'course_of_actions.name', 'products.*');

        if ($user['usertype'] === 3) {
            $data = $query->where('created_by', $user['employee_id'])->get();
            return response()->json($data);
        } else {
            $data = $query->get();
            return response()->json($data);
        }
    }





}
