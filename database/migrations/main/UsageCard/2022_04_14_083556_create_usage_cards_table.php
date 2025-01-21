<?php

use App\Enum\MealPlanPeriodEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usage_cards', function (Blueprint $table) {
            $table->date('date');
            $table->unsignedBigInteger('academic_id');
            $table->enum('period', MealPlanPeriodEnum::values()->toArray());
            $table->time('time');
            $table->unsignedTinyInteger('entry_staff_id');
            $table->foreign('academic_id')->references('academic_id')->on('card_applicants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('entry_staff_id')->references('id')->on('entry_staff')->onDelete('cascade')->onUpdate('cascade');
            $table->primary(['date', 'academic_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usage_cards');
    }
};
