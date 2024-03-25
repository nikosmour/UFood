<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperCardApplicationChecking
 */
class CardApplicationChecking extends Pivot
{
    use HasFactory;
    public $timestamps = false;

    public function cardApplicationStaff(): BelongsTo
    {
        return $this->belongsTo(CardApplicationStaff::class);
    }
    public function cardApplication(): BelongsTo
    {
        return $this->belongsTo(CardApplication::class);
    }
}
