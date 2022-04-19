<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransferCouponSeeder extends Seeder
{
//    use ManyToManyConnection;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sending_length=5;
        $couponOwners=\App\Models\CouponOwner::all();
//        $this->make_connection($couponOwners,$couponOwners,\App\Models\TransferCoupon::class);
        foreach ( $couponOwners as $sender){
            $receivers=$couponOwners->shuffle();
            $sender->sendingCoupon()->savemany($receivers->slice(0,$sending_length));
        }
    }
}
