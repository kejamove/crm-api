<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\EmailSetup;
use App\Models\Firm;
use App\Models\Invoice;
use App\Models\Move;
use Illuminate\Http\Request;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Invoice::all();
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'move' => 'required|string|max:255',
        ]);

        $move = Move::findOrFail($request->move);


        do {
            $invoice_number = 'kjvce' . substr(md5(uniqid()), 0, 4);
        } while (Invoice::where('invoice_number', $invoice_number)->exists());

        $invoice = Invoice::create([
            'invoice_number' => $invoice_number,
            'invoice_amount' => $move->invoiced_amount,
            'client_name' => $move->consumer_name,
            'client_last_name' => $move->invoiced_amount,
            'client_email' => $move->client_email,
            'move' => $request->move,
        ]);

        return response()->json(['message' => 'Invoice created successfully', 'invoice' => $invoice], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Send Invoice to client
     */
    public function send_invoice(string $id)
    {
        /*
         * WHO CAN SEND INVOICE:
         * CONDITIONS:
         * 1. You have to belong to the firm or be a super admin
         * 2. If you are not a
         */
        try {
            // Retrieve invoice data
            $data = Invoice::findOrFail($id);

            // Retrieve move and branch information
            $move = Move::findOrFail($data->move);
            $branch = Branch::findOrFail($move->branch);
            $firm = Firm::findOrFail($branch->firm);


//          Retrieve the email setup configuration for the firm
            $mailSetup = EmailSetup::where('firm', $firm->id)->firstOrFail();

//           Prepare mail configuration
             config([
                'mail.mailers.smtp.host' => $mailSetup->host,
                'mail.mailers.smtp.port' => $mailSetup->port,
                'mail.mailers.smtp.username' => $mailSetup->username,
                'mail.mailers.smtp.password' => $mailSetup->password,
                'mail.mailers.smtp.encryption' => $mailSetup->encryption,
                'mail.from.address' => $mailSetup->from_address,
                'mail.from.name' => $mailSetup->from_name,
            ]);

//           Ensure that mail configuration is correctly structured
            if (!isset($mailSetup->host) || !isset($mailSetup->port) ) {
                throw new \Exception("Mail configuration is missing required fields.");
            }

//            // Send the email
            Mail::to($data->client_email)->send(new InvoiceMail($data, $mailSetup));

            return response()->json(['message' => 'Email sent successfully!'], 200);
        } catch (ModelNotFoundException $e) {
            // Log the error
            \Log::error('Model not found: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to find required data.'], 404);
        } catch (\Exception $e) {
            // Log any other errors that occur during email sending
            \Log::error('Error sending email: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send email.', 'err'=> $e->getMessage()], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
