<?php

namespace App\Events;

use App\Models\CardApplication;
use App\Models\CardApplicationUpdate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardApplicationUpdated implements ShouldBroadcast//,ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $cardApplication_id;
    public string $expiration_date;
    private CardApplication $cardApplication;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public CardApplicationUpdate $cardApplicationUpdate,
//        public CardStatusEnum $status,
//        private CardStatusEnum $old_status,
//        public string|null $comment,
    )
    {
        $this->cardApplication = CardApplication::find($this->cardApplicationUpdate->card_application_id);
        $this->cardApplication_id = $this->cardApplication->id;
        $this->expiration_date = $this->cardApplication->expiration_date->toDateString();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('cardApplication.' . $this->cardApplication_id),
            //            new PrivateChannel('academic.' . $this->cardApplication->academic_id),
            //            new PresenceChannel('cardChecking.' . $this->old_status->valueWithUnderscores()),
            //            new PresenceChannel('cardChecking.' . $this->status->valueWithUnderscores())
        ];
    }

}
