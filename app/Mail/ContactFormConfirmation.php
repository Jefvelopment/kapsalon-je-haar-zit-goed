<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $contactData;

    public function __construct($contactData)
    {
        $this->contactData = $contactData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We hebben uw bericht ontvangen - Je haar zit goed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-confirmation',
            with: [
                'name' => $this->contactData['name'],
                'subject' => $this->contactData['subject'],
                'inquiry_type' => $this->contactData['inquiry_type'],
                'treatment_name' => $this->contactData['treatment_name'] ?? null,
                'product_name' => $this->contactData['product_name'] ?? null,
            ],
        );
    }
}
