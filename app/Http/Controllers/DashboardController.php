<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function  dashboardInfo(Request $request)
    {
        $users = User::all();

        return response()->json([
            'user' => $users,
            'usersCount' => $users->count()
        ]);
    }
}
