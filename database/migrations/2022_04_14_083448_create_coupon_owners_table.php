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
        Schema::create('coupon_owners', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_id')->primary();
            $table->foreign('academic_id')->references('academic_id')->on('academic_citizens')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('money')->unsigned()->default(0);
            $table->integer('breakfast')->unsigned()->default(0);
            $table->integer('lunch')->unsigned()->default(0);
            $table->integer('dinner')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_owners');
    }
};
