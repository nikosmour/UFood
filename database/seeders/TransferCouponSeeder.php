<?php

namespace Database\Seeders;

use App\Models\TransferCoupon;
use Database\Seeders\Classes\CreatedAtMoreThanSeeder;
use Database\Seeders\Classes\ManyToManySeeder;
use Illuminate\Database\Seeder;

class TransferCouponSeeder extends ManyToManySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $couponOwners = \App\Models\CouponOwner::where('created_at', '>', $this->createdAtMoreThan)->get();
        $this->make_connection($couponOwners, $couponOwners, \App\Models\TransferCoupon::class, $this->count);
    }
    /*public function run()
    {
        $sending_length = 5;
        $couponOwners = \App\Models\CouponOwner::where('created_at','>',$this->createdAtMoreThan)->cursor();
//        $this->make_connection($couponOwners,$couponOwners,\App\Models\TransferCoupon::class);
        foreach ($couponOwners as $sender) {
            $receivers = \App\Models\CouponOwner::inRandomOrder()->limit($this->sending_length)->cursor();
            foreach($receivers as $receiver)
                TransferCoupon::factory()->for($receiver, 'receiver')->for($sender, 'sender')->create();
        }
    }*/
}
