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

    /**
     * determine if the user has the specific $role
     * @param UserRoleEnum $role
     * @return bool
     */
    public function hasRole(UserRoleEnum $role): bool
    {
        return $this->hasAnyRole([$role]);
    }

    /**
     * determine if the user has any of the specific $roles
     * @param array $roles
     * array of UserRoleEnum
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role(), $roles);
    }

    /**
     * @return UserRoleEnum
     */
    private function role(): UserRoleEnum
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

    /**
     * determine if the user status has the specific $ability
     * @param UserAbilityEnum $ability
     * @return bool
     */
    public function hasAbility(UserAbilityEnum $ability): bool
    {
        return $this->hasAnyRole($ability->whoHas());
    }


}
