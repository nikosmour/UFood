<?php

namespace Database\Seeders;

use App\Models\CouponOwner;
use App\Models\EntryStaff;
use App\Models\UsageCoupon;
use Database\Seeders\Classes\ManyToManySeeder;
use Database\Seeders\Traits\ReorderRowsTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsageCouponSeeder extends ManyToManySeeder
{
    use ReorderRowsTrait;
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->make_connection(
            CouponOwner::whereDoesntHave('usageCoupon')->cursor(),
            EntryStaff::all(),
            UsageCoupon::class, $this->count, true);
        $this->reorderRows(UsageCoupon::class, 'created_at');

    }
}
