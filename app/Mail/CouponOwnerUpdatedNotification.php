<?php

namespace App\Mail;

use App\Models\CouponOwner;
use App\Models\CouponTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CouponOwnerUpdatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public CouponOwner $model;
    public CouponTransaction $latestTransaction;

    /**
     * Create a new message instance.
     */
    public function __construct(CouponOwner $couponOwner, CouponTransaction $couponTransaction)
    {
        $this->model = $couponOwner;
        $this->latestTransaction = $couponTransaction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Αλλαγή υπολοίπου κουπονιών / Coupon Balance changed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.couponOwnerUpdated',
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
