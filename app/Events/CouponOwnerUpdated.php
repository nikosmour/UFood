<?php

namespace App\Events;

use App\Models\CouponTransaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CouponOwnerUpdated implements ShouldBroadcast//,ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public CouponTransaction $couponTransaction
    )
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {

        $id = match ($this->couponTransaction->transaction) {
            'sending' => $this->couponTransaction->other_person_id,
            default => $this->couponTransaction->academic_id,
        };

        return [
            new PrivateChannel('academic.' . $id),
        ];
    }

}
