<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NesaController extends Controller
{
    //
    public function nesaList(){
        return inertia('Nesa/NesaList');
    }
}
