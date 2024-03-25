<?php

use App\Models\CardApplication;
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
        Schema::table('has_card_applicant_comments', function (Blueprint $table) {
            $table->renameColumn('card_applicant_comment', 'comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('has_card_applicant_comments', function (Blueprint $table) {
            $table->renameColumn('comment', 'card_applicant_comment');
        });
    }
};
