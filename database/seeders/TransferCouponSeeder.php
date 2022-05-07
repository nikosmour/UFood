<?php

namespace Database\Seeders;

use App\Models\TransferCoupon;
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
            for($i=$sending_length;$i>0;$i--)
                TransferCoupon::factory()->for($receivers[$i],'receiver')->for($sender,'sender')->create();
        }
    }
}
