<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait EnumToArray
{

    public static function toArray(): array
    {
        return array_combine(self::names()->toArray(), self::values()->toArray());
    }

    public static function names(): Collection
    {
        return collect(array_column(self::cases(), 'name'));
    }

    public static function values(): Collection
    {
        return collect(array_column(self::cases(), 'value'));
    }

    public static function toInverseArray(): array
    {
        return array_combine(self::values()->toArray(), self::names()->toArray());
    }
}
