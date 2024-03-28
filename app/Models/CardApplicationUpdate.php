<?php

namespace App\Models;

use App\Enum\CardStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;


/**
 * @mixin IdeHelperCardApplicationUpdate
 */
class CardApplicationUpdate extends Pivot
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'card_application_update';
    protected $casts = [
        'status' => CardStatusEnum::class,
    ];


    public function cardApplicationStaff(): BelongsTo
    {
        return $this->belongsTo(CardApplicationStaff::class);
    }

    public function cardApplication(): BelongsTo
    {
        return $this->belongsTo(CardApplication::class);
    }
}
