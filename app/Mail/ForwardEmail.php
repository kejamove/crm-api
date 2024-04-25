<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForwardEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $subject;
    public $to_mail;
    public $from_email;

    /**
     * Create a new message instance.
     */
    public function __construct($message, $subject, $to_mail, $from_email)
    {
        $this->message = $message;
        $this->subject = $subject;
        $this->to_mail = $to_mail;
        $this->from_email = $from_email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->from_email,
            replyTo: [$this->to_mail],
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
