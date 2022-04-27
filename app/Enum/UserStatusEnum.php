<?php

namespace App\Enum;

use App\Traits\EnumToArray;

enum UserStatusEnum: string
{
    use EnumToArray;

    case UNDERGRADUATE = 'undergraduate';
    case POSTGRADUATE = 'postgraduate';
    case PHD = 'phd';
    case ERASMUS = 'erasmus';
    case RESEARCHER = 'researcher';
    case STAFF_COUPON = 'staff coupon';
    case STAFF_CARD = 'staff card application';
    case STAFF_ENTRY = 'staff entry';

    public function role(): UserRoleEnum
    {
        return match ($this) {
            UserStatusEnum::STAFF_CARD => UserRoleEnum::STAFF_CARD,
            UserStatusEnum::STAFF_COUPON => UserRoleEnum::STAFF_COUPON,
            UserStatusEnum::STAFF_ENTRY => UserRoleEnum::STAFF_ENTRY,
            UserStatusEnum::UNDERGRADUATE,
            UserStatusEnum::POSTGRADUATE,
            UserStatusEnum::PHD,
            UserStatusEnum::ERASMUS => UserRoleEnum::STUDENT,
            UserStatusEnum::RESEARCHER => UserRoleEnum::RESEARCHER,
        };
    }


}
