<?php

namespace App\Http\Controllers;

use App\Mail\SupplierEmail;
use App\Models\ConsolidatedRequest;
use App\Models\CourseOfAction;
use App\Models\NesaRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\NesaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;

class NesaController extends Controller
{

    public function __construct(public NesaService $nesaService) {}
    //
    public function dashboard()
    {
        return inertia('Nesa/NesaDashboard');
    }

    public function countNesa(): array
    {
        return $this->nesaService->countDashboardNesa();
    }
    public function countNesaApproval(): mixed
    {
        return $this->nesaService->countApprovalDashboardNesa();
    }
    public function countNesaApproved(): mixed
    {
        return $this->nesaService->countApproveListNesaDashboard();
    }
    public function nesaList(Request $request)
    {
        $nesaId = NesaRequest::select(
            'nesa_requests.itemcode',
            DB::raw('MIN(nesa_requests.id) as id')
        )
            ->groupBy('nesa_requests.itemcode')
            ->get()
            ->pluck('id');

        $nesa = NesaRequest::select(
            'nesa_requests.itemcode as item_code',
            'nesa_no',
            'nesa_requests.created_at',
            'suppliers.name as supname',
            'business_units.name as buname',
            'products.description',
        )
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('suppliers', 'supplier_code', '=', 'products.vendor_no')
            ->join('users', 'id', '=', 'created_by')
            ->join('business_units', 'business_units.id', '=', 'users.bu')
            ->when($request->search, function ($query) use ($request) {
                $query->where('suppliers.name', 'like', '%' . $request->search . '%');
            })->where('nesa_requests.is_consolidated', 0)
            ->whereIn('nesa_requests.id', $nesaId)
            ->where('nesa_requests.status', 'approved')
            ->paginate(10)
            ->withQueryString();

        $nesa->transform(function ($item) {
            $item->nesa_date = $item->created_at->toFormattedDateString() ?? null;
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
            'products.description',
            'quantity',
            'uom'
        )
            ->join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('suppliers', 'supplier_code', '=', 'products.vendor_no')
            ->join('users', 'id', '=', 'created_by')
            ->join('business_units', 'business_units.id', '=', 'users.bu')
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
        if ($getEmail != '0') {
            $sent =  Mail::to($getEmail)
                ->send(new SupplierEmail($request->all()));
        } else {
            return redirect()->back()->with([
                'status' => 'error',
            ]);
        }

        if ($sent) {

            ConsolidatedRequest::findOrFail($request->id)->update([
                'status' => 1
            ]);

            return redirect()->back();
        }
    }

    public function consolidateProcess()
    {

        $nesa = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->join('suppliers', 'supplier_code', '=', 'products.vendor_no')
            ->join('users', 'employee_id', '=', 'created_by')
            ->join('business_units', 'business_units.id', '=', 'users.bu')
            ->select(
                'nesa_requests.id as nesaId',
                'products.vendor_no',
                'products.description',
                'products.uom',
                'nesa_requests.itemcode',
                'nesa_requests.*',
                'suppliers.supplier_code',
                'users.bu',
                'business_units.name'
            )
            ->get()
            ->groupBy(['vendor_no', 'itemcode']);


        $nesa->each(function ($itemcode, $supplier) {

            $allQty = 0;

            $allQty = $itemcode->flatMap(function ($item) {
                return $item->pluck('quantity');
            })->sum();

            $supplierName = Supplier::where('supplier_code', $supplier)->value('name');

            $html = view('pdf.nesa', [
                'supp_code' => $supplier,
                'supplier' => $supplierName,
                'items' => $itemcode,
                'totalQty' => $allQty,
            ])->render();

            // // Generate PDF
            $pdf = new Dompdf();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $filename = "nesa/{$supplier}-" . substr(md5(uniqid()), 0, 6) . ".pdf";

            $itemCode = [];

            $nesa_id = 0;

            $itemcode->each(function ($item, $key) use (&$itemCode, &$nesa_id): void {
                $nesa_id = $item[0]->nesa_id;
                $itemCode[] = $key;
            });

            $disk = Storage::disk('public');

            if ($disk->exists(path: $filename)) {
                $disk->delete(paths: $filename); // optional, for clarity or logging
            }

            DB::transaction(callback: function () use ($nesa_id, $supplier, $itemCode, $filename) {
                ConsolidatedRequest::create(attributes: [
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

            DB::transaction(callback: function () use (&$item): mixed {
                NesaRequest::where('id', $item->nesaId)->update([
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
            ->whereNull('pre_approval')
            ->whereNull('approval')
            ->where('status', 1)
            ->paginate(10);

        $data->each(function ($item) {
            $names = [];
            $approvable = [];
            collect($item->item_code)->each(function ($i) use (&$names, &$approvable) {
                // dd($i);
                $names[] = Product::where('itemcode', $i)->value('description');
                $approvable[] = NesaRequest::where('itemcode', $i)->whereNull('coa')->count();
                return $i;
            });
            $item->approvable = $approvable[0] ?  true : false;
            $item->nesa_date = Date::parse($item->updated_at)->toFormattedDateString();
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

        $collectedData = collect();

        $collect->each(function ($item) use (&$collectedData) {

            $nesa = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
                ->join('users', 'employee_id', '=', 'created_by')
                ->join('business_units', 'business_units.id', '=', 'users.bu')
                ->leftJoin('course_of_actions', 'course_of_actions.id', '=', 'nesa_requests.coa')
                ->select(
                    'business_units.name',
                    'users.bu',
                    'nesa_requests.*',
                    'products.description',
                )
                ->where('nesa_requests.itemcode', $item)->paginate(10);

            $collectedData[] = $nesa;
        });

        return inertia('Nesa/NesaHistoryDetails', [
            'records' => $collectedData,
            'supplier' => Supplier::where('supplier_code', $request->supplier)->value('name'),
            'coa' => CourseOfAction::all(),
            'id' => $request->id,
        ]);
    }
    public function updateCourseOfAction(Request $request)
    {
        $queue = NesaRequest::findOrFail($request->id)->update([
            'coa' => $request->coa,
        ]);

        if (!$queue) {
            return redirect()->back()->with([
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'Something Went Wrong!',
            ]);
        }
        return redirect()->back()->with([
            'status' => 'success',
            'title' => 'Successful',
            'msg' => 'Successfully Updated Action',
        ]);
    }

    public function pendingApproval()
    {
        $data = ConsolidatedRequest::select(
            DB::raw("CONCAT_WS(' ', users.firstname, users.middlename, users.lastname, users.name_extention) AS full_name"),
            'consolidated_requests.id',
            'suppliers.*',
            'consolidated_requests.*'
        )
            ->join('users', 'users.id', '=', 'pre_approval')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'suplier_code')
            ->where('status', 1)
            ->whereNotNull('pre_approval')
            ->whereNull('approval')
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

        return inertia('Nesa/Pendings/PendingApproval', [
            'records' => $data,
        ]);
    }

    public function pendingDetails(Request $request)
    {

        $collect = collect($request->item_code);

        $collectedData = collect();

        $collect->each(function ($item) use (&$collectedData) {

            $nesa = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
                ->join('users', 'employee_id', '=', 'created_by')
                ->join('business_units', 'business_units.id', '=', 'users.bu')
                ->leftJoin('course_of_actions', 'course_of_actions.id', '=', 'nesa_requests.coa')
                ->select(
                    'business_units.name',
                    'users.bu',
                    'nesa_requests.*',
                    'products.description',
                )
                ->where('nesa_requests.itemcode', $item)->paginate(10);

            $collectedData[] = $nesa;
        });

        return inertia('Nesa/Pendings/PendingDetails', [
            'records' => $collectedData,
            'supplier' => Supplier::where('supplier_code', $request->supplier)->value('name'),
            'coa' => CourseOfAction::all(),
        ]);
    }

    public function tagCoa(Request $request)
    {
        $queue = ConsolidatedRequest::findOrFail($request->id)->update([
            'pre_approval' => $request->user()->id,
        ]);

        if (!$queue) {
            return redirect()->back()->with([
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'Something Went Wrong!',
            ]);
        }
        return redirect()->back()->with([
            'status' => 'success',
            'title' => 'Successful',
            'msg' => 'Tagging Nesa Successfully',
        ]);
    }

    public function approvePendingNesa(Request $request)
    {
        $queue = ConsolidatedRequest::findOrFail($request->id)->update([
            'approval' => $request->user()->id,
        ]);

        if (!$queue) {
            return redirect()->back()->with([
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'Something Went Wrong!',
            ]);
        }
        return redirect()->back()->with([
            'status' => 'success',
            'title' => 'Successful',
            'msg' => 'Approve Nesa Successfully',
        ]);
    }

    public function getApprovedNesa()
    {
        $data = ConsolidatedRequest::select(
            DB::raw("CONCAT_WS(' ', tag.firstname, tag.middlename, tag.lastname, tag.name_extention) AS tagby"),
            DB::raw("CONCAT_WS(' ', appr.firstname, appr.middlename, appr.lastname, appr.name_extention) AS appby"),
            'consolidated_requests.id',
            'suppliers.*',
            'consolidated_requests.*'
        )
            ->join('users as tag', 'tag.id', '=', 'pre_approval')
            ->join('users as appr', 'appr.id', '=', 'approval')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'suplier_code')
            ->where('status', 1)
            ->whereNotNull('pre_approval')
            ->whereNotNull('approval')
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

        return inertia('Nesa/Approved/ApprovedNesa', [
            'records' => $data,
        ]);
    }

    public function approvedDetails(Request $request)
    {

        $collect = collect($request->item_code);

        $collectedData = collect();

        $collect->each(function ($item) use (&$collectedData) {

            $nesa = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
                ->join('users', 'employee_id', '=', 'created_by')
                ->join('business_units', 'business_units.id', '=', 'users.bu')
                ->leftJoin('course_of_actions', 'course_of_actions.id', '=', 'nesa_requests.coa')
                ->select(
                    'business_units.name',
                    'users.bu',
                    'nesa_requests.*',
                    'products.description',
                )
                ->where('nesa_requests.itemcode', $item)->paginate(10);

            $collectedData[] = $nesa;
        });

        return inertia('Nesa/Approved/ApprovedDetails', [
            'records' => $collectedData,
            'supplier' => Supplier::where('supplier_code', $request->supplier)->value('name'),
            'coa' => CourseOfAction::all(),
        ]);
    }

    public function taggedApprovedDetails(Request $request)
    {
        $tagged =  ConsolidatedRequest::findOrFail($request->id)->update([
            'tag' => $request->user()->id,
        ]);

        if (!$tagged) {
            return redirect()->back()->with([
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'Something Went Wrong!',
            ]);
        }
        return redirect()->back()->with([
            'status' => 'success',
            'title' => 'Successful',
            'msg' => 'Tag Nesa Successfully',
        ]);
    }

    public function tagApprovedIndex()
    {
        $data = ConsolidatedRequest::select(
            DB::raw("CONCAT_WS(' ', tag.firstname, tag.middlename, tag.lastname, tag.name_extention) AS tagby"),
            DB::raw("CONCAT_WS(' ', appr.firstname, appr.middlename, appr.lastname, appr.name_extention) AS appby"),
            'consolidated_requests.id',
            'suppliers.*',
            'consolidated_requests.*'
        )
            ->join('users as tag', 'tag.id', '=', 'pre_approval')
            ->join('users as appr', 'appr.id', '=', 'approval')
            ->join('suppliers', 'suppliers.supplier_code', '=', 'suplier_code')
            ->where('status', 1)
            ->whereNotNull('pre_approval')
            ->whereNotNull('tag')
            ->whereNotNull('approval')
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

        return inertia('Nesa/Approved/TagApprovedNesa', [
            'records' => $data,
        ]);
    }
}
