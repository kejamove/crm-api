<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForwardEmail;

class EmailController extends Controller

{
    public function send_email(Request $request)
{
    $from_email = $request->input('from_email'); // Email address from which the email will be sent
    $subject = $request->input('subject'); // Email subject
    $message = $request->input('message'); // Email message
    $to_email = $request->input('to_email'); // Recipient's email address

    try {
        // Create a new ForwardEmail instance with the provided parameters
        $mail = new ForwardEmail($message, $subject, $to_email, $from_email);

        // Send the email
        Mail::to($to_email)->send($mail);

        return 'Mail sent successfully!';
    } catch (\Exception $e) {
        // Handle exception
        return 'Failed to send mail: ' . $e->getMessage();
    }
}

}
