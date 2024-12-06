<?php

namespace App\Policies;

use App\Models\Academic;
use App\Models\CardApplication;
use App\Models\CardApplicationStaff;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CardApplicationPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability, $cardApplicationOrClass): bool|null
    {
        // if user is cardApplicant
        // if  there is a cardApplication must be the same with the cardApplicant. if that is true check in the  function if it is more restrict

        if ($user instanceof Academic && $user->cardApplicant()->exists()) {
            /*return ($ability != 'viewAny' && $ability != 'create' && $user->academic_id != $cardApplicationOrClass->academic_id)
                ? false : null;*/
            return ($cardApplicationOrClass instanceof CardApplication && $user->academic_id != $cardApplicationOrClass->academic_id)
                ? false : null;

        }
        //if the user is not cardApplicant must be cardApplicationStaffs
        return $user instanceof CardApplicationStaff && ($ability === 'view' || $ability === 'update');
    }


    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(Academic $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param CardApplication $cardApplication
     * @return Response|bool
     */
    public function view(User $user, CardApplication $cardApplication): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param Academic $user
     * @return Response|bool
     */
    public function create(Academic $user): Response|bool
    {
        return !$user->cardApplicant->currentCardApplication()->exists();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param CardApplication $cardApplication
     * @return Response|bool
     */
    public function edit(User $user, CardApplication $cardApplication): Response|bool
    {
        return $cardApplication->cardLastUpdate->status->canBeEdited($user instanceof Academic) || $user->id === $cardApplication->cardLastUpdate->card_application_staff_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param CardApplication $cardApplication
     * @return Response|bool
     */
    public function update(User $user, CardApplication $cardApplication): Response|bool
    {
        return $cardApplication->cardLastUpdate->status->canBeUpdated($user instanceof Academic) || $user->id === $cardApplication->cardLastUpdate->card_application_staff_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param CardApplication $cardApplication
     * @return Response|bool
     */
    public function delete(User $user, CardApplication $cardApplication): Response|bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param CardApplication $cardApplication
     * @return Response|bool
     */
    public function restore(User $user, CardApplication $cardApplication): Response|bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param CardApplication $cardApplication
     * @return Response|bool
     */
    public function forceDelete(User $user, CardApplication $cardApplication): Response|bool
    {
        return false;
    }
}
