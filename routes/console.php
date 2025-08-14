<?php

use App\Models\NesaRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('start:consolidate')
//     ->dailyAt('13:11')
//     ->timezone('Asia/Manila');

Schedule::call(function () {
    $nesaRequest = NesaRequest::where('is_consolidated', 0)->orderBy('created_at')->first();

    if ($nesaRequest) {
        $startDate = Carbon::parse($nesaRequest->created_at)->startOfDay();
        $today = Carbon::today();

        $daysDifference = $startDate->diffInDays($today);

        if ($daysDifference == 5) {  // Exactly 1 day difference means created yesterday
            Artisan::call('start:consolidate');
        }
    }
})->dailyAt('00:00')->timezone('Asia/Manila');
