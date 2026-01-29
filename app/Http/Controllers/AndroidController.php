<?php

namespace App\Http\Controllers;

use App\Models\NesaRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Review;
use App\Models\DownloadLog;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; // Add this import
use Illuminate\Support\Facades\Validator; // Add this if using validator
use Carbon\Carbon;

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
            ->where('nesa_requests.is_consolidated', 0)
            ->whereNull('nesa_requests.coa')
            ->where('nesa_requests.status', 'approved')
            ->selectRaw('
            nesa_requests.itemcode,
            nesa_requests.status,
            SUM(nesa_requests.quantity) AS quantity,
            products.description,
            products.vendor_no,
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
        $query = NesaRequest::where('itemcode', $request->itemcode)
            ->where('status', 'approved')
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
            ->where('status', "approved")
            ->whereDate('expiry', '<=', Carbon::today())
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
        $userId = $request->input('id'); // Current user

        // Subquery: latest message per conversation
        $latestMessageIds = DB::table('messages')
            ->select(DB::raw('MAX(id) as id'))
            ->where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->groupByRaw('LEAST(sender_id, recipient_id), GREATEST(sender_id, recipient_id)');

        // Main query: join with users
        $inbox = DB::table('messages as m')
            ->join('users as u', function ($join) use ($userId) {
                $join->on('u.id', '=', DB::raw('CASE WHEN m.sender_id = ' . $userId . ' THEN m.recipient_id ELSE m.sender_id END'));
            })
            ->whereIn('m.id', $latestMessageIds)
            ->select(
                'u.id as user_id',
                'u.firstname',
                'u.lastname',
                'm.message',
                'm.read',
                'm.created_at'
            )
            ->orderBy('m.created_at', 'desc')
            ->get();

        return response()->json($inbox);
    }



    public function messages(Request $request)
    {
        return DB::table('messages')
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
    }

    public function send(Request $request)
    {
        DB::table('messages')->insert([
            'sender_id' => $request->sender_id,
            'recipient_id' => $request->recipient_id,
            'message' => $request->message,
            'created_at' => now()
        ]);

        return response()->json(['status' => 'sent']);
    }

    public function getLatestNesaRequest()
    {
        $data = NesaRequest::where('is_consolidated', 0)
            ->where('created_at', '>=', Carbon::now()->subDays(5))
            ->get();

        return response()->json($data);
    }


    public function updateCourseAction(Request $request)
    {
        NesaRequest::where('id', $request->id)
            ->update(['is_done' => 1]);

        return response()->json(['message' => 'Success']);
    }

}
