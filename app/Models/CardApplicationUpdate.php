<?php

namespace App\Models;

use App\Enum\CardStatusEnum;
use App\Observers\CardApplicationUpdateObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperCardApplicationUpdate
 */
#[ObservedBy([CardApplicationUpdateObserver::class])]
class CardApplicationUpdate extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'card_application_update';
    protected $casts = [
        'status' => CardStatusEnum::class,
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(
        /**
         * define default card_application_staff_id when model is created and
         * @param $model
         * @return void
         */
            function ($model) {
                $user = auth()->user();
                if ($model->isClean('card_application_staff_id') && $user instanceof CardApplicationStaff)
                    $model->card_application_staff_id = $user->id;
            });
    }


    public function cardApplicationStaff(): BelongsTo
    {
        return $this->belongsTo(CardApplicationStaff::class);
    }

    public function cardApplication(): BelongsTo
    {
        return $this->belongsTo(CardApplication::class);
    }

    public function Academic(): BelongsTo
    {
        return $this->cardApplication->academic();
    }
}
