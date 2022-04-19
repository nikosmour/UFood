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
        Schema::create('usage_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_id');
            $table->unsignedTinyInteger('entry_staff_id');
            $table->enum('status',config('constants.meal.plan.period'));
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('academic_id')->references('academic_id')->on('coupon_owners')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('entry_staff_id')->references('id')->on('entry_staff')->onDelete('cascade')->onUpdate('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usage_coupons');
    }
};
