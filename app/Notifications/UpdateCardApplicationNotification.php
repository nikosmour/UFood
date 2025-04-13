<?php

namespace App\Notifications;

use App\Mail\CardApplicationUpdatedNotification;
use App\Models\CardApplicationUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class UpdateCardApplicationNotification extends Notification implements ShouldQueue //see more on /docs/future.md#005
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public CardApplicationUpdate $cardApplicationUpdate;

    public function __construct(CardApplicationUpdate $cardApplicationUpdate)
    {
        $this->cardApplicationUpdate = $cardApplicationUpdate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $send = ['mail'];

        if (config('app.vonage')) {
            $send[] = 'vonage';
        }
        return $send;
    }

    /**false
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        return (new CardApplicationUpdatedNotification($this->cardApplicationUpdate))
            ->to($this->cardApplicationUpdate->Academic()->value('email'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

        ];
    }

    public function toVonage(object $notifiable): VonageMessage

    {
        $statusName = $this->cardApplicationUpdate->status->name;
        return (new VonageMessage)
            ->content(/*"Αλλαγή της κατάστασης της αίτησής σας: " . __('status.' . $statusName, [], 'el') . ".\n"
                . */ "Your application status has been updated: " . __('status.' . $statusName, [], 'en') . '.')/* ->unicode()*/ ;

    }

}
