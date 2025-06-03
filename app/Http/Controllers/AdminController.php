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

class AdminController extends Controller
{
    //
    public function masterFile(Request $request)
    {
        $query = Product::query()
            ->leftJoin('suppliers', 'suppliers.supplier_code', '=', 'products.vendor_no');


        if ($request->filled('search')) {
            $query->where('itemcode', 'like', '%' . $request->search . '%');
        }

        return inertia('MasterFile/Product', [
            'records' => $query->paginate(10)->withQueryString(),
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

    public function listOfSupplier()
    {
        return inertia("MasterFile/Supplier", [
            'records' => Supplier::paginate(10),
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

    public function addUser(Request $request)
    {
        $usertype = DB::table('user_types')->select('id', 'name')->get();
        $businessUnit = DB::table('business_units')->select('id', 'name')->get();

        return inertia('AdminSetup/AddUser-Setup', [
            'usertypes' => $usertype,
            'businessUnit' => $businessUnit
        ]);
    }

    public function submitUser(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'middlename' => 'required',
            'usertype' => 'required',
            'businessUnit' => 'required'
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
            'password' => Hash::make($request->password),
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'middlename' => $request->middlename,
            'name_extention' => $request->nameExtention,
            'usertype' => $request->usertype,
            'bu' => $request->businessUnit
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
        return inertia('AdminSetup/ViewProfile-Setup');
    }
}
