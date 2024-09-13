<?php

namespace Database\Seeders;

use App\Models\CouponOwner;
use App\Models\TransferCoupon;
use Database\Seeders\Classes\ManyToManySeeder;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;

class TransferCouponSeeder extends ManyToManySeeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $couponOwners = CouponOwner::where('created_at', '>', $this->createdAtMoreThan)->get();
        $this->make_connection($couponOwners, $couponOwners, TransferCoupon::class, $this->count);

    }

    protected function define_connection(Model $item1, Model $item2, string $connection): void
    {
        try {
            $connection::firstOrCreate(
                $connection::factory()->
                for($item1, 'Sender')->
                for($item2, 'Receiver')->
                make()->
                toArray()
            );
        } catch (Exception $e) {
            if ($e->errorInfo[1] != 1062)
                echo 'Seeder encountered an error: ' . $e->getMessage() . PHP_EOL;
        }
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
