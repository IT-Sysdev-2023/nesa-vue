<?php

namespace App\Http\Controllers;

use App\Events\SyncingProductsEvent;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
}
