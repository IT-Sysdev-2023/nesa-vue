<?php

namespace App\Console\Commands;

use App\Http\Controllers\NesaController;
use App\Models\NesaRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class ConsolidateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:consolidate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consolidates Nesa Requests When It Is About 5 days behind';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new NesaController();
        return $controller->consolidateProcess();
    }
}
