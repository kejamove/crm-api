<?php

namespace App\Http\Controllers;

use App\Models\EmailSetup;
use App\Models\Invoice;
use App\Models\Move;
use Illuminate\Http\Request;

class EmailSetupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return EmailSetup::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'host' => 'required|string|max:255',
            'mailer' => 'required|string|max:255',
            'port' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'encryption' => 'required|string|max:255',
            'from_address' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'firm' => 'required|string|max:255',
        ]);

        // Check if an EmailSetup record already exists for the given firm
        $email_setup = EmailSetup::where('firm', $validatedData['firm'])->first();

        if ($email_setup) {
            // If the record exists, update it
            $email_setup->update($validatedData);
            $message = 'Email setup updated successfully';
        } else {
            // If the record does not exist, create a new one
            $email_setup = EmailSetup::create($validatedData);
            $message = 'Email setup created successfully';
        }

        return response()->json(['message' => $message, 'email_setup' => $email_setup], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $mail_setup = EmailSetup::findOrFail($id);
        $mail_setup->delete();

    }
}
