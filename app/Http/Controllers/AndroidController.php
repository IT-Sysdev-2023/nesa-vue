<?php

namespace App\Http\Controllers;

use App\Models\BORequest;
use App\Models\NesaRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Review;
use App\Models\DownloadLog;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; // Add this import
use Illuminate\Support\Facades\Validator; // Add this if using validator
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class AndroidController extends Controller
{

    public function checkServer(): JsonResponse
    {
        try {
            // Optional DB check
            DB::connection()->getPdo();

            return response()->json([
                'status' => 'online',
                'database' => 'connected',
                'time' => now()->toDateTimeString()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'online',
                'database' => 'error',
                'time' => now()->toDateTimeString()
            ], 500);
        }
    }
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
            'is_online',
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

        $limit = (int) $request->get('limit', 500);
        $lastId = (int) $request->get('last_id', 0);

        $products = Product::select(
            'id',
            'itemcode',
            'description',
            'uom',
            'uom_price',
            'vendor_no'
        )
            ->whereIn('vendor_no', $vendorNumbers)
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        // RESPONSE IS STILL A PLAIN ARRAY
        return response()->json($products);
    }


    public function countItemCodes(Request $request)
    {
        $userId = (int) $request->input('userId');

        if ($userId <= 0) {
            return response()->json(['total' => 0]);
        }

        $cacheKey = "product_count:user:$userId";

        $count = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($userId) {

            $suppliersJson = DB::table('users')
                ->where('id', $userId)
                ->value('selected_supplier');

            if (empty($suppliersJson)) {
                return 0;
            }

            $suppliers = json_decode($suppliersJson, true);

            if (!is_array($suppliers) || empty($suppliers)) {
                return 0;
            }

            // ✅ Normalize suppliers
            $supplierIds = collect($suppliers)
                ->map(function ($item) {
                    if (is_array($item) && isset($item['vendor_no'])) {
                        return $item['vendor_no'];
                    }
                    return $item;
                })
                ->filter()
                ->values()
                ->toArray();

            if (empty($supplierIds)) {
                return 0;
            }

            return DB::table('products')
                ->whereIn('vendor_no', $supplierIds)
                ->count();
        });

        return response()->json(['total' => $count]);
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
        // Validate immediately
        $validated = $request->validate([
            'userId' => 'required|integer|exists:users,id'
        ]);

        $cacheKey = "supplier_count:user:{$validated['userId']}";

        // Cache for 10 minutes
        $count = Cache::remember($cacheKey, 600, function () use ($validated) {
            // Get only what we need
            $user = User::select('selected_supplier')
                ->findOrFail($validated['userId']);

            $vendorNumbers = $user->selected_supplier ?? [];

            if (empty($vendorNumbers)) {
                return 0;
            }

            // Ensure vendorNumbers is an array
            if (!is_array($vendorNumbers)) {
                $vendorNumbers = is_object($vendorNumbers)
                    ? array_keys((array) $vendorNumbers)
                    : json_decode($vendorNumbers, true) ?? [];
            }

            if (empty($vendorNumbers)) {
                return 0;
            }

            // Use raw query for maximum speed
            return DB::table('suppliers')
                ->whereIn('supplier_code', $vendorNumbers)
                ->count('id');
        });

        return response()->json([
            'total' => $count  // Changed from 'count' to 'total' to match countItemCodes
        ]);
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

            // Generate unique tracking number
            $trackingNumber = $this->generateUniqueTrackingNumber();

            NesaRequest::insert([
                'itemcode' => $itemcode,
                'quantity' => $quantity,
                'expiry' => $expirydate,
                'created_by' => $employee_id,
                'signature' => $filename,
                'tracking_number' => $trackingNumber,
                'created_at' => now(),
            ]);

            $path = storage_path('app/public/signatures');

            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $file->move($path, $filename);

            return response()->json(['success' => true, 'tracking_number' => $trackingNumber]);
        } catch (\Exception $e) {
            \Log::error('Upload failed: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * Generate a random alphanumeric string and ensure uniqueness
     */
    private function generateUniqueTrackingNumber($length = 8)
    {
        do {
            // Generate random string (letters + numbers)
            $trackingNumber = strtoupper(Str::random($length)); // e.g., 8 chars
            $exists = NesaRequest::where('tracking_number', $trackingNumber)->exists();
        } while ($exists); // repeat if duplicate

        return $trackingNumber;
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

    public function getConfirmedNesaRequestSummed(Request $request)
    {
        $query = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('users', 'users.id', '=', 'nesa_requests.created_by')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->whereNull('nesa_requests.coa')
            ->where('nesa_requests.status', 'approved')
            ->selectRaw('
            nesa_requests.itemcode,
            nesa_requests.status,
            SUM(nesa_requests.quantity) AS quantity,
            products.description,
            products.vendor_no,
            products.uom,
            suppliers.name AS vendor_name
        ')
            ->groupBy(
                'nesa_requests.itemcode',
                'products.description',
                'products.vendor_no',
                'suppliers.name'
            );

        return response()->json($query->get());
    }

    public function getCancelledNesaRequest(Request $request)
    {
        $usertype = User::where('id', $request->id)->value('usertype');

        $query = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('users', 'users.id', '=', 'nesa_requests.created_by')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->where('nesa_requests.is_consolidated', 0)
            ->whereNull('nesa_requests.coa')
            ->where('nesa_requests.status', 'cancelled')
            ->select(
                'nesa_requests.*',
                'products.*',
                'users.firstname',
                'users.lastname',
                'suppliers.name as vendor_name'
            );

        // 🔹 Only restrict results if usertype == 3
        if ($usertype == 3) {
            $query->where('nesa_requests.created_by', $request->id);
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

    public function getConfirmedRequestPerItemcode(Request $request)
    {
        $itemcode = $request->input('itemcode'); // fetch POST field

        $query = NesaRequest::leftJoin('users', 'users.id', '=', 'nesa_requests.created_by')
            ->leftJoin('business_units', 'business_units.id', '=', 'users.bu')
            ->where('nesa_requests.itemcode', $itemcode)
            ->where('nesa_requests.status', 'approved')
            ->where(function ($q) {
                $q->where('nesa_requests.is_consolidated', 0)
                    ->orWhereNull('nesa_requests.is_consolidated');
            })
            ->select(
                'nesa_requests.*',
                'users.firstname',
                'users.lastname',
                'business_units.name as business_unit_name'
            )
            ->get();

        return response()->json($query);
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
                'nesa_requests.created_at',
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

    public function getPhoto(Request $request)
    {
        // Get only the 'photo' field from the user with the given ID
        $photo = User::where('id', $request->id)->value('photo');

        return response()->json([
            'photo' => $photo
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = User::findorfail($request->id);

        if ($user->android_password != $request->password) {
            return response()->json([
                'message' => 'Old password does not match'
            ]);
        }

        $user->android_password = $request->new_password;
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    public function countAllSuppliers()
    {
        $count = Supplier::count();

        return response()->json([
            'total' => $count
        ]);
    }

    public function countAllProducts()
    {
        $count = Product::count();

        return response()->json([
            'total' => $count
        ]);
    }

    public function countAllNesaRequest()
    {
        $count = NesaRequest::where('status', 'pending')->count();

        return response()->json([
            'total' => $count
        ]);
    }


    public function getProducts(Request $request)
    {
        $limit = $request->query('limit', 50);   // default 50
        $offset = $request->query('offset', 0);   // default 0

        $products = Product::select('id', 'itemcode', 'description', 'uom', 'uom_price', 'vendor_no')
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json($products); // plain array
    }


    public function getSuppliers(Request $request)
    {
        $limit = $request->query('limit', 50);   // default 50
        $offset = $request->query('offset', 0);   // default 0

        $suppliers = Supplier::select('id', 'supplier_code', 'name') // only these fields
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json($suppliers); // return as plain array
    }

    public function countAllApprovedRequest(Request $request)
    {
        $user = User::findOrFail($request->id);

        $query = NesaRequest::where('status', 'approved');

        // If normal user / section head
        if ((int) $user->usertype === 3) {
            $query->where('created_by', $user->id); // ✅ consistent ID usage
        }

        return response()->json([
            'total' => $query->count()
        ]);
    }

    public function countAllCancelledRequest(Request $request)
    {
        $user = User::findOrFail($request->id);

        $query = NesaRequest::where('status', 'cancelled');

        // If Section Head / normal user
        if ((int) $user->usertype === 3) {
            $query->where('created_by', $user->id); // ✅ use user ID consistently
        }

        return response()->json([
            'total' => $query->count()
        ]);
    }


    public function exportProductsJson(Request $request)
    {
        // Fetch all products
        $products = Product::select('id', 'itemcode', 'description', 'uom', 'uom_price', 'vendor_no')
            ->get();

        // Convert to JSON
        $jsonData = $products->toJson(JSON_PRETTY_PRINT);

        // Save to a temporary file in storage/app
        $filePath = storage_path('app/products.json');
        file_put_contents($filePath, $jsonData);

        // Return the file as a download
        return response()->download($filePath, 'products.json')->deleteFileAfterSend(true);
    }

    public function addReview(Request $request)
    {
        $review = Review::create([
            'user_id' => $request->user_id,
            'score' => $request->score,
            'reviews' => $request->reviews,
        ]);

        return response()->json([
            'message' => 'Review added successfully'
        ]);
    }

    public function getTodayNoti(Request $request)
    {
        $userId = $request->id;

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $notifications = Notifications::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where(function ($query) use ($userId) {
                $query->where('type', 'all')
                    ->orWhere('type', $userId);
            })
            ->get()
            ->map(function ($noti) {
                return [
                    'id' => $noti->id,
                    'title' => $noti->title,
                    'message' => $noti->message,
                    'created_at' => $noti->created_at->format('Y-m-d'), // date only
                    'type' => $noti->type,
                    'is_read' => $noti->is_read
                ];
            });

        return response()->json($notifications);
    }


    // Get yesterday's notifications
    public function getYesterdayNoti(Request $request)
    {
        $userId = $request->id;
        $yesterdayStart = now()->subDay()->startOfDay();
        $yesterdayEnd = now()->subDay()->endOfDay();

        $notifications = Notifications::whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])
            ->where(function ($query) use ($userId) {
                $query->where('type', 'all')
                    ->orWhere('type', $userId);
            })
            ->get()
            ->map(function ($noti) {
                return [
                    'id' => $noti->id,
                    'title' => $noti->title,
                    'message' => $noti->message,
                    'type' => $noti->type,
                    'is_read' => $noti->is_read,
                    'created_at' => $noti->created_at->format('Y-m-d') // date only
                ];
            });

        return response()->json($notifications);
    }

    // Last week notifications
    public function getPastNoti(Request $request)
    {
        $userId = $request->id;

        // Start date: as far back as you want (e.g., 2026-01-01)
        $startDate = '2026-01-01';

        // End date: last week (exclude today and yesterday)
        $endDate = now()->subDays(2)->endOfDay();

        $notifications = Notifications::whereBetween('created_at', [$startDate, $endDate])
            ->where(function ($query) use ($userId) {
                $query->where('type', 'all')
                    ->orWhere('type', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($noti) {
                return [
                    'id' => $noti->id,
                    'title' => $noti->title,
                    'message' => $noti->message,
                    'type' => $noti->type,
                    'is_read' => $noti->is_read,
                    'created_at' => $noti->created_at->format('Y-m-d')
                ];
            });

        return response()->json($notifications);
    }

    public function updateNotification(Request $request)
    {
        Notifications::where('id', $request->id)
            ->update(['is_read' => 1]);

        return response()->json(['message' => 'Success']);
    }

    public function addNotification(Request $request)
    {

        $notification = Notifications::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
        ]);

        return response()->json([
            'success' => true,
            'notification' => [
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'created_at' => $notification->created_at->format('Y-MM-dd') // date only
            ]
        ]);
    }

    public function getTotalUnreadNotification(Request $request)
    {
        $userId = $request->userId;

        $totalUnread = Notifications::where('type', $userId)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'total_unread' => $totalUnread
        ]);
    }

    public function addLog(Request $request)
    {
        $log = DownloadLog::create([
            'userId' => $request->userId,
            'created_at' => now(),
            'type' => $request->type,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Download log added successfully',
            'data' => $log
        ]);
    }

    public function listLogs(Request $request)
    {
        $userId = $request->userId;
        $query = DownloadLog::query();

        if ($userId) {
            $query->where('userId', $userId);
        }

        $logs = $query->orderBy('id', 'desc')->get();

        return response()->json([$logs]);
    }

    public function getSelectedNesa(Request $request)
    {
        $itemcode = $request->input('itemcode');
        $createdBy = $request->input('created_by');
        $today = Carbon::today()->toDateString();

        $results = NesaRequest::where('itemcode', $itemcode)
            ->where('created_by', $createdBy)
            ->where('status', 'approved')
            ->whereDate('expiry', '>=', $today) // Only fetch items not yet expired
            ->get();

        return response()->json($results);
    }

    public function getAllCourseAction()
    {
        // Fetch products from database
        $products = DB::table('course_of_actions')->get()
            ->map(function ($item) {
                return (array) $item; // convert stdClass objects to associative arrays
            })
            ->toArray();

        // Return as JSON response
        return response()->json($products);
    }

    public function getConfirmedNesaRequestWithCOA(Request $request)
    {
        $usertype = User::where('id', $request->id)->value('usertype');

        $query = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('users', 'users.id', '=', 'nesa_requests.created_by')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->join('course_of_actions', 'course_of_actions.id', '=', 'nesa_requests.coa')
            ->where('nesa_requests.is_consolidated', 0)
            ->where('nesa_requests.is_done', 0)
            ->whereNotNull('nesa_requests.coa')
            ->where('nesa_requests.status', 'approved')
            ->select(
                'nesa_requests.id',
                'nesa_requests.nesa_no',
                'nesa_requests.nesa_id',
                'nesa_requests.itemcode',
                'nesa_requests.quantity',
                'nesa_requests.is_consolidated',
                'course_of_actions.name as coa', // ✅ REPLACED HERE
                'nesa_requests.expiry',
                'nesa_requests.signature',
                'nesa_requests.created_by',
                'nesa_requests.created_at',
                'nesa_requests.updated_at',
                'nesa_requests.ric_section_head',
                'nesa_requests.ric_section_head_signature',
                'nesa_requests.status',
                'nesa_requests.date_approved',
                'nesa_requests.tracking_number',
                'nesa_requests.is_done',
                'products.description',
                'products.uom',
                'products.uom_price',
                'products.vendor_no',
                'users.firstname',
                'users.lastname',
                'suppliers.name as vendor_name'
            );

        if ($usertype === 3) {
            $query->where('nesa_requests.created_by', $request->id);
        } else {
            $query->groupBy('nesa_requests.itemcode');
        }

        return response()->json($query->get());
    }

    public function getConfirmedNesaRequestWithCOAIsDone(Request $request)
    {
        $usertype = User::where('id', $request->id)->value('usertype');

        $query = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('users', 'users.id', '=', 'nesa_requests.created_by')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->join('course_of_actions', 'course_of_actions.id', '=', 'nesa_requests.coa')
            ->where('nesa_requests.is_consolidated', 0)
            ->where('nesa_requests.is_done', 1)
            ->whereNotNull('nesa_requests.coa')
            ->where('nesa_requests.status', 'approved')
            ->select(
                'nesa_requests.id',
                'nesa_requests.nesa_no',
                'nesa_requests.nesa_id',
                'nesa_requests.itemcode',
                'nesa_requests.quantity',
                'nesa_requests.is_consolidated',
                'course_of_actions.name as coa', // ✅ REPLACED HERE
                'nesa_requests.expiry',
                'nesa_requests.signature',
                'nesa_requests.created_by',
                'nesa_requests.created_at',
                'nesa_requests.updated_at',
                'nesa_requests.ric_section_head',
                'nesa_requests.ric_section_head_signature',
                'nesa_requests.status',
                'nesa_requests.date_approved',
                'nesa_requests.tracking_number',
                'nesa_requests.is_done',
                'products.description',
                'products.uom',
                'products.uom_price',
                'products.vendor_no',
                'users.firstname',
                'users.lastname',
                'suppliers.name as vendor_name'
            );

        if ($usertype === 3) {
            $query->where('nesa_requests.created_by', $request->id);
        } else {
            $query->groupBy('nesa_requests.itemcode');
        }

        return response()->json($query->get());
    }

    public function getInbox(Request $request)
    {
        $userId = $request->input('id');

        // Get the latest message IDs per conversation
        $latestMessageIds = DB::table('messages')
            ->select(DB::raw('MAX(id) as id'))
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('recipient_id', $userId);
            })
            ->groupByRaw('LEAST(sender_id, recipient_id), GREATEST(sender_id, recipient_id)');

        // Join messages with users
        $inbox = DB::table('messages as m')
            ->join('users as u', function ($join) use ($userId) {
                $join->on('u.id', '=', DB::raw(
                    "CASE 
                    WHEN m.sender_id = {$userId} THEN m.recipient_id 
                    ELSE m.sender_id 
                 END"
                ));
            })
            ->whereIn('m.id', $latestMessageIds)
            ->select(
                'u.id as user_id',
                'u.firstname',
                'u.lastname',
                'u.is_online', // add the datetime column
                DB::raw("
                CASE 
                    WHEN m.sender_id = {$userId} 
                    THEN CONCAT('me: ', m.message)
                    ELSE m.message
                END AS message
            "),
                'm.read',
                'm.created_at'
            )
            ->orderBy('m.created_at', 'desc')
            ->get();

        // Decrypt messages and calculate online status
        $inbox = $inbox->map(function ($msg) {
            try {
                // Decrypt message
                if (str_starts_with($msg->message, 'me: ')) {
                    $encryptedPart = substr($msg->message, 4); // remove "me: "
                    $msg->message = 'me: ' . Crypt::decryptString($encryptedPart);
                } else {
                    $msg->message = Crypt::decryptString($msg->message);
                }
            } catch (\Exception $e) {
                $msg->message = '[Cannot decrypt]';
            }

            // Calculate is_online: true if within last 5 minutes
            if ($msg->is_online) {
                $lastOnline = Carbon::parse($msg->is_online);
                $msg->is_online = $lastOnline->diffInMinutes(now()) <= 5 ? true : false;
            } else {
                $msg->is_online = false;
            }

            return $msg;
        });

        return response()->json($inbox);
    }

    public function messages(Request $request)
    {
        $messages = DB::table('messages')
            ->where(function ($q) use ($request) {
                $q->where('sender_id', $request->user_id)
                    ->where('recipient_id', $request->recipient_id);
            })
            ->orWhere(function ($q) use ($request) {
                $q->where('sender_id', $request->recipient_id)
                    ->where('recipient_id', $request->user_id);
            })
            ->orderBy('created_at')
            ->get();

        // Decrypt each message
        $messages = $messages->map(function ($msg) {
            $msg->message = Crypt::decryptString($msg->message);
            return $msg;
        });

        return $messages;
    }

    public function send(Request $request)
    {
        // Encrypt the message
        $encryptedMessage = Crypt::encryptString($request->message);

        DB::table('messages')->insert([
            'sender_id' => $request->sender_id,
            'recipient_id' => $request->recipient_id,
            'message' => $encryptedMessage,
            'created_at' => now()
        ]);

        return response()->json(['status' => 'sent']);
    }

    // public function getLatestNesaRequest()
    // {
    //     $data = DB::table('nesa_requests as nr')
    //         ->join('users as u', 'nr.created_by', '=', 'u.id') // join users table
    //         ->join('business_units as bu', 'u.bu', '=', 'bu.id') // join business_units
    //         ->where('nr.is_consolidated', 0)
    //         ->where('nr.created_at', '>=', Carbon::now()->subDays(5))
    //         ->where('nr.status', 'pending')
    //         ->orderBy('nr.created_at', 'desc')
    //         ->select(
    //             'nr.*', // all nesa_requests columns
    //             'bu.abbrevation as business_unit_name' // add the abbreviation
    //         )
    //         ->get();

    //     return response()->json($data);
    // }

    public function getLatestNesaRequest(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);
        $user_id = $request->input('user_id', 0);

        $query = DB::table('nesa_requests as nr')
            ->join('users as u', 'nr.created_by', '=', 'u.id')
            ->join('business_units as bu', 'u.bu', '=', 'bu.id')
            ->where('nr.is_consolidated', 0)
            ->where('nr.created_at', '>=', Carbon::now()->subDays(5))
            ->where('nr.status', 'pending')
            ->where('nr.created_by', '!=', $user_id);
    
        $data = $query->orderBy('nr.created_at', 'desc')
            ->select('nr.*', 'bu.abbrevation as business_unit_name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json($data);
    }



    public function updateCourseAction(Request $request)
    {
        NesaRequest::where('id', $request->id)
            ->update(['is_done' => 1]);

        return response()->json(['message' => 'Success']);
    }

    public function countRequestsByStatusNESA(Request $request)
    {
        // Use user_id from request
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'error' => 'user_id is required'
            ], 400);
        }

        // Count requests by status
        $pendingCount = NesaRequest::where('created_by', $userId)
            ->where('status', 'pending')
            ->count();

        $approvedCount = NesaRequest::where('created_by', $userId)
            ->where('status', 'approved')
            ->count();

        $cancelledCount = NesaRequest::where('created_by', $userId)
            ->where('status', 'cancelled')
            ->count();

        return response()->json([
            'user_id' => $userId,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
            'cancelled' => $cancelledCount
        ]);
    }

    public function check(Request $request)
    {
        // Validate the incoming app version
        $request->validate([
            'version' => 'required|integer' // your app's current version code
        ]);

        $currentVersion = $request->version;

        // Get the latest version from DB
        $latest = DB::table('app_update')
            ->orderByDesc('version') // highest version first
            ->first();

        if (!$latest) {
            // No updates found
            return response()->json([
                'updateAvailable' => false
            ]);
        }

        // Compare current version with latest
        if ($currentVersion < $latest->version) {
            // There is an update
            return response()->json([
                'updateAvailable' => true,
                'version' => $latest->version,
                'versionName' => $latest->versionName,
                'updateMessage' => $latest->updateMessage,
                'downloadUrl' => $latest->downloadUrl,
                'forceUpdate' => (bool) $latest->forceUpdate
            ]);
        }

        // Current version is up-to-date
        return response()->json([
            'updateAvailable' => false
        ]);
    }

    public function getUserTypeName(Request $request)
    {
        // Fetch name by id using Query Builder
        $name = DB::table('user_types')
            ->where('id', $request->id)
            ->value('name'); // get single column value

        return response()->json([
            'name' => $name
        ]);
    }

    public function getPendingRequestV2(Request $request)
    {
        // Get the user type
        $usertype = User::where('id', $request->id)->value('usertype');

        // Base query
        $rows = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->join('users', 'users.id', '=', 'nesa_requests.created_by') // to get BU
            ->join('business_units', 'business_units.id', '=', 'users.bu') // join BU table
            ->where('nesa_requests.status', 'pending')
            ->when($usertype == 3, function ($q) use ($request) {
                $q->where('nesa_requests.created_by', $request->id);
            })
            ->when($request->filled('supplier_name'), function ($q) use ($request) {
                $q->where('suppliers.name', 'like', "%{$request->supplier_name}%");
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $q->whereDate('nesa_requests.created_at', $request->date);
            })
            ->when($request->filled('date_from') && $request->filled('date_to'), function ($q) use ($request) {
                $q->whereBetween('nesa_requests.created_at', [$request->date_from, $request->date_to]);
            })
            ->select(
                'nesa_requests.nesa_no',
                'nesa_requests.created_at',
                'suppliers.name as supplier_name',
                'business_units.name as location', // added location
                'nesa_requests.itemcode',
                'products.description',
                'products.uom',
                'nesa_requests.quantity',
                'nesa_requests.expiry'
            )
            ->orderBy('nesa_requests.nesa_no')
            ->get();

        // Group by NESA number + Supplier
        $result = $rows->groupBy(function ($item) {
            return $item->nesa_no . '|' . $item->supplier_name;
        })->map(function ($items) {
            return [
                'nesa_no' => $items->first()->nesa_no,
                'date' => \Carbon\Carbon::parse($items->first()->created_at)->format('Y-m-d'),
                'supplier_name' => $items->first()->supplier_name,
                'location' => $items->first()->location, // added location
                'data' => $items->map(function ($item) {
                    return [
                        'item_code' => $item->itemcode,
                        'item_description' => $item->description,
                        'item_quantity' => $item->quantity,
                        'item_expiry_date' => $item->expiry,
                        'item_uom' => $item->uom,
                    ];
                })->values()
            ];
        })->values();

        return response()->json($result);
    }

    public function getApprovedRequestV2(Request $request)
    {
        // Get the user type
        $usertype = User::where('id', $request->id)->value('usertype');

        // Base query
        $rows = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->leftJoin('users as ric_head', 'ric_head.id', '=', 'nesa_requests.ric_section_head') // Ric section head
            ->leftJoin('users as creator', 'creator.id', '=', 'nesa_requests.created_by') // Created_by user to get BU
            ->leftJoin('business_units', 'business_units.id', '=', 'creator.bu') // <-- changed table name
            ->where('nesa_requests.status', 'approved')
            ->when($usertype == 3, function ($q) use ($request) {
                $q->where('nesa_requests.created_by', $request->id);
            })
            ->when($request->filled('supplier_name'), function ($q) use ($request) {
                $q->where('suppliers.name', 'like', "%{$request->supplier_name}%");
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $q->whereDate('nesa_requests.created_at', $request->date);
            })
            ->when($request->filled('date_from') && $request->filled('date_to'), function ($q) use ($request) {
                $q->whereBetween('nesa_requests.created_at', [$request->date_from, $request->date_to]);
            })
            ->select(
                'nesa_requests.nesa_no',
                'nesa_requests.created_at',
                'suppliers.name as supplier_name',
                'business_units.name as location', // updated table
                'nesa_requests.itemcode',
                'products.description',
                'products.uom',
                'nesa_requests.quantity',
                'nesa_requests.expiry',
                'ric_head.firstname as ric_firstname',
                'ric_head.middlename as ric_middlename',
                'ric_head.lastname as ric_lastname',
                'ric_head.name_extention as ric_name_extention',
                'nesa_requests.ric_section_head_signature'
            )
            ->orderBy('nesa_requests.nesa_no')
            ->get();

        // Group by NESA number + Supplier
        $result = $rows->groupBy(function ($item) {
            return $item->nesa_no . '|' . $item->supplier_name;
        })->map(function ($items) {
            return [
                'nesa_no' => $items->first()->nesa_no,
                'date' => \Carbon\Carbon::parse($items->first()->created_at)->format('Y-m-d'),
                'supplier_name' => $items->first()->supplier_name,
                'location' => $items->first()->location, // location added
                'data' => $items->map(function ($item) {
                    return [
                        'item_code' => $item->itemcode,
                        'item_description' => $item->description,
                        'item_quantity' => $item->quantity,
                        'item_expiry_date' => $item->expiry,
                        'item_uom' => $item->uom,
                        'item_ric_section_head' => [
                            'firstname' => $item->ric_firstname,
                            'middlename' => $item->ric_middlename,
                            'lastname' => $item->ric_lastname,
                            'name_extention' => $item->ric_name_extention,
                        ],
                        'item_ric_section_head_signature' => $item->ric_section_head_signature,
                    ];
                })->values()
            ];
        })->values();

        return response()->json($result);
    }

    public function updateIsOnline(Request $request)
    {
        User::where('id', $request->id)
            ->update([
                'is_online' => now() // current date & time
            ]);

        return response()->noContent(); // 204
    }

    public function getBusinessUnits()
    {
        $business_units = DB::table('business_units')->get(); // Fetch all rows from 'users' table
        return response()->json($business_units);    // Return as JSON
    }


    // #############################
    // #######  BAD ORDER  #########
    // #############################

    public function uploadBORequest(Request $request)
    {

        try {
            $nesa_id = $request->nesa_id;
            $itemcode = $request->itemcode;
            $quantity = $request->quantity;
            $created_by = $request->created_by;
            $reason = $request->reason;
            $bo_policy = $request->bo_policy;

            // ================= SIGNATURE =================
            $signature = $request->file('signature');
            $signatureName = Str::random(10) . '.' . $signature->getClientOriginalExtension();

            $signaturePath = storage_path('app/public/signatures');
            if (!is_dir($signaturePath)) {
                mkdir($signaturePath, 0755, true);
            }
            $signature->move($signaturePath, $signatureName);

            // ================= MULTIPLE IMAGES =================
            $imageNames = [];
            $imagePath = storage_path('app/public/bo/images');

            if (!is_dir($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $img) {
                    $name = Str::random(10) . '.' . $img->getClientOriginalExtension();
                    $img->move($imagePath, $name);
                    $imageNames[] = $name;
                }
            }

            // ✅ STORE AS JSON (not comma string)
            $imagesJson = json_encode($imageNames);

            // ================= SAVE TO DB =================
            BORequest::create([
                'nesa_id' => $nesa_id,
                'itemcode' => $itemcode,
                'quantity' => $quantity,
                'created_by' => $created_by,
                'reason' => $reason,
                'bo_policy' => $bo_policy,
                'signature' => $signatureName,
                'images' => $imagesJson, // ✅ JSON
                'created_at' => now(),
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Upload failed: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getBORequestsByCreatedBy(Request $request)
    {
        try {
            $created_by = $request->created_by;

            $data = BORequest::where('created_by', $created_by)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([$data]);

        } catch (\Exception $e) {
            \Log::error('Fetch failed: ' . $e->getMessage());

            return response()->json([
                $e->getMessage()
            ], 500);
        }
    }

    public function countRequestsByStatus(Request $request)
    {
        // Use user_id from request
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'error' => 'user_id is required'
            ], 400);
        }

        // Count requests by status
        $pendingCount = BORequest::where('created_by', $userId)
            ->where('status', 'pending')
            ->count();

        $approvedCount = BORequest::where('created_by', $userId)
            ->where('status', 'approved')
            ->count();

        $cancelledCount = BORequest::where('created_by', $userId)
            ->where('status', 'cancelled')
            ->count();

        return response()->json([
            'user_id' => $userId,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
            'cancelled' => $cancelledCount
        ]);
    }

    public function getAllBORequestByCreatedBy(Request $request)
    {
        try {
            $createdBy = $request->created_by;

            if (!$createdBy) {
                return response()->json([
                    'success' => false,
                    'message' => 'created_by is required'
                ], 400);
            }

            // Join BORequest -> Products -> Suppliers
            $data = BORequest::select(
                'bo_requests.id',
                'bo_requests.nesa_id',
                'bo_requests.itemcode',
                'products.description as product_description',
                'products.vendor_no',
                'suppliers.name as supplier_name',
                'bo_requests.quantity',
                'bo_requests.reason',
                'bo_requests.bo_policy',
                'bo_requests.signature',
                'bo_requests.images',
                'bo_requests.created_by',
                'bo_requests.created_at'
            )
                ->join('products', 'products.itemcode', '=', 'bo_requests.itemcode')
                ->leftJoin('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
                ->where('bo_requests.created_by', $createdBy)
                ->where('bo_requests.status', 'pending')
                ->orderBy('bo_requests.created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nesa_id' => $item->nesa_id,
                        'itemcode' => $item->itemcode,
                        'product_name' => $item->product_description,
                        'supplier_name' => $item->supplier_name ?? 'N/A',
                        'quantity' => $item->quantity,
                        'reason' => $item->reason,
                        'bo_policy' => $item->bo_policy,
                        'signature' => $item->signature,
                        'images' => json_decode($item->images, true),
                        'created_by' => $item->created_by,
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'count' => $data->count(),
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Get BO Request failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch BO requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function countAllRequestsByStatus(Request $request)
    {
        // Use user_id from request
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'error' => 'user_id is required'
            ], 400);
        }

        // Count requests by status
        $pendingNESACount = NesaRequest::where('created_by', $userId)
            ->where('status', 'pending')
            ->count();

        $pendingBOCount = BORequest::where('created_by', $userId)
            ->where('status', 'pending')
            ->count();

        $approvedNESACount = NesaRequest::where('created_by', $userId)
            ->where('status', 'approved')
            ->count();

        $approvedBOCount = BORequest::where('created_by', $userId)
            ->where('status', 'approved')
            ->count();

        return response()->json([
            'user_id' => $userId,
            'pendingNESA' => $pendingNESACount,
            'pendingBO' => $pendingBOCount,
            'approvedNESA' => $approvedNESACount,
            'approvedBO' => $approvedBOCount
        ]);
    }

}
