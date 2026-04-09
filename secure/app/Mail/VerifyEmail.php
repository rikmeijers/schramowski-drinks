<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $app_name;
    public string $email;
    public string $action_url;

    /**
     * Create a new message instance.
     */
    public function __construct(string $email, string $action_url)
    {
        $this->app_name = $_ENV['APP_NAME'];
        $this->email = $email;
        $this->action_url = $action_url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address($_ENV['MAIL_REPLY_TO_ADDRESS'], $_ENV['MAIL_REPLY_TO_NAME']),
            ],
            subject: 'E-Mail-Bestätigung für ' . $this->app_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verifymail',
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
