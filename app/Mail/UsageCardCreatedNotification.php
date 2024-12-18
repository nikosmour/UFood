<?php

namespace App\Mail;

use App\Models\UsageCard;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UsageCardCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public UsageCard $model;

    /**
     * Create a new message instance.
     */
    public function __construct(UsageCard $usageCard)
    {
        $this->model = $usageCard;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Αλλαγή της κατάστασης της αίτησης σας / Card Application Updated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.usageCardCreated',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
