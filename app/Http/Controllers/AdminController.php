<?php

namespace App\Http\Controllers;

use App\Events\SyncingProductsEvent;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ColumnHelper;

use function Laravel\Prompts\select;

class AdminController extends Controller
{
    public function about()
    {
        return inertia('About-Setup');
    }
    public function masterfileIndex()
    {
        return inertia('MasterFile/MasterFileIndex');
    }
    public function masterFile(Request $request)
    {

        return inertia('MasterFile/Product');
    }

    public function getProductItemSupplier()
    {
        $data = Product::query()
            ->select('products.id','products.uom','products.uom_price', 'products.itemcode', 'products.description', 'suppliers.name') // ✅ only needed cols
            ->join('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no')
            ->paginate(10);

        return response()->json([
            'records' => $data
        ]);
    }


    public function syncProducts()
    {
        set_time_limit(-1);
        $totalCount = DB::connection('sqlsrv')
            ->table(DB::raw('[dbo].[GGM ITEM MASTERFILE$Item]'))
            ->where('Blocked', '0')
            ->count(); // Get total items once before chunking

        $count = 1;

        DB::connection('sqlsrv')
            ->table(DB::raw('[dbo].[GGM ITEM MASTERFILE$Item]'))
            ->select('No_', 'Description', 'Base Unit of Measure', 'Unit Price', 'Vendor No_')
            ->where('Blocked', '0')
            ->orderBy('No_') // still required
            ->chunkById(1000, function ($items) use (&$count, $totalCount) {
                foreach ($items as $item) {
                    Product::updateOrCreate([
                        'itemcode' => $item->No_,
                        'description' => $item->Description,
                        'uom' => $item->{'Base Unit of Measure'},
                        'uom_price' => $item->{'Unit Price'},
                        'vendor_no' => $item->{'Vendor No_'},
                    ]);

                    SyncingProductsEvent::dispatch(
                        'Syncing data in NAV please wait....',
                        $count++, // current item number
                        $totalCount, // total item count across all chunks
                        Auth::user()
                    );
                }
            }, 'No_');
    }

