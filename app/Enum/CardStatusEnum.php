<?php

namespace App\Enum;

use App\Traits\EnumToArray;

enum CardStatusEnum: string
{
    use EnumToArray;

    case TEMPORARY_SAVED = 'temporary saved';
    case SUBMITTED = 'submitted';
    case CHECKING = 'checking';
    case TEMPORARY_CHECKED = 'temporary checked';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case INCOMPLETE = 'incomplete';
}
