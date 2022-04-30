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
        Schema::create('purchase_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_id');
            $table->unsignedTinyInteger('coupon_staff_id');
            $table->unsignedInteger('money')->default(0);
            foreach (MealPlanPeriodEnum::names() as $period) {
                $table->unsignedTinyInteger($period)->default(0);
            }
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('academic_id')->references('academic_id')->on('coupon_owners')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('coupon_staff_id')->references('id')->on('coupon_staff')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_coupons');
    }
};
