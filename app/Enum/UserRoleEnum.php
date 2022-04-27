<?php

namespace App\Enum;

use App\Traits\EnumToArray;

enum UserRoleEnum: string
{
    use EnumToArray;

    case STUDENT = 'student';
    case RESEARCHER = 'researcher';
    case STAFF_COUPON = 'staff coupon';
    case STAFF_CARD = 'staff card application';
    case STAFF_ENTRY = 'staff entry';
}
