<?php

namespace Database\Seeders\Traits;

use Illuminate\Support\Facades\Schema;

trait ReorderRowsTrait
{
    protected function reorderRows(string $model, string|array $direction)
    {
        $id = $model::max('id') ?? 0;
        $columns = array_diff(
            Schema::getColumnListing((new $model)->getTable()),
            ['id']
        );
        $model::insertUsing($columns, $model::select($columns)->where('id', '<=', $id)->orderBy($direction));
        $model::where('id', '<=', $id)->delete();
    }

}
