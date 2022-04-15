<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardApplicationDocument extends Model
{
    use HasFactory;
    public function cardApplication(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CardApplication::class);
    }
}
