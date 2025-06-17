<?php

namespace App\Http\Controllers;

use App\Mail\SupplierEmail;
use App\Models\ConsolidatedRequest;
use App\Models\NesaRequest;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;

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
            'supplier',
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
            ->paginate(10)
            ->withQueryString();

        $nesa->map(function ($item) {
            $item->expiry = Date::parse($item->expiry)->toFormattedDateString();
            $item->quantity = $item->quantity . ' pcs';
            return $item;
        });

        return inertia('Nesa/NesaViewing', [
            'records' => $nesa
        ]);
    }

    public function sendEmailFunction(Request $request)
    {
        $getEmail = Supplier::where('supplier_code', $request->sup)->value('email');

        $sent =  Mail::to($getEmail)
            ->send(new SupplierEmail($request->all()));

        if ($sent) {

            ConsolidatedRequest::findOrFail($request->id)->update([
                'status' => 1
            ]);

            return redirect()->back();
        }
    }

    public function consolidateProcess()
    {
        $nesa = NesaRequest::join('suppliers', 'supplier_code', '=', 'supplier')
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('business_units', 'business_units.id', '=', 'bu')
            ->get()
            ->groupBy(['supplier', 'itemcode']);

        $nesa->each(function ($itemcode, $supplier) {

            $supplierName = Supplier::where('supplier_code', $supplier)->value('name');

            $html = view('pdf.nesa', [
                'supp_code' => $supplier,
                'supplier' => $supplierName,
                'items' => $itemcode,
            ])->render();

            // // Generate PDF
            $pdf = new Dompdf();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $filename = "nesa/{$supplier}-" . substr(md5(uniqid()), 0, 6) . ".pdf";

            $itemCode = [];

            $nesa_id = 0;

            $itemcode->each(function ($item, $key) use (&$itemCode, &$nesa_id) {
                $nesa_id = $item[0]->nesa_id;
                $itemCode[] = $key;
            });

            $disk = Storage::disk('public');

            if ($disk->exists($filename)) {
                $disk->delete($filename); // optional, for clarity or logging
            }

            DB::transaction(function () use ($nesa_id, $supplier, $itemCode, $filename) {
                ConsolidatedRequest::create([
                    'suplier_code' => $supplier,
                    'item_code' =>  $itemCode,
                    'nesa_id' =>   $nesa_id,
                    'documents' => $filename,
                    'batch' => 1,
                    'status' => 0,
                ]);
            });

            $disk->put($filename, $pdf->output());
        });


        $nesa->flatten(2)->each(function ($item) {

            DB::transaction(function () use (&$item) {
                NesaRequest::where('id', $item->nesa_no)->update([
                    'is_consolidated' => 1
                ]);

                return $item;
            });
        });

        return redirect()->back();
    }

    public function consolidatedList()
    {
        $data = ConsolidatedRequest::select('consolidated_requests.id', 'suppliers.*', 'consolidated_requests.*')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'suplier_code')
            ->where('status', 0)
            ->paginate(10);

        $data->each(function ($item) {
            $names = [];
            collect($item->item_code)->each(function ($i) use (&$names) {
                // dd($i);
                $names[] = Product::where('itemcode', $i)->value('description');
                return $i;
            });
            $item->desc = $names;
            return $item;
        });

        return inertia('Nesa/ConsolidatedList', [
            'records' => $data,
        ]);
    }
    public function nesaHistory()
    {
        $data = ConsolidatedRequest::select('consolidated_requests.id', 'suppliers.*', 'consolidated_requests.*')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'suplier_code')
            ->where('status', 1)
            ->paginate(10);

        $data->each(function ($item) {
            $names = [];
            collect($item->item_code)->each(function ($i) use (&$names) {
                // dd($i);
                $names[] = Product::where('itemcode', $i)->value('description');
                return $i;
            });
            $item->desc = $names;
            return $item;
        });

        return inertia('Nesa/NesaHistory', [
            'records' => $data,
        ]);
    }

    public function nesaHistoryDetails(Request $request)
    {

        $collect = collect($request->item_code);

        $collectedData = [];

        $collect->each(function ($item) use (&$collectedData) {

            $nesa = NesaRequest::join('business_units', 'business_units.id', '=', 'nesa_requests.bu')
            ->join('products', 'products.itemcode', '=','nesa_requests.itemcode')
                ->where('nesa_requests.itemcode', $item)->get();

            $collectedData[] = $nesa;
        });

        return inertia('Nesa/NesaHistoryDetails', [
            'records' => $collectedData,
            'supplier' => Supplier::where('supplier_code', $request->supplier)->value('name')
        ]);
    }
}
