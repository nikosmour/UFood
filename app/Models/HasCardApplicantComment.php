<?php

namespace App\Models;

/**
 * @mixin IdeHelperHasCardApplicantComment
 */
class HasCardApplicantComment extends CardApplicationUpdate
{
    protected $attributes = [
        'card_application_staff_id' => null,
    ];
    protected static function booted(): void
    {
        static::addGlobalScope('ApplicantComments', function ($builder) {
            $builder->whereNull('card_application_staff_id');
        });
    }
}
