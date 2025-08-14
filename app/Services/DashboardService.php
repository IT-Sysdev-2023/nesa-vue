<?php

namespace App\Services;

use App\Models\NesaRequest;

class DashboardService
{
    public function __construct()
    {
        //
    }
    public function userCountEveryDayAndMoth($users)
    {
        $todayCount = $users->where('created_at', '>=', now()->startOfDay())->count();

        if ($todayCount > 0) {
            return '+ ' . $todayCount . ' today';
        } else {
            $monthCount = $users->where('created_at', '>=', now()->startOfMonth())->count();
            if ($monthCount > 0) {
                return '+ ' . $monthCount . ' this month';
            } else {
                $yearCount = $users->where('created_at', '>=', now()->startOfYear())->count();
                if ($yearCount > 0) {
                    return '+ ' . $yearCount . ' this year';
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
