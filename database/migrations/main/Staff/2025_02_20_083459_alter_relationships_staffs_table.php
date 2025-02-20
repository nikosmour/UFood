<?php

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
        Schema::table('card_application_update', function (Blueprint $table) {
            $table->dropForeign('main_card_application_checking_card_application_staff_id_foreign');
            $table->foreign('card_application_staff_id')->references('id')->on('card_application_staff')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dropForeign('main_card_application_checking_card_application_id_foreign');
            $table->foreign('card_application_id')->references('id')->on('card_applications')->cascadeOnDelete()->cascadeOnUpdate();
        });

        $this->fixIds('entry_staff', 'usage_coupons');
        $this->fixIds('entry_staff', 'usage_cards');
        $this->fixIds('coupon_staff', 'purchase_coupons');
        $this->fixIds('card_application_staff', 'card_application_update');
        Schema::dropIfExists('entry_staff');
        Schema::dropIfExists('coupon_staff');
        Schema::dropIfExists('card_application_staff');


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }

    private function fixIds($staffTable, $relationTable)
    {
        $foreignKey = $staffTable . '_id';
        $results = DB::table($staffTable)
            ->join('staffs', $staffTable . '.email', '=', 'staffs.email')
            ->select($staffTable . '.id as old_staff_id', 'staffs.id as staff_id')
            ->cursor();
//    Schema::table('card_application_update',function (Blueprint $table) {
//        $table->dropForeign('main_card_application_checking_card_application_staff_id_foreign');
//    });
        Schema::table($relationTable, function (Blueprint $table) use ($foreignKey) {
            $table->dropForeign([$foreignKey]);
        });
        foreach ($results as $result) {
            DB::table($relationTable)->where($foreignKey, $result->old_staff_id)->update([$foreignKey => $result->staff_id]);
        }
        Schema::table($relationTable, function (Blueprint $table) use ($foreignKey) {
            $table->foreign($foreignKey)->references('id')->on('staffs')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }
};
