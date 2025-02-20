<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperEntryStaff
 */
class EntryStaff extends Staff
{
    public function usageCoupon(): HasMany
    {
        return $this->hasMany(UsageCoupon::class);
    }

    public function usageCard(): HasMany
    {
        return $this->hasMany(UsageCard::class);
    }

    public function takeStatistics($vData)
    {
        return UsageCoupon::takeStatistics($vData)
            ->union(UsageCard::takeStatistics($vData))
            ->orderBy('date', 'desc');
    }
}
