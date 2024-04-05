<?php

namespace App\Policies;

use App\Models\Academic;
use App\Models\CardApplication;
use App\Models\CardApplicationDocument;
use App\Models\CardApplicationStaff;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class CardApplicationDocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability, $document = null, CardApplication $cardApplication): bool|null
    {
//        return true;
//        dump("User $user \n attempting $ability on \n $document other \n $cardApplication");
        $applicationAbility = match ($ability) {
            'view', 'viewAny' => 'view',
            'create', 'update', 'delete' => 'update',
        };
        return ($user->can($applicationAbility, $cardApplication)) ? null : false;
    }
    /**
     * Determine whether the user can view any models.
     * @param User $user
     * @param CardApplication $cardApplication
     * @return Response|bool
     */
    public function viewAny(User $user, CardApplication $cardApplication): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\CardApplication $cardApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CardApplication $cardApplication)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, CardApplication $cardApplication): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\CardApplication $cardApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CardApplicationDocument $cardApplicationDocument, CardApplication $cardApplication)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\CardApplication $cardApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CardApplicationDocument $cardApplicationDocument, CardApplication $cardApplication): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\CardApplication $cardApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CardApplication $cardApplication): Response|bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\CardApplication $cardApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CardApplication $cardApplication): Response|bool
    {
        return false;
    }



//    /**
//     * Determine whether the user can view the model.
//     *
//     * @param  \App\Models\User $user
//     * @param  \App\Models\CardApplicationDocument  $cardApplicationDocument
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function view(User $user, CardApplicationDocument $cardApplicationDocument)
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can create models.
//     *
//     * @param  \App\Models\User $user
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function create(User $user)
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can update the model.
//     *
//     * @param  \App\Models\User $user
//     * @param  \App\Models\CardApplicationDocument  $cardApplicationDocument
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function update(User $user, CardApplicationDocument $cardApplicationDocument)
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can delete the model.
//     *
//     * @param  \App\Models\User $user
//     * @param  \App\Models\CardApplicationDocument  $cardApplicationDocument
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function delete(User $user, CardApplicationDocument $cardApplicationDocument)
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can restore the model.
//     *
//     * @param  \App\Models\User $user
//     * @param  \App\Models\CardApplicationDocument  $cardApplicationDocument
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function restore(User $user, CardApplicationDocument $cardApplicationDocument)
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can permanently delete the model.
//     *
//     * @param  \App\Models\User $user
//     * @param  \App\Models\CardApplicationDocument  $cardApplicationDocument
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function forceDelete(User $user, CardApplicationDocument $cardApplicationDocument)
//    {
//        //
//    }
}
