<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->tinyInteger('breakfast')->unsigned()->default(0);
            $table->tinyInteger('lunch')->unsigned()->default(0);
            $table->tinyInteger('dinner')->unsigned()->default(0);
            $table->timestamp('created_at');
            $table->foreign('sender_id')->references('academic_id')->on('coupon_owners')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('receiver_id')->references('academic_id')->on('coupon_owners')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['created_at','sender_id','receiver_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_coupons');
    }
};
