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

    public function countDashboardNesa(): array
    {

        return [
            'nesaCount' => $this->getNesaPending(),
            'approvedCount' => $this->getApprovedNesaCount(),
            'conCount' => $this->getNesaConsolidated(),
            'sonCount' => $this->getSummaryOfNesaCount(),
        ];
    }

    public function getNesaPending(): mixed
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

    public function getNesaConsolidated(): mixed
    {
        return ConsolidatedRequest::where(column: 'status', operator: 0)
            ->count();
    }

    public function getSummaryOfNesaCount(): mixed
    {
        return ConsolidatedRequest::whereNull('pre_approval')
            ->whereNull('approval')
            ->where('status', 1)
            ->count();
    }

    public function getApprovedNesaCount(): mixed
    {
        return ConsolidatedRequest::where('status', 1)
            ->whereNotNull('pre_approval')
            ->whereNotNull('approval')
            ->count();
    }

    public function countApprovalDashboardNesa(): array
    {
        return [
            'approvalCount' => $this->getApprovalCount(),
            'approvedCount' => $this->getApprovedNesaCountApprove(),
        ];
    }

    public function getApprovalCount(): mixed
    {
      return ConsolidatedRequest::where('status', 1)
            ->whereNotNull('pre_approval')
            ->whereNull('approval')
            ->count();
    }

    public function getApprovedNesaCountApprove(): mixed{
      return  ConsolidatedRequest::where('status', 1)
            ->whereNotNull('pre_approval')
            ->whereNotNull('approval')
            ->count();
    }

    public function countApproveListNesaDashboard(){
        return [
            'approvedCount' => $this->countApprovedNesa(),
        ];
    }
    public function countApprovedNesa(){
          return ConsolidatedRequest::where('status', 1)
            ->whereNotNull('pre_approval')
            ->whereNotNull('tag')
            ->whereNotNull('approval')
            ->count();
    }
}
