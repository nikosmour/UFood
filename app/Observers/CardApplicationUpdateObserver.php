<?php

namespace App\Observers;

use App\Events\CardApplicationUpdated;
use App\Mail\CardApplicationUpdatedNotification;
use App\Models\CardApplicationUpdate;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Mail;

class CardApplicationUpdateObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the CardApplicationUpdate "created" event.
     */
    public function created(CardApplicationUpdate $cardApplicationUpdate): void
    {
        $cardApplicationUpdate->load('cardApplication:id,expiration_date');
        if ($cardApplicationUpdate->card_application_staff_id)
            broadcast(event: new CardApplicationUpdated(
                cardApplicationUpdate: $cardApplicationUpdate
            ))->toOthers();
    }

    /**
     * Handle the CardApplicationUpdate "updated" event.
     */
    public function updated(CardApplicationUpdate $cardApplicationUpdate): void
    {
        $cardApplicationUpdate->load('cardApplication:id,expiration_date');
        if ($cardApplicationUpdate->card_application_staff_id)
                broadcast(event: new CardApplicationUpdated(
                    cardApplicationUpdate: $cardApplicationUpdate
                ))->toOthers();
        dispatch(function () use ($cardApplicationUpdate) {
            Mail::to($cardApplicationUpdate->Academic()->value('email'))->send(new CardApplicationUpdatedNotification($cardApplicationUpdate));
        })->afterResponse();

    }

    /**
     * Handle the CardApplicationUpdate "deleted" event.
     */
    public function deleted(CardApplicationUpdate $cardApplicationUpdate): void
    {
        //
    }

    /**
     * Handle the CardApplicationUpdate "restored" event.
     */
    public function restored(CardApplicationUpdate $cardApplicationUpdate): void
    {
        //
    }

    /**
     * Handle the CardApplicationUpdate "force deleted" event.
     */
    public function forceDeleted(CardApplicationUpdate $cardApplicationUpdate): void
    {
        //
    }
}
