<?php
namespace App\Mail;

use App\Models\Invoice;
use App\Models\Move;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade as PDF;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $move;

    /**
    * Create a new message instance.
    *
    * @return void
    */
    public function __construct($data)
    {
        $this->invoice = $data;
        $this->move = Move::findOrFail($this->invoice->move);

    }

    /**
    * Build the message.
    *
    * @return $this
    */
    public function build()
    {

        return $this->view('mail.invoice')
            ->with(['move', $this->move,
            'invoice' => $this->invoice
            ]);
    }


}
