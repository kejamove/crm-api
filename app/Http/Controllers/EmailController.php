<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import the Log facade
use Illuminate\Support\Facades\Mail;
use App\Mail\ForwardEmail;
use Exception; 

class EmailController extends Controller

{

    public function send_email(Request $request) {
        try {
            $data = $request->all(); // Assuming you'll pass the recipient email and name in the request
    
            // Validate request data here if needed
            
            Mail::send('mail.mail', $data, function ($message) use ($data) {
                $message->to($data['to_email'], $data['to_name'])
                        ->subject($data['to_name'])
                        ->from($data['from_email'], $data['from_name']);
            });
    
            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error sending email: ' . $e->getMessage());

        // Return the actual error message in the API response
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    
}