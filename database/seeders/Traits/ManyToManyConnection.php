<?php

namespace Database\Seeders\Traits;

use Illuminate\Database\Eloquent\Model;

trait ManyToManyConnection
{
    /**
     * Make many to Many Connection
     *
     * @param $collection1
     * @param $collection2
     * @param $connection
     * @param int $count
     * @return void
     */
    protected function make_connection($collection1, $collection2, $connection, $count = 1, $withCreatedAt = false): void
    {
        foreach ($collection1 as $item)
            for ($i = $count; $i > 0; $i--)
                $this->define_connection($item, $collection2->random(), $connection, $withCreatedAt);
    }

    /**
     * Generate one instance of the connection
     *
     */
    protected function define_connection(Model $item1, Model $item2, string $connection, bool $withCreatedAt): void
    {
        $temp = $connection::factory();
        if ($withCreatedAt)
            $temp = $temp->createdAt(max([
                $item1->created_at,
                $item2->created_at
            ]));
        $connection::firstOrCreate($temp->for(
            $item1)->for($item2)->make()->toArray());
    }

}
