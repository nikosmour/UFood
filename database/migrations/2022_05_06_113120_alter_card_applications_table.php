<?php

use App\Models\CardApplicationStaff;
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
        Schema::table('card_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(CardApplicationStaff::class);
            $table->dropColumn(['student_comments',
                'card_application_staff_comments']);
            $table->date('expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_applications', function (Blueprint $table) {
            //
        });
    }
};
