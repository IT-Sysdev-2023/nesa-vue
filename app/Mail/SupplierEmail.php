<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SupplierEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $suppliercode = '';
    protected $docs = '';

    public function __construct(public array $request )
    {
        $this->suppliercode = $request['sup'];
        $this->docs = $request['docs'];
        // You can pass data here
    }

    public function build()
    {
        
        $path = Storage::disk('public')->path('/' . $this->docs);

        return $this->subject('This is the Request Near Expiry Stocks')
            ->view('emails.supplier')
               ->attach($path, [
                'as' => 'nesa.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
