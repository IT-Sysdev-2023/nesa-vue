<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function __construct(public DashboardService $dashboardService)
    {
        // You can inject services here if needed
    }
    public function  dashboardInfo(Request $request)
    {
        $users = User::all();

        return response()->json([
            'user' => $users,
            'usersCount' => $users->count(),
            'usersCountToday' => $this->dashboardService->userCountEveryDayAndMoth(),
            'nesaCount' => $this->dashboardService->nesaCount(),
            'nesaThisMonth' => $this->dashboardService->nesaThisMonth(),
        ]);
    }

    public function dashboard()
    {
        $users = User::count();

        return inertia('Dashboard', [
            'usersCount' => $users,
        ]);
    }
}
