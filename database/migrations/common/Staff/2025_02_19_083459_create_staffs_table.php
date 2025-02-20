<?php

use App\Enum\UserStatusEnum;
use App\Models\Staff;
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
        Schema::create('staffs', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name');
            $table->string('father_name')->default('George');
            $table->string('email')->unique();
//            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('status', [
                UserStatusEnum::STAFF_CARD->value,
                UserStatusEnum::STAFF_ENTRY->value,
                UserStatusEnum::STAFF_COUPON->value,
            ]);
//            $table->rememberToken();
            $table->timestamps();
        });
        $columns = array_diff(Schema::getColumnListing((new Staff())->getTable()), ['id']);
        $query = DB::table('coupon_staff')->select($columns);
        $query = $query->union(DB::table('entry_staff')->select($columns));
        $query = $query->union(DB::table('card_application_staff')->select($columns));
        Staff::insertUsing($columns, $query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staffs');
    }
};
