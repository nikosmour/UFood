<?php

namespace App\Policies;

use App\Enum\UserAbilityEnum;
use App\Models\TransferCoupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TransferCouponPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param User $user
     * @return void|bool
     */
    public function before(User $user)
    {
        if ($user->hasAbility(UserAbilityEnum::COUPON_OWNERSHIP)) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */

    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param TransferCoupon $transferCoupon
     * @return Response|bool
     */
    public function view(User $user, TransferCoupon $transferCoupon)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param TransferCoupon $transferCoupon
     * @return Response|bool
     */
    public function update(User $user, TransferCoupon $transferCoupon)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param TransferCoupon $transferCoupon
     * @return Response|bool
     */
    public function delete(User $user, TransferCoupon $transferCoupon)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param TransferCoupon $transferCoupon
     * @return Response|bool
     */
    public function restore(User $user, TransferCoupon $transferCoupon)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param TransferCoupon $transferCoupon
     * @return Response|bool
     */
    public function forceDelete(User $user, TransferCoupon $transferCoupon)
    {
        //
    }
}
