<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function addUser(Request $request)
    {
        return Inertia::render('AdminSetup/AddUser-Setup');
    }
}
