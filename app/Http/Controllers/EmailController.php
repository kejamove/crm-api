<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import the Log facade
use Illuminate\Support\Facades\Mail;
use App\Mail\ForwardEmail;
use Exception; 

class EmailController extends Controller

{

    public function email_received_and_under_review(Request $request) {
        try {
            $data = $request->all(); 
                
            /**
             * Notify us of a possible lead
             */
            Mail::send('mail.mail', $data, function ($message) use ($data) {
                $message->to('joshuamutua39@gmail.com', 'Keja Move')
                        ->subject('Possible Move')
                        ->from($data['from_email'], $data['from_name']);
            });

            /**
             * Notify the client that we have received their email
             */
            Mail::send('mail.notify_client', $data, function ($message) use ($data) {
                $message->to($data['from_email'], $data['from_name'])
                        ->subject('Possible Move')
                        ->from('vicmwe184@gmail.com', 'Keja Move');
            });
    
            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            // Return the actual error message in the API response
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function close_move_and_give_client_feedback(Request $request) {
        try {
            $data = $request->all(); 

            /**
             * Notify the client that we have received their email
             */
            Mail::send('mail.successful_move', $data, function ($message) use ($data) {
                $message->to($data['client_email'], $data['client_name'])
                        ->subject('Satisfactory Move')
                        ->from('vicmwe184@gmail.com', 'Keja Move')
                        ->replyTo('no-reply@example.com');
                        
            });
    
            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            // Return the actual error message in the API response
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
   
}