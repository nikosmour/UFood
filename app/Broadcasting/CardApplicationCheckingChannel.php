<?php

namespace App\Broadcasting;

use App\Enum\CardStatusEnum;
use App\Models\Academic;
use App\Models\CardApplicationStaff;

class CardApplicationCheckingChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(CardApplicationStaff $user): array|bool
    {
        return ['name' => $user->name];
    }
}
