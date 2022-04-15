<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardApplication extends Model
{
    use HasFactory;
    public function cardApplicant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CardApplicant::class,'academic_id');
    }
    public function cardApplicationDocument(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CardApplicationDocument::class);
    }
    public function cardApplicationStaff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CardApplicationStaff::class);
    }
}
