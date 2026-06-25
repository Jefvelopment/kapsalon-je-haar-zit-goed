<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmission extends Mailable
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
            subject: 'Nieuw contactformulier van ' . $this->contactData['name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-submission',
            with: [
                'name' => $this->contactData['name'],
                'email' => $this->contactData['email'],
                'phone' => $this->contactData['phone'] ?? 'Niet opgegeven',
                'subject' => $this->contactData['subject'],
                // Hernoemd van 'message' naar 'customer_message': Laravel injecteert in
                // mailable-views automatisch een $message-variabele (het Illuminate\Mail\Message
                // object zelf), dus een eigen 'message'-sleutel botst daarmee.
                'customer_message' => $this->contactData['message'],
                'inquiry_type' => $this->contactData['inquiry_type'],
                'treatment_name' => $this->contactData['treatment_name'] ?? null,
                'product_name' => $this->contactData['product_name'] ?? null,
            ],
        );
    }
}