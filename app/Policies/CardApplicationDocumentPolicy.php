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
    public function before(User $user, string $ability, $document, ?CardApplication $cardApplication = null): bool|null
    {
        if ($document instanceof CardApplicationDocument) {
            $cardApplication = $document->cardApplication;
        }
        $applicationAbility = match ($ability) {
            'viewAny' => 'viewAny',
            'view' => 'view',
            'create', 'update', 'delete' => 'update',
        };
        $ifUser = $user instanceof CardApplicationStaff ? true : null;
        return ($user->can($applicationAbility, $cardApplication)) ? $ifUser : false;
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
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @param CardApplication $cardApplication
     * @return Response|bool
     */
    public function create(User $user, CardApplication $cardApplication): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param CardApplicationDocument $cardApplicationDocument
     * @return Response|bool
     */
    public function update(User $user, CardApplicationDocument $cardApplicationDocument)
    {
        return $cardApplicationDocument->status->canBeUpdated();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param CardApplicationDocument $cardApplicationDocument
     * @return Response|bool
     */
    public function delete(User $user, CardApplicationDocument $cardApplicationDocument): Response|bool
    {
        return $cardApplicationDocument->status->canBeDeleted();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return Response|bool
     */
    public function restore(User $user): Response|bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return Response|bool
     */
    public function forceDelete(User $user): Response|bool
    {
        return false;
    }
}
