<?php

namespace App\Http\Controllers;

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
        $validatedData = $request->validate([
            'client_first_name' => 'required|string|max:255',
            'client_last_name' => 'required|string|max:255',
            'client_email' => 'required|string|max:255',
            'move' => 'required|string|max:255',
        ]);

        $invoice_amount = Move::findOrFail($request->move)->first();

        do {
            $invoice_number = 'kjvce' . substr(md5(uniqid()), 0, 4);
        } while (Invoice::where('invoice_number', $invoice_number)->exists());

        $invoice = Invoice::create(array_merge($validatedData, ['invoice_number' => $invoice_number, 'invoice_amount' => $invoice_amount->invoiced_amount]));

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
        $invoice = Invoice::findOrFail($id);
        // Send the email
        Mail::to($invoice->client_email)->send(new InvoiceMail($invoice));

        return response()->json(['message' => 'Email sent successfully!', $invoice->client_email, $id], 200);
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
