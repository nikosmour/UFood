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
        Schema::create('meal_meal_plan', function (Blueprint $table) {
            $table->unsignedBigInteger('meal_plan_id');
            $table->unsignedSmallInteger('meal_id');
            $table->foreign('meal_plan_id')->references('id')->on('meal_plans')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meal_meal_plan');
    }
};
