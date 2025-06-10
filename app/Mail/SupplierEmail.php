<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SupplierEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        // You can pass data here
    }

    public function build()
    {

        $path = Storage::disk('public')->path('/consolidated/example.xlsx');

        return $this->subject('This is the Request Near Expiry Stocks')
            ->view('emails.supplier')
               ->attach($path, [
                'as' => 'nesa.xlsx',
                'mime' => 'application/pdf',
            ]);
    }
}
