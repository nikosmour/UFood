<?php

namespace Database\Seeders\Classes;

use Database\Seeders\Traits\ManyToManyConnection;
use Illuminate\Database\Seeder;

abstract class ManyToManySeeder extends Seeder
{
    use ManyToManyConnection;
}
