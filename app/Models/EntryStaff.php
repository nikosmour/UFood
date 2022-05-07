<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperEntryStaff
 */
class EntryStaff extends User
{
    use HasFactory;

    public function usageCoupon(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UsageCoupon::class);
    }

    public function usageCard(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UsageCard::class);
    }
}
