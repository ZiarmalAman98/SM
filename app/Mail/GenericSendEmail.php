<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericSendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public array $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Define the envelope (subject + metadata).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailData['title'] ?? 'New Message'
        );
    }

    /**
     * Define the content of the email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.generic',
            with: [
                'fromEmail' => $this->mailData['from'] ?? '',
                'toEmail' => $this->mailData['to'] ?? '',
                'title' => $this->mailData['title'] ?? '',
                'description' => $this->mailData['description'] ?? '',
            ],
        );
    }

    /**
     * Customize the sender address.
     */
    public function build()
    {
        return $this->from($this->mailData['from'] ?? config('mail.from.address'))
                    ->to($this->mailData['to'])
                    ->subject($this->mailData['title']);
    }

    /**
     * Attachments (optional).
     */
    public function attachments(): array
    {
        return [];
    }
}
