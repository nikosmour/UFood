<?php

namespace App\Observers;

use App\Mail\UsageCardCreatedNotification;
use App\Models\UsageCard;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Mail;

class UsageCardObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the UsageCard "created" event.
     */
    public function created(UsageCard $usageCard): void
    {
        //
    }

    /**
     * Handle the UsageCard "updated" event.
     */
    public function updated(UsageCard $usageCard): void
    {
        if ($usageCard->isDirty('status')) {
            Mail::to($usageCard->Academic()->value('email'))->send(new UsageCardCreatedNotification($usageCard));
        }
    }

    /**
     * Handle the UsageCard "deleted" event.
     */
    public function deleted(UsageCard $usageCard): void
    {
        //
    }

    /**
     * Handle the UsageCard "restored" event.
     */
    public function restored(UsageCard $usageCard): void
    {
        //
    }

    /**
     * Handle the UsageCard "force deleted" event.
     */
    public function forceDeleted(UsageCard $usageCard): void
    {
        //
    }
}
