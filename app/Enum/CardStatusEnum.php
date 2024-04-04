<?php

namespace App\Enum;

use App\Http\Controllers\CardApplicationController;
use App\Interfaces\Enum;
use App\Traits\Enums\EnumTrait;

enum CardStatusEnum: string implements Enum
{
    use EnumTrait;

    case TEMPORARY_SAVED = 'temporary saved';
    case SUBMITTED = 'submitted';
    case CHECKING = 'checking';
    case TEMPORARY_CHECKED = 'temporary checked';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case INCOMPLETE = 'incomplete';

    /**
     * Check if the applicant can update the card
     * @param CardStatusEnum $statusEnum
     * @return bool
     */
    public function canBeUpdated(): bool
    {
        return match ($this) {
            CardStatusEnum::TEMPORARY_SAVED,
            CardStatusEnum::INCOMPLETE,
            CardStatusEnum::SUBMITTED => true,
            CardStatusEnum::CHECKING,
            CardStatusEnum::ACCEPTED,
            CardStatusEnum::REJECTED,
            CardStatusEnum::TEMPORARY_CHECKED => false
        };
    }
}
