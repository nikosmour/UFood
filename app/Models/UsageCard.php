<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageCard extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = ['date','academic_id','type'];

    public function cardApplicant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CardApplicant::class,'academic_id');
    }
    public function entryStaff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EntryStaff::class);
    }
}