    public function listOfSupplier(Request $request)
    {
        $supplier =  Supplier::when($request->search, function ($query, $search) {
            $query->where('supplier_code', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%');
        })
            ->paginate(10)
            ->withQueryString();
        return inertia("MasterFile/Supplier", [
            'records' => $supplier
        ]);
    }
    public function syncSupplier()
    {
        $totalCount = DB::connection('sqlsrv')
            ->table(DB::raw('[dbo].[GGM ITEM MASTERFILE$Vendor]'))
            ->count();

        $count = 1;


        DB::connection('sqlsrv')
            ->table(DB::raw('[dbo].[GGM ITEM MASTERFILE$Vendor]'))
            ->select('No_', 'Name')
            ->orderBy('No_')
            ->chunkById(1000, function ($items) use (&$count, $totalCount) {
                foreach ($items as $item) {
                    Supplier::updateOrCreate([
                        'supplier_code' => $item->No_,
                        'name' => $item->Name,
                    ]);

                    SyncingProductsEvent::dispatch(
                        'Syncing data in NAV please wait....',
                        $count++, // current item number
                        $totalCount, // total item count across all chunks
                        Auth::user()
                    );
                }
            }, 'No_');
    }

    public function addUser()
    {
        $usertype = DB::table('user_types')->select('id', 'name')->get();

        $businessUnit = DB::table('business_units')->select('id', 'name')->get();

        return inertia('AdminSetup/AddUser-Setup', [
            'usertypes' => $usertype,
            'businessUnit' => $businessUnit,
        ]);
    }

    public function getSuppliers(Request $request)
    {
        $suppliers = DB::table('suppliers')
            ->select('supplier_code as value', 'name as label')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('supplier_code', 'like', '%' . $search . '%');
                });
            })
            ->get();

        return response()->json($suppliers);
    }

    public function selectedSupplier(Request $request)
    {
        $specifiedProducts = DB::table('suppliers')
            ->select(
                'suppliers.name as supplierName',
                'suppliers.supplier_code as supplierCode',
            )->when($request->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('supplier_code', 'like', '%' . $search . '%');
            })
            ->paginate(10);

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Supplier Code', 'Supplier Name', 'Action'],
            ['supplierCode', 'supplierName', 'action']
        );

        return response()->json([
            'specifiedProducts' => $specifiedProducts->items(),
            'productColumns' => $columns,
            'search' => $request->search,
            'pagination' => [
                'current_page' => $specifiedProducts->currentPage(),
                'per_page' => $specifiedProducts->perPage(),
                'total' => $specifiedProducts->total(),
                'last_page' => $specifiedProducts->lastPage(),
                'from' => $specifiedProducts->firstItem(),
                'to' => $specifiedProducts->lastItem()
            ]
        ]);
    }

    public function submitUser(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'username' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'middlename' => 'required',
            'usertype' => 'required',
            'businessUnit' => 'required',
            'specifiedSupplier' => 'required|array|min:1'
        ]);
        $check = User::where('username',  $request->username)->exists();
        if ($check === true) {
            return back()->with(
                'error',
                'Username is already taken, please try another username'
            );
        }

        User::create([
            'username' => $request->username,
            'password' => Hash::make('NESA2025'),
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'middlename' => $request->middlename,
            'name_extention' => $request->nameExtention,
            'usertype' => $request->usertype,
            'bu' => $request->businessUnit,
            'employee_id' => $request->employee_id,
            'selected_supplier' => $request->specifiedSupplier
        ]);
        return back()->with(
            'success',
            'User added successfully'
        );
    }

    public function userType()
    {
        $usertype = User::join('user_types', 'user_types.id', '=', 'users.usertype')
            ->where('users.id', Auth::user()->id)
            ->select('user_types.name')
            ->first();

        return response()->json([
            'usertypes' => $usertype
        ]);
    }

    public function viewProfile()
    {
        // $result = DB::connection('pis')
        //     ->table('employee3')
        //     // ->select('photo', 'firstname', 'lastname')
        //     ->join('applicant', 'app_id', '=', 'emp_id')
        //     ->whereYear('startdate', '2025')
        //     ->where('emp_type', 'ojt')
        //     ->where('current_status', 'active')
        //     ->where('gender', 'Female')
        //     ->where('tag_as', 'new')
        //     ->where('school', 'Holy Name University')
        //     ->limit(100)
        //     ->get();
        // dd($result);
        return inertia('AdminSetup/ViewProfile-Setup');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required',
            'password' => 'required',
            'confirmPassword' => 'required'
        ]);

        $user = User::findOrFail($request->id);

        if (!Hash::check($request->oldPassword, $user->password)) {
            return back()->with('error', 'Old password is incorrect.');
        }

        if (Hash::check($request->password, $user->password)) {
            return back()->with('error', 'New password must be different from the old one.');
        }

        if ($request->password !== $request->confirmPassword) {
            return back()->with('error', 'New and old password do not match, please try again.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|max:255',
            'confirmPass' => 'required'
        ]);

        $check = User::findOrFail($request->id);

        if (!Hash::check($request->confirmPass, $check->password)) {
            return back()->with('error', 'Confirm password is not correct, please try again');
        }
        if ($check->username === $request->username) {
            return back()->with('error', 'The new username is the same as your current username');
        }
        if (User::where('username', $request->username)->where('id', '!=', $request->id)->exists()) {
            return back()->with('error', 'Username is already taken, please try another username');
        }

        $check->update([
            'username' => $request->username
        ]);
        return back()->with('success', 'Username updated successfully');
    }

    public function setupUser(Request $request)
    {
        $usertype = DB::table('user_types')->select('id', 'name')->get();
        $businessUnit = DB::table('business_units')->select('id', 'name')->get();

        $users = User::select(
            'users.id',
            'users.username',
            'users.firstname',
            'users.lastname',
            'users.middlename',
            'users.name_extention',
            'users.usertype',
            'users.bu',
            'user_types.name as userType',
            'business_units.name as businessUnit',
            'users.selected_supplier'
        )
            ->join('user_types', 'user_types.id', '=', 'users.usertype')
            ->join('business_units', 'business_units.id', '=', 'users.bu')
            ->when($request->search, function ($query, $search) {
                $query->where('users.username', 'like', '%' . $search . '%')
                    ->orWhere('users.middlename', 'like', '%' . $search . '%')
                    ->orWhere('users.firstname', 'like', '%' . $search . '%')
                    ->orWhere('users.lastname', 'like', '%' . $search . '%')
                    ->orWhere('users.name_extention', 'like', '%' . $search . '%');
            })
            ->orderByDesc('users.id')
            ->paginate(10)
            ->withQueryString();

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Username', 'Firstname', 'Middlename', 'Lastname', 'Name Extention', 'Business Unit', 'Usertype', 'Actions'],
            ['username', 'firstname', 'middlename', 'lastname', 'name_extention', 'businessUnit', 'userType', 'action']
        );
        return inertia('AdminSetup/SetupUser-Setup', [
            'users' => $users,
            'columns' => $columns,
            'usertype' => $usertype,
            'businessUnit' => $businessUnit,
            'search' => $request->search
        ]);
    }

    public function deleteUserAccount(Request $request)
    {
        User::where('id', $request->id)->delete();
    }

    public function updateUserDetails(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id' => 'required',
            'username' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'businessUnit' => 'required|integer',
            'usertype' => 'required|integer',
            'specifiedSupplier' => 'required|array|min:1'

        ]);

        $user = User::findOrFail($request->id);

        $changesDetected =
            $user->username !== $request->username ||
            $user->firstname !== $request->firstname ||
            $user->middlename !== $request->middlename ||
            $user->lastname !== $request->lastname ||
            $user->bu != $request->businessUnit ||
            $user->usertype != $request->usertype ||
            $user->name_extention !== $request->nameExtention ||
            $user->selected_supplier !== $request->specifiedSupplier;

        if (!$changesDetected) {
            return back()->with('error', 'No changes detected. Please make updates before submitting.');
        }

        if ($user->username !== $request->username) {
            $exists = User::where('username', $request->username)
                ->where('id', '!=', $request->id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'The username is already taken.');
            }
        }

        $user->update([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'bu' => $request->businessUnit,
            'usertype' => $request->usertype,
            'name_extention' => $request->nameExtention,
            'selected_supplier' => $request->specifiedSupplier
        ]);

        return back()->with('success', 'User credentials updated successfully.');
    }
    
}
