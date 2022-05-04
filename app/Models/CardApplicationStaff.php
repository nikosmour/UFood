<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCardApplicationStaff
 */
class CardApplicationStaff extends Model
{
    use HasFactory;
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function cardApplication(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CardApplication::class);
    }
}
