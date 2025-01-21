<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{

    public function __construct()
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (__('departments', locale: 'el') as $department)
            Department::factory()->make(['name' => $department])->save();
    }
}
