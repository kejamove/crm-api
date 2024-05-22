<?php
namespace App\Mail;

use App\Models\Branch;
use App\Models\EmailSetup;
use App\Models\Firm;
use App\Models\Invoice;
use App\Models\Move;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Services\MailConfigService;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $move;
    public $branch;
    public $email_setup_config;

    /**
    * Create a new message instance.
    *
    * @return void
    */
    public function __construct($data)
    {
        $this->invoice = $data;
        $this->move = Move::findOrFail($this->invoice->move);

        $this->branch = Branch::findOrFail($this->move->branch);

        if (empty($this->branch->firm)) {
            throw new \Exception("Firm data is missing: {$this->branch->firm}");
        }

        try {
            $this->email_setup_config = EmailSetup::where('firm', $this->branch->firm)->firstOrFail();

            MailConfigService::setMailConfig(
                $this->email_setup_config->mailer,
                $this->email_setup_config->host,
                $this->email_setup_config->port,
                $this->email_setup_config->username,
                $this->email_setup_config->password,
                $this->email_setup_config->encryption,
                $this->email_setup_config->from_address,
                $this->email_setup_config->from_name,
            );
        } catch (ModelNotFoundException $e) {
            // Log the error and throw a custom exception with a descriptive message
            $message = "This Firm is not confiured to send emails";
            \Log::error($message, ['firm' => $this->move->firm]);
            throw new Exception($message);
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
            ->with(['move'=> $this->move,
            'invoice' => $this->invoice
            ]);
    }


}
