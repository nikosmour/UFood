<?php

namespace App\Observers;

use App\Events\CouponOwnerUpdated;
use App\Mail\CouponOwnerUpdatedNotification;
use App\Models\CouponOwner;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Mail;

class CouponOwnerObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the CouponOwner "created" event.
     */
    public function created(CouponOwner $couponOwner): void
    {
        //
    }

    /**
     * Handle the CouponOwner "updated" event.
     */
    public function updated(CouponOwner $couponOwner): void
    {
        $transaction = $couponOwner->couponTransactionLatest;
        dispatch(function () use ($transaction, $couponOwner) {
            Mail::to($couponOwner->academic()->value('email'))->send(new CouponOwnerUpdatedNotification($couponOwner, $transaction));
        })->afterResponse();
        if ($transaction->transaction !== 'sending')
            broadcast(event: new CouponOwnerUpdated(
                couponTransaction: $transaction
            ))->toOthers();
    }

    /**
     * Handle the CouponOwner "deleted" event.
     */
    public function deleted(CouponOwner $couponOwner): void
    {
        //
    }

    /**
     * Handle the CouponOwner "restored" event.
     */
    public function restored(CouponOwner $couponOwner): void
    {
        //
    }

    /**
     * Handle the CouponOwner "force deleted" event.
     */
    public function forceDeleted(CouponOwner $couponOwner): void
    {
        //
    }
}
