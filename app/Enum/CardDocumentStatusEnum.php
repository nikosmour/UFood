<?php

namespace App\Enum;

use App\Traits\EnumToArray;

enum CardDocumentStatusEnum: string
{
    use EnumToArray;

    case SUBMITTED = 'submitted';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case INCOMPLETE = 'incomplete';
}
