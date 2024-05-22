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


        $email_setup = EmailSetup::create($validatedData);

        return response()->json(['message' => 'Email Set up successfully', 'email_setup' => $email_setup], 201);
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
        //
    }
}
