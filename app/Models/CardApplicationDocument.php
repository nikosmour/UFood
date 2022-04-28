<?php

namespace App\Models;

use App\Enum\CardDocumentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardApplicationDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     * @var string[]
     */
    protected $casts = [
        'status' => CardDocumentStatusEnum::class,
    ];

    public function cardApplication(): BelongsTo
    {
        return $this->belongsTo(CardApplication::class);
    }
}
