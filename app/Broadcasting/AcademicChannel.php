<?php

namespace App\Broadcasting;

use App\Models\Academic;

class AcademicChannel
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
    public function join(Academic $user, int $Academic): array|bool
    {
        return $user->academic_id === $Academic;
    }
}
