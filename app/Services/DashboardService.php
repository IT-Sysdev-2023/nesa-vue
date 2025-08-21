<?php

namespace App\Services;

use App\Models\NesaRequest;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function __construct()
    {
        //
    }
    public function userCountEveryDayAndMoth()
    {
        $todayCount = User::whereDate('created_at', Carbon::today())->get();


        if ($todayCount->count() > 0) {
            return '+ ' . $todayCount->count() . ' today';
        } else {
            $monthCount = $todayCount->where('created_at', '>=', now()->startOfMonth())->count();
            if ($monthCount > 0) {
                return '+ ' . $monthCount->count() . ' this month';
            } else {
                $yearCount = $todayCount->where('created_at', '>=', now(
                    
                )->startOfYear())->count();
                if ($yearCount > 0) {
                    return '+ ' . $yearCount->count() . ' this year';
                }
            }
        }

        return 'No new users today';
    }

    public function nesaCount()
    {
        return NesaRequest::count();
    }

    public function nesaThisMonth()
    {
        $nesa = NesaRequest::where('created_at', '>=', now()->startOfMonth())
            ->count();

        if ($nesa > 0) {
            return '+ ' . $nesa . ' this month';
        }

        return 'No Nesa requests this month';
    }
}
