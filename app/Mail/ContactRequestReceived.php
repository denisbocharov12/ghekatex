<?php

namespace App\Mail;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly ContactRequest $contactRequest,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->contactRequest->subject ?: __('Новая заявка с сайта');

        return new Envelope(
            subject: 'GHEKATEX — '.$subject,
            replyTo: [$this->contactRequest->email],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact-request');
    }
}
