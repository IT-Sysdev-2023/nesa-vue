<?php

namespace App\Services;

use App\Models\ConsolidatedRequest;
use App\Models\NesaRequest;
use Illuminate\Support\Facades\DB;

class NesaService
{
    public function __construct()
    {
        //
    }

    public function countDashboardNesa()
    {

        return [
            'nesaCount' => $this->getNesaPending(),
            'approvedCount' => $this->getNesaConsolidated(),
            'conCount' => 30,
            'sonCount' => 20,
        ];
    }

    public function getNesaPending()
    {
        $nesa = NesaRequest::select(
            'nesa_requests.itemcode',
            DB::raw('MIN(nesa_requests.id) as id') // get the earliest ID per itemcode
        )
            ->groupBy('nesa_requests.itemcode')
            ->get()
            ->pluck('id');

        return $nesa = NesaRequest::join('products', 'products.itemcode', '=', 'nesa_requests.itemcode')
            ->where('nesa_requests.is_consolidated', 0)
            ->whereIn('nesa_requests.id', $nesa)
            ->where('nesa_requests.status', 'approved')
            ->count();
    }

    public function getNesaConsolidated(){
        return ConsolidatedRequest::where('status', 0)
            ->count();
    }
}
