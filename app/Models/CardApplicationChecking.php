<?php

namespace App\Models;

/**
 * @mixin IdeHelperCardApplicationChecking
 */
class CardApplicationChecking extends CardApplicationUpdate
{
    protected static function booted(): void
    {
        static::addGlobalScope('ApplicantComments', function ($builder) {
            $builder->whereNot('card_application_staff_id', null);
        });
        static::creating(function ($model) {
            $model->card_application_staff_id = auth()->id(); // Set to the authenticated user's ID
        });
    }
}
