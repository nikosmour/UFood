<?php

namespace App\Broadcasting;

use App\Models\Academic;
use App\Models\CardApplication;

class CardApplicationChannel
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
    public function join(Academic $user, CardApplication $cardApplication): array|bool
    {
        return $user->academic_id === $cardApplication->academic_id;
    }
}
