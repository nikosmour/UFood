<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryStaff extends Model
{
    use HasFactory;
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function usageCoupon(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UsageCoupon::class);
    }
    public function usageCard(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UsageCard::class);
    }
}
