<?php

namespace App\Enum;

use App\Interfaces\Ability;
use App\Interfaces\Enum;
use App\Models\Academic;
use App\Models\CardApplicationStaff;
use App\Models\CouponStaff;
use App\Models\EntryStaff;
use App\Traits\Enums\EnumTrait;

enum UserAbilityEnum: string implements Enum, Ability
{
    use EnumTrait;

    case COUPON_OWNERSHIP = 'coupon ownership';
    case COUPON_SELL = 'coupon sell';
    case CARD_APPLICATION_CHECK = 'card application check';
    case CARD_OWNERSHIP = 'card ownership';
    case ENTRY_CHECK = 'entry check';


    public function UserClass(): string
    {
        return match ($this) {
            self::COUPON_OWNERSHIP, self::CARD_OWNERSHIP => Academic::class,
            self::COUPON_SELL => CouponStaff::class,
            self::CARD_APPLICATION_CHECK => CardApplicationStaff::class,
            self::ENTRY_CHECK => EntryStaff::class,
        };
    }
}
