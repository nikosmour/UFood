<?php

namespace Database\Seeders\Classes;

use Carbon\Carbon;
use Database\Seeders\Traits\ManyToManyConnection;
use Illuminate\Database\Seeder;

abstract class ManyToManySeeder extends CreatedAtMoreThanSeeder
{
    use ManyToManyConnection;

    public function __construct($createdAtMoreThan = '01-01-1900', protected int $count = 5)
    {
        parent::__construct($createdAtMoreThan);
    }
}
