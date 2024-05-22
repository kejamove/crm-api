<?php

namespace App\Mail;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Move;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $move;
    public $branch;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, $mailConfig)
    {
        $this->invoice = $invoice;
        $move = Move::findOrFail($invoice->move);
        $this->move = $move;
        $branch = Branch::findOrFail($move->branch);
        $this->branch = $branch;


        if (!$mailConfig || !isset($mailConfig->host)) {
            throw new \Exception("Mail configuration is missing or incomplete.");
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.invoice')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->with(['move' => $this->move, 'invoice' => $this->invoice]);
    }
}
